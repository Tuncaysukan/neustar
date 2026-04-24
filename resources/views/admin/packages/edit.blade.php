@extends('layouts.admin')

@section('header')
    Paketi Düzenle
@endsection

@section('content')
    @include('admin.packages._form', [
        'action' => route('admin.packages.update', $package),
        'method' => 'PUT',
        'package' => $package,
        'operators' => $operators,
    ])
@endsection
