@extends('layouts.app')
@section('title', 'New cashier')
@section('content')
<h1 class="text-2xl font-bold mb-6">New cashier</h1>
@include('admin.cashiers._form', ['action' => route('admin.cashiers.store'), 'method' => 'POST'])
@endsection
