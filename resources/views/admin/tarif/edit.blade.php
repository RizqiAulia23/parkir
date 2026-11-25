@extends('layouts.app')

@section('title', 'Edit Tarif')

@section('content')
    <x-page-header title="Edit Tarif" :description="'Perbarui tarif '.$tarif->jenis_kendaraan.'.'" />

    @include('admin.tarif._form', [
        'tarif' => $tarif,
        'action' => route('admin.tarif.update', $tarif),
        'method' => 'PUT',
        'submitLabel' => 'Perbarui Tarif',
    ])
@endsection
