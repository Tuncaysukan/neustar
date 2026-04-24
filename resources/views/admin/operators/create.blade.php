@extends('layouts.admin')

@section('header', 'Yeni Operatör')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        @include('admin.operators._form')
    </div>
@endsection
