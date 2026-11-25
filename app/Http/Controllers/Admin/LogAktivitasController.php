<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogAktivitasController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));
        $tanggal = trim((string) $request->input('tanggal'));

        $log = LogAktivitas::query()
            ->with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('aktivitas', 'ilike', '%'.$search.'%')
                        ->orWhereHas('user', fn ($user) => $user->where('username', 'ilike', '%'.$search.'%'));
                });
            })
            ->when($tanggal !== '', function ($query) use ($tanggal) {
                try {
                    $mulai = Carbon::parse($tanggal)->startOfDay();
                    $selesai = Carbon::parse($tanggal)->endOfDay();
                    $query->whereBetween('waktu_aktivitas', [$mulai, $selesai]);
                } catch (\Throwable) {
                    // Abaikan tanggal yang tidak valid.
                }
            })
            ->orderByDesc('waktu_aktivitas')
            ->orderByDesc('id_log')
            ->paginate(15)
            ->withQueryString();

        return view('admin.log.index', compact('log', 'search', 'tanggal'));
    }
}
