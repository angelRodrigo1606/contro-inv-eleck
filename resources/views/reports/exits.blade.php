<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-midnight leading-tight">
            {{ __('Reporte de salidas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-brand-midnight">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Salidas de mercancía</h3>
                        <div class="space-x-2">
                            <a href="{{ route('reports.exits.export', ['format' => 'csv'] + request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-brand-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-primary/80 transition-colors">CSV</a>
                            <a href="{{ route('reports.exits.export', ['format' => 'pdf'] + request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-brand-secondary border border-transparent rounded-md font-semibold text-xs text-brand-midnight uppercase tracking-widest hover:bg-brand-secondary/80 transition-colors">PDF</a>
                        </div>
                    </div>

                    @include('reports._filters')

                    <table class="w-full border-separate border-spacing-0 border border-brand-midnight/10 rounded-xl overflow-hidden shadow-sm">
                        <thead class="bg-brand-midnight">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Referencia</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-brand-midnight/10">
                            @foreach($movements as $movement)
                                <tr class="hover:bg-brand-soft transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">{{ $movement->product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">{{ $movement->product->category->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">{{ $movement->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">{{ $movement->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">{{ $movement->reference }}</td>
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
