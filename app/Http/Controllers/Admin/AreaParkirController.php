<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaParkir;
use App\Models\LogAktivitas;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AreaParkirController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));

        $area = AreaParkir::query()
            ->when($search !== '', fn ($query) => $query->where('nama_area', 'ilike', '%'.$search.'%'))
            ->orderBy('nama_area')
            ->paginate(10)
            ->withQueryString();

        return view('admin.area.index', compact('area', 'search'));
    }

    public function create(): View
    {
        return view('admin.area.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(), [], $this->attributes());

        $area = AreaParkir::create([
            'nama_area' => $data['nama_area'],
            'kapasitas' => (int) $data['kapasitas'],
            'terisi' => 0,
        ]);

        LogAktivitas::catat('Tambah area: '.$area->nama_area.' (kapasitas '.$area->kapasitas.')');

        return redirect()
            ->route('admin.area.index')
            ->with('success', 'Area '.$area->nama_area.' berhasil ditambahkan.');
    }

    public function edit(AreaParkir $area): View
    {
        return view('admin.area.edit', compact('area'));
    }

    public function update(Request $request, AreaParkir $area): RedirectResponse
    {
        $data = $request->validate($this->rules($area), [], $this->attributes());

        if ((int) $data['kapasitas'] < $area->terisi) {
            return back()
                ->withErrors(['kapasitas' => 'Kapasitas tidak boleh lebih kecil dari jumlah kendaraan yang sedang terisi ('.$area->terisi.').'])
                ->withInput();
        }

        $area->update([
            'nama_area' => $data['nama_area'],
            'kapasitas' => (int) $data['kapasitas'],
        ]);

        LogAktivitas::catat('Ubah area: '.$area->nama_area.' (kapasitas '.$area->kapasitas.')');

        return redirect()
            ->route('admin.area.index')
            ->with('success', 'Area '.$area->nama_area.' berhasil diperbarui.');
    }

    public function destroy(AreaParkir $area): RedirectResponse
    {
        if ($area->terisi > 0) {
            return back()->with('error', 'Area tidak dapat dihapus karena masih ada kendaraan yang terparkir.');
        }

        if (Transaksi::where('id_area', $area->id_area)->exists()) {
            return back()->with('error', 'Area tidak dapat dihapus karena sudah dipakai pada transaksi.');
        }

        $nama = $area->nama_area;
        $area->delete();

        LogAktivitas::catat('Hapus area: '.$nama);

        return redirect()
            ->route('admin.area.index')
            ->with('success', 'Area '.$nama.' berhasil dihapus.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?AreaParkir $area = null): array
    {
        return [
            'nama_area' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tb_area_parkir', 'nama_area')->ignore($area?->id_area, 'id_area'),
            ],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:1000000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function attributes(): array
    {
        return [
            'nama_area' => 'nama area',
            'kapasitas' => 'kapasitas',
        ];
    }
}
