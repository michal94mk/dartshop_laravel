@extends('layouts.admin')

@section('title', 'Edytuj produkt: ' . $product->name)

@section('content')
    @include('admin.products.form')
@endsection 