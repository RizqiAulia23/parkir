<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\Tarif;
use App\Models\Transaksi;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransaksiController extends Controller
{
    public function index(): View
    {
        $aktif = Transaksi::with(['kendaraan', 'area', 'tarif', 'user'])
            ->masuk()
            ->orderByDesc('waktu_masuk')
            ->paginate(8, ['*'], 'aktif')
            ->withQueryString();

        $riwayat = Transaksi::with(['kendaraan', 'area', 'tarif', 'user'])
            ->keluar()
            ->orderByDesc('waktu_keluar')
            ->paginate(10, ['*'], 'riwayat')
            ->withQueryString();

        $totalKapasitas = (int) AreaParkir::sum('kapasitas');
        $totalTerisi = (int) AreaParkir::sum('terisi');
        $selesaiHariIni = Transaksi::keluar()
            ->whereBetween('waktu_keluar', [now()->startOfDay(), now()->endOfDay()])
            ->count();
        $pendapatanHariIni = (float) Transaksi::keluar()
            ->whereBetween('waktu_keluar', [now()->startOfDay(), now()->endOfDay()])
            ->sum('biaya_total');

        $transaksiAktif = Transaksi::masuk()->count();

        return view('petugas.transaksi.index', compact(
            'aktif',
            'riwayat',
            'totalKapasitas',
            'totalTerisi',
            'selesaiHariIni',
            'pendapatanHariIni',
            'transaksiAktif'
        ));
    }

    public function create(): View
    {
        $kendaraanList = Kendaraan::query()
            ->whereDoesntHave('transaksi', fn ($query) => $query->where('status', 'masuk'))
            ->orderBy('plat_nomor')
            ->get();

        $areaList = AreaParkir::query()
            ->whereColumn('terisi', '<', 'kapasitas')
            ->orderBy('nama_area')
            ->get();

        $tarifList = Tarif::query()
            ->orderByRaw("CASE jenis_kendaraan WHEN 'motor' THEN 1 WHEN 'mobil' THEN 2 ELSE 3 END")
            ->get();

        return view('petugas.transaksi.create', compact('kendaraanList', 'areaList', 'tarifList'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (blank($request->input('id_kendaraan')) && filled($request->input('plat_nomor'))) {
            $request->merge(['plat_nomor' => $this->normalisasiPlat($request->input('plat_nomor'))]);
        }

        $data = $request->validate([
            'id_kendaraan' => ['nullable', 'integer', 'exists:tb_kendaraan,id_kendaraan'],
            'plat_nomor' => ['nullable', 'required_without:id_kendaraan', 'string', 'max:15'],
            'jenis_kendaraan' => ['nullable', 'required_without:id_kendaraan', 'in:motor,mobil,lainnya'],
            'warna' => ['nullable', 'string', 'max:20'],
            'pemilik' => ['nullable', 'string', 'max:100'],
            'id_area' => ['required', 'integer', 'exists:tb_area_parkir,id_area'],
            'id_tarif' => ['required', 'integer', 'exists:tb_tarif,id_tarif'],
        ], [], [
            'id_kendaraan' => 'kendaraan',
            'plat_nomor' => 'plat nomor',
            'jenis_kendaraan' => 'jenis kendaraan',
            'id_area' => 'area parkir',
            'id_tarif' => 'tarif',
        ]);

        $transaksi = DB::transaction(function () use ($data) {
            $petugas = auth()->user();

            if (filled($data['id_kendaraan'] ?? null)) {
                $kendaraan = Kendaraan::whereKey($data['id_kendaraan'])->lockForUpdate()->firstOrFail();
            } else {
                $plat = $this->normalisasiPlat($data['plat_nomor']);

                $sudahTerdaftar = Kendaraan::query()
                    ->whereRaw('UPPER(plat_nomor) = ?', [$plat])
                    ->exists();

                if ($sudahTerdaftar) {
                    throw ValidationException::withMessages([
                        'plat_nomor' => 'Plat nomor sudah terdaftar. Pilih kendaraan tersebut dari daftar.',
                    ]);
                }

                $kendaraan = Kendaraan::create([
                    'plat_nomor' => $plat,
                    'jenis_kendaraan' => $data['jenis_kendaraan'],
                    'warna' => $data['warna'] ?? null,
                    'pemilik' => $data['pemilik'] ?? null,
                    'id_user' => null,
                ]);
            }

            $masihParkir = Transaksi::query()
                ->where('id_kendaraan', $kendaraan->id_kendaraan)
                ->where('status', 'masuk')
                ->exists();

            if ($masihParkir) {
                throw ValidationException::withMessages([
                    'id_kendaraan' => 'Kendaraan ini masih tercatat sedang parkir.',
                ]);
            }

            $area = AreaParkir::whereKey($data['id_area'])->lockForUpdate()->firstOrFail();

            if ($area->terisi >= $area->kapasitas) {
                throw ValidationException::withMessages([
                    'id_area' => 'Area parkir sudah penuh. Pilih area lain.',
                ]);
            }

            $tarif = Tarif::findOrFail($data['id_tarif']);

            $transaksi = Transaksi::create([
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'waktu_masuk' => now(),
                'waktu_keluar' => null,
                'id_tarif' => $tarif->id_tarif,
                'durasi_jam' => 0,
                'biaya_total' => 0,
                'status' => 'masuk',
                'id_user' => $petugas->id_user,
                'id_area' => $area->id_area,
            ]);

            $area->increment('terisi');

            LogAktivitas::catat(
                'Transaksi masuk: '.$kendaraan->plat_nomor.' - '.$area->nama_area,
                $petugas->id_user
            );

            return $transaksi;
        });

        return redirect()
            ->route('transaksi.struk', $transaksi)
            ->with('success', 'Transaksi parkir masuk berhasil. Tiket siap dicetak.');
    }

    public function keluarForm(Transaksi $transaksi): View|RedirectResponse
    {
        if (! $transaksi->isMasuk()) {
            return redirect()
                ->route('transaksi.index')
                ->with('error', 'Transaksi ini sudah selesai.');
        }

        $transaksi->load(['kendaraan', 'area', 'tarif', 'user']);

        $preview = $this->hitungBiaya($transaksi, now());

        return view('petugas.transaksi.keluar', compact('transaksi', 'preview'));
    }

    public function keluarStore(Transaksi $transaksi): RedirectResponse
    {
        DB::transaction(function () use ($transaksi) {
            $trx = Transaksi::whereKey($transaksi->id_parkir)->lockForUpdate()->firstOrFail();

            if (! $trx->isMasuk()) {
                throw ValidationException::withMessages([
                    'transaksi' => 'Transaksi ini sudah diproses keluar.',
                ]);
            }

            $trx->load(['kendaraan', 'area', 'tarif']);

            $hasil = $this->hitungBiaya($trx, now());

            $trx->update([
                'waktu_keluar' => $hasil['waktu_keluar'],
                'durasi_jam' => $hasil['durasi_jam'],
                'biaya_total' => $hasil['biaya_total'],
                'status' => 'keluar',
            ]);

            if ($trx->id_area) {
                AreaParkir::whereKey($trx->id_area)
                    ->where('terisi', '>', 0)
                    ->decrement('terisi');
            }

            LogAktivitas::catat(
                'Transaksi keluar: '.$trx->kendaraan->plat_nomor
                    .' - '.$hasil['durasi_jam'].' jam - Rp'.number_format($hasil['biaya_total'], 0, ',', '.'),
                auth()->id()
            );
        });

        return redirect()
            ->route('transaksi.struk', $transaksi)
            ->with('success', 'Transaksi parkir keluar berhasil diproses.');
    }

    public function struk(Transaksi $transaksi): View
    {
        $transaksi->load(['kendaraan', 'area', 'tarif', 'user']);

        return view('petugas.transaksi.struk', compact('transaksi'));
    }

    /**
     * @return array{waktu_keluar: CarbonInterface, durasi_jam: int, tarif_per_jam: float, biaya_total: float}
     */
    private function hitungBiaya(Transaksi $transaksi, CarbonInterface $waktuKeluar): array
    {
        $detik = max(0, $waktuKeluar->getTimestamp() - $transaksi->waktu_masuk->getTimestamp());
        $durasiJam = max(1, (int) ceil($detik / 3600));
        $tarifPerJam = (float) ($transaksi->tarif?->tarif_per_jam ?? 0);

        return [
            'waktu_keluar' => $waktuKeluar,
            'durasi_jam' => $durasiJam,
            'tarif_per_jam' => $tarifPerJam,
            'biaya_total' => $durasiJam * $tarifPerJam,
        ];
    }

    private function normalisasiPlat(?string $plat): string
    {
        return strtoupper(trim(preg_replace('/\s+/', ' ', (string) $plat)));
    }
}
