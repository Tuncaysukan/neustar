@extends('layouts.admin')
@section('header') Kullanıcı Düzenle — {{ $user->name }} @endsection
@section('content')
    @include('admin.users.create')
@endsection
