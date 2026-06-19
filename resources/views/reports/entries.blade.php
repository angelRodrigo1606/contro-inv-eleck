<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-midnight leading-tight">
            {{ __('Reporte de entradas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-brand-midnight">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Entradas de mercancía</h3>
                        <div class="space-x-2">
                            <a href="{{ route('reports.entries.export', ['format' => 'csv'] + request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">CSV</a>
                            <a href="{{ route('reports.entries.export', ['format' => 'pdf'] + request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">PDF</a>
                        </div>
                    </div>

                    @include('reports._filters')

                    <table class="min-w-full divide-y divide-brand-midnight/20">
                        <thead class="bg-brand-cream">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase">Proveedor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase">Referencia</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-brand-midnight/20">
                            @foreach($movements as $movement)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $movement->product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $movement->product->category->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $movement->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $movement->product->supplier->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $movement->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $movement->reference }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $movements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
