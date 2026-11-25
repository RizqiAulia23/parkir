<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        [$mulai, $selesai, $basis] = $this->filter($request);

        $query = $this->query($mulai, $selesai, $basis);

        $ringkasan = $this->ringkasan($query);

        $transaksi = $query
            ->clone()
            ->with(['kendaraan', 'area', 'tarif', 'user'])
            ->orderByDesc($basis === 'keluar' ? 'waktu_keluar' : 'waktu_masuk')
            ->paginate(15)
            ->withQueryString();

        return view('owner.laporan.index', compact(
            'transaksi',
            'ringkasan',
            'mulai',
            'selesai',
            'basis'
        ));
    }

    public function cetak(Request $request): View
    {
        [$mulai, $selesai, $basis] = $this->filter($request);

        $query = $this->query($mulai, $selesai, $basis);

        $ringkasan = $this->ringkasan($query);

        $transaksi = $query
            ->clone()
            ->with(['kendaraan', 'area', 'tarif', 'user'])
            ->orderBy($basis === 'keluar' ? 'waktu_keluar' : 'waktu_masuk')
            ->get();

        return view('owner.laporan.cetak', compact(
            'transaksi',
            'ringkasan',
            'mulai',
            'selesai',
            'basis'
        ));
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    private function filter(Request $request): array
    {
        try {
            $mulai = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()))->startOfDay();
        } catch (\Throwable) {
            $mulai = now()->startOfMonth()->startOfDay();
        }

        try {
            $selesai = Carbon::parse($request->input('end_date', now()->toDateString()))->endOfDay();
        } catch (\Throwable) {
            $selesai = now()->endOfDay();
        }

        if ($selesai->lt($mulai)) {
            $selesai = $mulai->copy()->endOfDay();
        }

        $basis = $request->input('basis', 'masuk') === 'keluar' ? 'keluar' : 'masuk';

        return [$mulai, $selesai, $basis];
    }

    private function query(Carbon $mulai, Carbon $selesai, string $basis): Builder
    {
        $kolom = $basis === 'keluar' ? 'waktu_keluar' : 'waktu_masuk';

        $query = Transaksi::query()->whereBetween($kolom, [$mulai, $selesai]);

        if ($basis === 'keluar') {
            $query->where('status', 'keluar');
        }

        return $query;
    }

    /**
     * @return array{total: int, selesai: int, aktif: int, pendapatan: float}
     */
    private function ringkasan(Builder $query): array
    {
        $total = (clone $query)->count();
        $selesai = (clone $query)->where('status', 'keluar')->count();
        $pendapatan = (float) (clone $query)->where('status', 'keluar')->sum('biaya_total');

        return [
            'total' => $total,
            'selesai' => $selesai,
            'aktif' => max(0, $total - $selesai),
            'pendapatan' => $pendapatan,
        ];
    }
}
