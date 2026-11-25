@php
    $editing = isset($user) && $user !== null;
    $inputClass = 'block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10';
@endphp

<form action="{{ $action }}" method="POST" class="space-y-5">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="nama_lengkap" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                <input
                    type="text"
                    name="nama_lengkap"
                    id="nama_lengkap"
                    value="{{ old('nama_lengkap', $user->nama_lengkap ?? '') }}"
                    maxlength="50"
                    class="{{ $inputClass }} @error('nama_lengkap') border-rose-400 @enderror"
                    required
                >
                @error('nama_lengkap')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="username" class="mb-1.5 block text-sm font-medium text-slate-700">Username</label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    value="{{ old('username', $user->username ?? '') }}"
                    maxlength="50"
                    class="{{ $inputClass }} @error('username') border-rose-400 @enderror"
                    autocomplete="off"
                    required
                >
                @error('username')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="mb-1.5 block text-sm font-medium text-slate-700">Role</label>
                <select name="role" id="role" class="{{ $inputClass }} @error('role') border-rose-400 @enderror" required>
                    @foreach (['admin' => 'Admin', 'petugas' => 'Petugas', 'owner' => 'Owner'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('role', $user->role ?? 'petugas') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('role')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status_aktif" class="mb-1.5 block text-sm font-medium text-slate-700">Status Akun</label>
                <select name="status_aktif" id="status_aktif" class="{{ $inputClass }} @error('status_aktif') border-rose-400 @enderror" required>
                    <option value="1" @selected((string) old('status_aktif', $user->status_aktif ?? 1) === '1')>Aktif</option>
                    <option value="0" @selected((string) old('status_aktif', $user->status_aktif ?? 1) === '0')>Nonaktif</option>
                </select>
                @error('status_aktif')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-8 border-t border-slate-100 pt-6">
            <h2 class="text-sm font-semibold text-slate-900">Keamanan</h2>
            <p class="mt-1 text-xs text-slate-500">
                {{ $editing ? 'Biarkan kosong jika password tidak ingin diubah.' : 'Password minimal 6 karakter.' }}
            </p>

            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="{{ $inputClass }} @error('password') border-rose-400 @enderror"
                        autocomplete="new-password"
                        @if (! $editing) required @endif
                    >
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="{{ $inputClass }}"
                        autocomplete="new-password"
                        @if (! $editing) required @endif
                    >
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
            <x-icon name="check" class="h-4 w-4" />
            {{ $submitLabel }}
        </button>
    </div>
</form>
