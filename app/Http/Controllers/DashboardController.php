<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $awalHari = now()->startOfDay();
        $akhirHari = now()->endOfDay();

        $transaksiAktif = Transaksi::masuk()->count();
        $selesaiHariIni = Transaksi::keluar()
            ->whereBetween('waktu_keluar', [$awalHari, $akhirHari])
            ->count();
        $pendapatanHariIni = (float) Transaksi::keluar()
            ->whereBetween('waktu_keluar', [$awalHari, $akhirHari])
            ->sum('biaya_total');

        $cards = [];
        $transaksiAktifList = collect();
        $logTerbaru = collect();
        $areaList = collect();
        $transaksiTerakhir = collect();

        if ($user->isAdmin()) {
            $cards = [
                ['label' => 'Total Pengguna', 'value' => User::count(), 'hint' => 'Admin, petugas, owner'],
                ['label' => 'Kendaraan Terdaftar', 'value' => Kendaraan::count(), 'hint' => 'Karyawan & tamu'],
                ['label' => 'Area Parkir', 'value' => AreaParkir::count(), 'hint' => 'Lokasi aktif'],
                ['label' => 'Sedang Parkir', 'value' => $transaksiAktif, 'hint' => 'Kendaraan di dalam area'],
            ];

            $logTerbaru = LogAktivitas::with('user')
                ->orderByDesc('waktu_aktivitas')
                ->orderByDesc('id_log')
                ->limit(6)
                ->get();

            $areaList = AreaParkir::orderBy('nama_area')->get();
            $transaksiAktifList = Transaksi::with(['kendaraan', 'area'])
                ->masuk()
                ->orderByDesc('waktu_masuk')
                ->limit(5)
                ->get();
        } elseif ($user->isPetugas()) {
            $kapasitas = (int) AreaParkir::sum('kapasitas');
            $terisi = (int) AreaParkir::sum('terisi');

            $cards = [
                ['label' => 'Sedang Parkir', 'value' => $transaksiAktif, 'hint' => 'Kendaraan di dalam area'],
                ['label' => 'Kapasitas Terisi', 'value' => $terisi.' / '.$kapasitas, 'hint' => 'Seluruh area'],
                ['label' => 'Selesai Hari Ini', 'value' => $selesaiHariIni, 'hint' => 'Transaksi keluar'],
                ['label' => 'Pendapatan Hari Ini', 'value' => 'Rp '.number_format($pendapatanHariIni, 0, ',', '.'), 'hint' => 'Dari transaksi selesai'],
            ];

            $transaksiAktifList = Transaksi::with(['kendaraan', 'area'])
                ->masuk()
                ->orderByDesc('waktu_masuk')
                ->limit(5)
                ->get();

            $areaList = AreaParkir::orderBy('nama_area')->get();
        } else {
            $awalBulan = now()->startOfMonth();
            $pendapatanBulanIni = (float) Transaksi::keluar()
                ->whereBetween('waktu_keluar', [$awalBulan, $akhirHari])
                ->sum('biaya_total');
            $selesaiBulanIni = Transaksi::keluar()
                ->whereBetween('waktu_keluar', [$awalBulan, $akhirHari])
                ->count();

            $cards = [
                ['label' => 'Pendapatan Hari Ini', 'value' => 'Rp '.number_format($pendapatanHariIni, 0, ',', '.'), 'hint' => 'Transaksi selesai'],
                ['label' => 'Pendapatan Bulan Ini', 'value' => 'Rp '.number_format($pendapatanBulanIni, 0, ',', '.'), 'hint' => now()->locale('id')->translatedFormat('F Y')],
                ['label' => 'Transaksi Bulan Ini', 'value' => $selesaiBulanIni, 'hint' => 'Kendaraan keluar'],
                ['label' => 'Rata-rata per Transaksi', 'value' => 'Rp '.number_format($selesaiBulanIni > 0 ? $pendapatanBulanIni / $selesaiBulanIni : 0, 0, ',', '.'), 'hint' => 'Bulan berjalan'],
            ];

            $transaksiTerakhir = Transaksi::with(['kendaraan', 'area', 'tarif'])
                ->keluar()
                ->orderByDesc('waktu_keluar')
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact(
            'user',
            'cards',
            'transaksiAktifList',
            'logTerbaru',
            'areaList',
            'transaksiTerakhir',
            'transaksiAktif',
            'selesaiHariIni',
            'pendapatanHariIni'
        ));
    }
}
