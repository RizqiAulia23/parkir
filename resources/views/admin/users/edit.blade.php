@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <x-page-header title="Edit User" :description="'Perbarui data akun '.$user->username.'.'" />

    @include('admin.users._form', [
        'user' => $user,
        'action' => route('admin.users.update', $user),
        'method' => 'PUT',
        'submitLabel' => 'Perbarui User',
    ])
@endsection
