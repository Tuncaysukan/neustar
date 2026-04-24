@extends('layouts.admin')

@section('header')
    Şablonu Düzenle — {{ $template->name }}
@endsection

@section('content')
    @include('admin.location-meta.create')
@endsection
