<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-midnight leading-tight">
            {{ __('Proveedor') }}: {{ $supplier->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-brand-midnight">
                    <p><strong>Contacto:</strong> {{ $supplier->contactName ?: 'No especificado' }}</p>
                    <p><strong>Teléfono:</strong> {{ $supplier->phone ?: 'No especificado' }}</p>
                    <p><strong>Dirección:</strong> {{ $supplier->address ?: 'No especificada' }}</p>
                    <p><strong>Email:</strong> {{ $supplier->email ?: 'No especificado' }}</p>
                    <p><strong>Estado:</strong> {{ $supplier->isActive ? 'Activo' : 'Inactivo' }}</p>
                    <p><strong>Productos asociados:</strong> {{ $productsCount }}</p>

                    <div class="mt-6">
                        <a href="{{ route('suppliers.index') }}" class="text-brand-primary hover:text-brand-primary/80">Volver al listado</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
