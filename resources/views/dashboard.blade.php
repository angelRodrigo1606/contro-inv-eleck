<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Inventario total valorizado</h3>
                    <p class="mt-2 text-3xl font-bold text-indigo-600">${{ number_format($totalValue, 2) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Productos con stock bajo</h3>
                    <p class="mt-2 text-3xl font-bold {{ $lowStockCount > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $lowStockCount }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Movimientos recientes</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-800">{{ $recentMovements->count() }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Movimientos recientes</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($recentMovements as $movement)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $movement->created_at->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $movement->product->name }}</td>
                                        <td class="px-4 py-2 text-sm capitalize">{{ $movement->type }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $movement->quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Alertas pendientes</h3>
                        @if($unresolvedAlerts->count())
                            <ul class="divide-y divide-gray-200">
                                @foreach($unresolvedAlerts as $alert)
                                    <li class="py-3 flex justify-between items-center">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $alert->product->name }}</p>
                                            <p class="text-sm text-gray-500">Stock: {{ $alert->product->quantity }} / {{ $alert->product->min_stock }}</p>
                                        </div>
                                        <a href="{{ route('products.show', $alert->product) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Ver</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-4">
                                <a href="{{ route('low-stock-alerts.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Ver todas</a>
                            </div>
                        @else
                            <p class="text-gray-500">No hay alertas pendientes.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
