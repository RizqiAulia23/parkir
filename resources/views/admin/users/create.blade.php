@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
    <x-page-header title="Tambah User" description="Buat akun baru untuk admin, petugas, atau owner." />

    @include('admin.users._form', [
        'user' => null,
        'action' => route('admin.users.store'),
        'method' => 'POST',
        'submitLabel' => 'Simpan User',
    ])
@endsection
