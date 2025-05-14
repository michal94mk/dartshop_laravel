@extends('layouts.admin')

@section('title', 'Edytuj kategorię: ' . $category->name)

@section('content')
    @include('admin.categories.form')
@endsection 