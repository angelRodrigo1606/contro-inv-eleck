<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-midnight leading-tight">
            {{ __('Alertas de stock bajo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-brand-midnight">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-brand-secondary/20 text-brand-midnight rounded">{{ session('success') }}</div>
                    @endif

                    <table class="min-w-full divide-y divide-brand-midnight/20">
                        <thead class="bg-brand-cream">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Stock actual</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Stock mínimo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-brand-midnight/20">
                            @forelse($alerts as $alert)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('products.show', $alert->product) }}" class="text-brand-primary hover:text-brand-primary/80">
                                            {{ $alert->product->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $alert->product->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $alert->product->min_stock }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $alert->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form action="{{ route('low-stock-alerts.resolve', $alert) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900">Marcar resuelta</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-brand-midnight/60">No hay alertas pendientes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $alerts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
