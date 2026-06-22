<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-midnight leading-tight">
            {{ __('Categoría') }}: {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-brand-midnight">
                    <p><strong>Descripción:</strong> {{ $category->description ?: 'Sin descripción' }}</p>
                    <p><strong>Estado:</strong> {{ $category->isActive ? 'Activo' : 'Inactivo' }}</p>
                    <p><strong>Productos asociados:</strong> {{ $productsCount }}</p>

                    <div class="mt-6">
                        <a href="{{ route('categories.index') }}" class="text-brand-primary hover:text-brand-primary/80">Volver al listado</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
