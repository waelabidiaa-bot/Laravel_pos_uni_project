@extends('layouts.app')
@section('title', 'Edit product')
@section('content')
<h1 class="text-2xl font-bold mb-6">Edit {{ $product->name }}</h1>
@include('admin.products._form', ['action' => route('admin.products.update', $product), 'method' => 'PUT'])
@endsection
