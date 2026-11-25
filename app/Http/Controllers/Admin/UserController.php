<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('nama_lengkap', 'ilike', '%'.$search.'%')
                        ->orWhere('username', 'ilike', '%'.$search.'%');
                });
            })
            ->orderBy('id_user')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(), [], $this->attributes());

        $data['password'] = Hash::make($data['password']);
        $data['status_aktif'] = (int) $data['status_aktif'];

        $user = User::create($data);

        LogAktivitas::catat('Tambah user: '.$user->username.' ('.$user->role.')');

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User '.$user->username.' berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate($this->rules($user), [], $this->attributes());

        $data['status_aktif'] = (int) $data['status_aktif'];

        if ($user->id_user === auth()->id()) {
            if ($data['status_aktif'] !== 1) {
                return back()->withErrors(['status_aktif' => 'Anda tidak dapat menonaktifkan akun sendiri.'])->withInput();
            }

            if ($data['role'] !== 'admin') {
                return back()->withErrors(['role' => 'Anda tidak dapat mengubah role akun sendiri.'])->withInput();
            }
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        LogAktivitas::catat('Ubah user: '.$user->username.' ('.$user->role.')');

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data user '.$user->username.' berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id_user === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $username = $user->username;
        $role = $user->role;

        $user->delete();

        LogAktivitas::catat('Hapus user: '.$username.' ('.$role.')');

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User '.$username.' berhasil dihapus.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?User $user = null): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:50'],
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tb_user', 'username')->ignore($user?->id_user, 'id_user'),
            ],
            'role' => ['required', Rule::in(['admin', 'petugas', 'owner'])],
            'status_aktif' => ['required', Rule::in(['0', '1'])],
            'password' => [
                $user ? 'nullable' : 'required',
                'string',
                'min:6',
                'max:100',
                'confirmed',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function attributes(): array
    {
        return [
            'nama_lengkap' => 'nama lengkap',
            'status_aktif' => 'status aktif',
        ];
    }
}
