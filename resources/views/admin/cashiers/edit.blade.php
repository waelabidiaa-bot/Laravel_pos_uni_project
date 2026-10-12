@extends('layouts.app')

@section('title', 'Edit Cashier')

@section('content')
@php
    $currency = \App\Models\Parametre::first()?->currency ?? 'TND';
@endphp

<div class="mx-auto max-w-4xl space-y-6">

    {{-- Breadcrumb --}}
    <div>
        <a href="{{ route('admin.cashiers.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-4 w-4"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m15 18-6-6 6-6" />
            </svg>
            Back to cashiers
        </a>
    </div>

    {{-- Page heading --}}
    <div>
        <p class="text-sm font-medium text-slate-500">Management / Cashiers</p>

        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
            Edit cashier
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Update the account information for
            <span class="font-semibold text-slate-700">{{ $cashier->name }}</span>.
        </p>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4"
             role="alert">
            <h2 class="text-sm font-semibold text-rose-800">
                Please correct the following errors:
            </h2>

            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">
            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-lg font-bold text-slate-700">
                    {{ strtoupper(substr($cashier->name ?? '?', 0, 1)) }}
                </div>

                <div>
                    <h2 class="font-semibold text-slate-900">
                        {{ $cashier->name }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Update account details and credentials.
                    </p>
                </div>

            </div>
        </div>

        <div class="p-6">
            @include('admin.cashiers._form', [
                'action' => route('admin.cashiers.update', $cashier),
                'method' => 'PUT',
                'cashier' => $cashier
            ])
        </div>

    </div>

    <p class="text-xs leading-5 text-slate-500">
        Leave the password field empty if you do not want to change the current password.
    </p>

</div>
@endsection