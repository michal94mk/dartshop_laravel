@extends('layouts.admin')

@section('title', 'Edytuj markę: ' . $brand->name)

@section('content')
    @include('admin.brands.form')
@endsection 