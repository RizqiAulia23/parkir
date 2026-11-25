@extends('layouts.app')

@section('title', 'Tambah Tarif')

@section('content')
    <x-page-header title="Tambah Tarif" description="Tentukan tarif per jam untuk jenis kendaraan." />

    @include('admin.tarif._form', [
        'tarif' => null,
        'action' => route('admin.tarif.store'),
        'method' => 'POST',
        'submitLabel' => 'Simpan Tarif',
    ])
@endsection
