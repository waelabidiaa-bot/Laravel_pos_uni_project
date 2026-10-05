@extends('layouts.app')
@section('title', 'Edit cashier')
@section('content')
<h1 class="text-2xl font-bold mb-6">Edit {{ $cashier->name }}</h1>
@include('admin.cashiers._form', ['action' => route('admin.cashiers.update', $cashier), 'method' => 'PUT'])
@endsection
