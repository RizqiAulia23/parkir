@extends('layouts.app')

@section('title', 'Edit Kendaraan')

@section('content')
    <x-page-header title="Edit Kendaraan" :description="'Perbarui data kendaraan '.$kendaraan->plat_nomor.'.'" />

    @include('admin.kendaraan._form', [
        'kendaraan' => $kendaraan,
        'action' => route('admin.kendaraan.update', $kendaraan),
        'method' => 'PUT',
        'submitLabel' => 'Perbarui Kendaraan',
    ])
@endsection
