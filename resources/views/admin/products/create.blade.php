@extends('layouts.app')
@section('title', 'New product')
@section('content')
<h1 class="text-2xl font-bold mb-6">New product</h1>
@include('admin.products._form', ['action' => route('admin.products.store'), 'method' => 'POST'])
@endsection
