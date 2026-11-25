@extends('layouts.app')

@section('title', 'Tambah Area')

@section('content')
    <x-page-header title="Tambah Area Parkir" description="Tentukan nama lokasi dan kapasitas slot." />

    @include('admin.area._form', [
        'area' => null,
        'action' => route('admin.area.store'),
        'method' => 'POST',
        'submitLabel' => 'Simpan Area',
    ])
@endsection
