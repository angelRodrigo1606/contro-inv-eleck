<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-midnight leading-tight">
            {{ __('Proveedores') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-brand-midnight">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Listado de proveedores</h3>
                        <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-primary/80 focus:bg-brand-primary/80 active:bg-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 transition ease-in-out duration-150">
                            Nuevo proveedor
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-brand-secondary/20 text-brand-midnight rounded">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
                    @endif

                    <table class="min-w-full divide-y divide-brand-midnight/20">
                        <thead class="bg-brand-cream">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Contacto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Productos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-brand-midnight/20">
                            @foreach($suppliers as $supplier)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->contact_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->phone }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->products_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="text-brand-primary hover:text-brand-primary/80">Editar</a>
                                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('¿Estás seguro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $suppliers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
