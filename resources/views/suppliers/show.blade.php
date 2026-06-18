<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Proveedor') }}: {{ $supplier->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p><strong>Contacto:</strong> {{ $supplier->contact_name ?: 'No especificado' }}</p>
                    <p><strong>Teléfono:</strong> {{ $supplier->phone ?: 'No especificado' }}</p>
                    <p><strong>Dirección:</strong> {{ $supplier->address ?: 'No especificada' }}</p>
                    <p><strong>Email:</strong> {{ $supplier->email ?: 'No especificado' }}</p>
                    <p><strong>Estado:</strong> {{ $supplier->is_active ? 'Activo' : 'Inactivo' }}</p>
                    <p><strong>Productos asociados:</strong> {{ $supplier->products()->count() }}</p>

                    <div class="mt-6">
                        <a href="{{ route('suppliers.index') }}" class="text-indigo-600 hover:text-indigo-900">Volver al listado</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
