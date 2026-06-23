<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-theme-text leading-tight">
            {{ __('Editar categoría') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-theme-surface overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-theme-text">
                    <form method="POST" action="{{ route('categories.update', $category->id) }}">
                        @csrf
                        @method('PUT')
                        @include('categories._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
