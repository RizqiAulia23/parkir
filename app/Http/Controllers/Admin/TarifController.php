<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Tarif;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TarifController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));

        $tarif = Tarif::query()
            ->when($search !== '', fn ($query) => $query->where('jenis_kendaraan', 'ilike', '%'.$search.'%'))
            ->orderBy('id_tarif')
            ->paginate(10)
            ->withQueryString();

        return view('admin.tarif.index', compact('tarif', 'search'));
    }

    public function create(): View
    {
        return view('admin.tarif.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(), [], $this->attributes());

        $tarif = Tarif::create($data);

        LogAktivitas::catat('Tambah tarif: '.$tarif->jenis_kendaraan.' - Rp'.number_format((float) $tarif->tarif_per_jam, 0, ',', '.').'/jam');

        return redirect()
            ->route('admin.tarif.index')
            ->with('success', 'Tarif '.$tarif->jenis_kendaraan.' berhasil ditambahkan.');
    }

    public function edit(Tarif $tarif): View
    {
        return view('admin.tarif.edit', compact('tarif'));
    }

    public function update(Request $request, Tarif $tarif): RedirectResponse
    {
        $data = $request->validate($this->rules($tarif), [], $this->attributes());

        $tarif->update($data);

        LogAktivitas::catat('Ubah tarif: '.$tarif->jenis_kendaraan.' - Rp'.number_format((float) $tarif->tarif_per_jam, 0, ',', '.').'/jam');

        return redirect()
            ->route('admin.tarif.index')
            ->with('success', 'Tarif '.$tarif->jenis_kendaraan.' berhasil diperbarui.');
    }

    public function destroy(Tarif $tarif): RedirectResponse
    {
        if (Transaksi::where('id_tarif', $tarif->id_tarif)->exists()) {
            return back()->with('error', 'Tarif tidak dapat dihapus karena sudah dipakai pada transaksi.');
        }

        $jenis = $tarif->jenis_kendaraan;
        $tarif->delete();

        LogAktivitas::catat('Hapus tarif: '.$jenis);

        return redirect()
            ->route('admin.tarif.index')
            ->with('success', 'Tarif '.$jenis.' berhasil dihapus.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Tarif $tarif = null): array
    {
        return [
            'jenis_kendaraan' => [
                'required',
                Rule::in(['motor', 'mobil', 'lainnya']),
                Rule::unique('tb_tarif', 'jenis_kendaraan')->ignore($tarif?->id_tarif, 'id_tarif'),
            ],
            'tarif_per_jam' => ['required', 'numeric', 'min:0', 'max:9999999999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function attributes(): array
    {
        return [
            'jenis_kendaraan' => 'jenis kendaraan',
            'tarif_per_jam' => 'tarif per jam',
        ];
    }
}
