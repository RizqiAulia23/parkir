@extends('layouts.app')

@section('title', 'Edit Area')

@section('content')
    <x-page-header title="Edit Area Parkir" :description="'Perbarui data '.$area->nama_area.'.'" />

    @include('admin.area._form', [
        'area' => $area,
        'action' => route('admin.area.update', $area),
        'method' => 'PUT',
        'submitLabel' => 'Perbarui Area',
    ])
@endsection
