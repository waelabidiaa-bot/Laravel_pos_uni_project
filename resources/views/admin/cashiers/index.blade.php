{{-- Expects: $cashiers (paginator of User) --}}
@extends('layouts.app')

@section('title', 'Cashiers')

@section('content')
@php
    $currency = \App\Models\Parametre::first()?->currency ?? 'TND';
@endphp

<div class="space-y-6">

    {{-- Page header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">Management</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                Cashiers
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Manage your cashiers and their account information.
            </p>
        </div>

        <a href="{{ route('admin.cashiers.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 5v14m-7-7h14" />
            </svg>
            New cashier
        </a>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800"
             role="status">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="mt-0.5 h-5 w-5 shrink-0"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m5 12 4 4L19 6" />
            </svg>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"
             role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- Cashiers table --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Table header --}}
        <div class="flex flex-col gap-1 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-slate-900">All cashiers</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Accounts authorized to use the cashier interface.
                </p>
            </div>

            <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                {{ $cashiers->total() }} {{ $cashiers->total() === 1 ? 'cashier' : 'cashiers' }}
            </span>
        </div>

        @if ($cashiers->isNotEmpty())

            {{-- Responsive table --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[650px] text-left text-sm">

                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-5 py-4 font-semibold">
                                Cashier
                            </th>

                            <th scope="col" class="px-5 py-4 font-semibold">
                                Email address
                            </th>

                            <th scope="col" class="px-5 py-4 font-semibold">
                                Account created
                            </th>

                            <th scope="col" class="px-5 py-4 text-right font-semibold">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($cashiers as $cashier)
                            <tr class="transition hover:bg-slate-50/80">

                                {{-- Cashier name and avatar --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-700">
                                            {{ strtoupper(substr($cashier->name ?? '?', 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-900">
                                                {{ $cashier->name }}
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-500">
                                                Cashier account
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Email --}}
                                <td class="px-5 py-4 text-slate-600">
                                    {{ $cashier->email }}
                                </td>

                                {{-- Creation date --}}
                                <td class="px-5 py-4 text-slate-600">
                                    {{ $cashier->created_at?->format('d M Y') ?? '—' }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-3">

                                        <a href="{{ route('admin.cashiers.edit', $cashier) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-100">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="h-4 w-4"
                                                 viewBox="0 0 24 24"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="m16.862 4.487 2.651 2.651M4 20l4.5-1 11-11a2.121 2.121 0 0 0-3-3l-11 11L4 20Z" />
                                            </svg>
                                            Edit
                                        </a>

                                        <form method="POST"
                                              action="{{ route('admin.cashiers.destroy', $cashier) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this cashier?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:border-rose-300 hover:bg-rose-50">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="h-4 w-4"
                                                     viewBox="0 0 24 24"
                                                     fill="none"
                                                     stroke="currentColor"
                                                     stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M3 6h18m-2 0-.9 14H5.9L5 6m4 0V4h6v2m-5 4v6m4-6v6" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($cashiers->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $cashiers->links() }}
                </div>
            @endif

        @else

            {{-- Empty state --}}
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-7 w-7"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m16 0v-2a4 4 0 0 0-3-3.87M14 3.13a4 4 0 0 1 0 7.75M14 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" />
                    </svg>
                </div>

                <h3 class="mt-4 text-base font-semibold text-slate-900">
                    No cashiers yet
                </h3>

                <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                    Create a cashier account to give an employee access to the cashier interface.
                </p>

                <a href="{{ route('admin.cashiers.create') }}"
                   class="mt-5 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-4 w-4"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 5v14m-7-7h14" />
                    </svg>
                    Create your first cashier
                </a>
            </div>

        @endif

    </div>

</div>
@endsection