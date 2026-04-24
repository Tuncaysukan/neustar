@extends('layouts.admin')

@section('header')
    Yeni Paket
@endsection

@section('content')
    @include('admin.packages._form', [
        'action' => route('admin.packages.store'),
        'method' => 'POST',
        'package' => null,
        'operators' => $operators,
    ])
@endsection
