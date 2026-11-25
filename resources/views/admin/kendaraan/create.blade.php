@extends('layouts.app')

@section('title', 'Tambah Kendaraan')

@section('content')
    <x-page-header title="Tambah Kendaraan" description="Daftarkan kendaraan karyawan atau tamu kantor." />

    @include('admin.kendaraan._form', [
        'kendaraan' => null,
        'action' => route('admin.kendaraan.store'),
        'method' => 'POST',
        'submitLabel' => 'Simpan Kendaraan',
    ])
@endsection
