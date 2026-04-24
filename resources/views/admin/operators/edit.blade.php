@extends('layouts.admin')

@section('header', 'Operatörü Düzenle')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        @include('admin.operators._form', ['operator' => $operator])
    </div>
@endsection
