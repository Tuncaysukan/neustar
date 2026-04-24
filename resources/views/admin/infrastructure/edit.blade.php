@extends('layouts.admin')

@section('header')
    Altyapı kaydını düzenle · {{ $record->city_name }}
    @if($record->district_name) · {{ $record->district_name }} @endif
    @if($record->neighborhood_name) · {{ $record->neighborhood_name }} @endif
@endsection

@section('content')
    @include('admin.infrastructure._form')
@endsection
