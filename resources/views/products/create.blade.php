<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-midnight leading-tight">
            {{ __('Nuevo producto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-brand-midnight">
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf
                        @include('products._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
