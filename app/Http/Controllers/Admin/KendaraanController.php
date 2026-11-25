<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KendaraanController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));

        $kendaraan = Kendaraan::query()
            ->with('user')
            ->withExists(['transaksi as sedang_parkir' => fn ($query) => $query->where('status', 'masuk')])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('plat_nomor', 'ilike', '%'.$search.'%')
                        ->orWhere('pemilik', 'ilike', '%'.$search.'%');
                });
            })
            ->orderBy('plat_nomor')
            ->paginate(10)
            ->withQueryString();

        return view('admin.kendaraan.index', compact('kendaraan', 'search'));
    }

    public function create(): View
    {
        $users = User::orderBy('nama_lengkap')->get();

        return view('admin.kendaraan.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['plat_nomor' => $this->normalisasiPlat($request->input('plat_nomor'))]);

        $data = $request->validate($this->rules(), [], $this->attributes());

        $data['id_user'] = ($data['id_user'] ?? null) ?: null;

        $kendaraan = Kendaraan::create($data);

        LogAktivitas::catat('Tambah kendaraan: '.$kendaraan->plat_nomor.' ('.$kendaraan->jenis_kendaraan.')');

        return redirect()
            ->route('admin.kendaraan.index')
            ->with('success', 'Kendaraan '.$kendaraan->plat_nomor.' berhasil ditambahkan.');
    }

    public function edit(Kendaraan $kendaraan): View
    {
        $users = User::orderBy('nama_lengkap')->get();

        return view('admin.kendaraan.edit', compact('kendaraan', 'users'));
    }

    public function update(Request $request, Kendaraan $kendaraan): RedirectResponse
    {
        $request->merge(['plat_nomor' => $this->normalisasiPlat($request->input('plat_nomor'))]);

        $data = $request->validate($this->rules($kendaraan), [], $this->attributes());

        $data['id_user'] = ($data['id_user'] ?? null) ?: null;

        $kendaraan->update($data);

        LogAktivitas::catat('Ubah kendaraan: '.$kendaraan->plat_nomor.' ('.$kendaraan->jenis_kendaraan.')');

        return redirect()
            ->route('admin.kendaraan.index')
            ->with('success', 'Kendaraan '.$kendaraan->plat_nomor.' berhasil diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan): RedirectResponse
    {
        if ($kendaraan->isSedangParkir()) {
            return back()->with('error', 'Kendaraan tidak dapat dihapus karena masih parkir.');
        }

        if (Transaksi::where('id_kendaraan', $kendaraan->id_kendaraan)->exists()) {
            return back()->with('error', 'Kendaraan tidak dapat dihapus karena memiliki riwayat transaksi.');
        }

        $plat = $kendaraan->plat_nomor;
        $kendaraan->delete();

        LogAktivitas::catat('Hapus kendaraan: '.$plat);

        return redirect()
            ->route('admin.kendaraan.index')
            ->with('success', 'Kendaraan '.$plat.' berhasil dihapus.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Kendaraan $kendaraan = null): array
    {
        return [
            'plat_nomor' => [
                'required',
                'string',
                'max:15',
                Rule::unique('tb_kendaraan', 'plat_nomor')->ignore($kendaraan?->id_kendaraan, 'id_kendaraan'),
            ],
            'jenis_kendaraan' => ['required', Rule::in(['motor', 'mobil', 'lainnya'])],
            'warna' => ['nullable', 'string', 'max:20'],
            'pemilik' => ['nullable', 'string', 'max:100'],
            'id_user' => ['nullable', 'integer', 'exists:tb_user,id_user'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function attributes(): array
    {
        return [
            'plat_nomor' => 'plat nomor',
            'jenis_kendaraan' => 'jenis kendaraan',
            'id_user' => 'pemilik akun',
        ];
    }

    private function normalisasiPlat(?string $plat): string
    {
        return strtoupper(trim(preg_replace('/\s+/', ' ', (string) $plat)));
    }
}
