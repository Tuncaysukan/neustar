@extends('layouts.admin')

@section('header', 'Operatörü Düzenle')

@section('content')
    <style>
        .operator-form input[type="text"],
        .operator-form input[type="url"],
        .operator-form input[type="email"],
        .operator-form input[type="number"],
        .operator-form textarea,
        .operator-form select {
            color: #111827 !important;
            background-color: #ffffff !important;
        }
        .operator-form label {
            color: #374151 !important;
        }
    </style>
    <div class="bg-white rounded-lg shadow p-6 operator-form">
        @include('admin.operators._form', ['operator' => $operator])
    </div>
@endsection
