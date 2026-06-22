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

                    <table class="w-full border-separate border-spacing-0 border border-brand-midnight/10 rounded-xl overflow-hidden shadow-sm">
                        <thead class="bg-brand-midnight">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Stock actual</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Stock mínimo</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-brand-midnight/10">
                            @forelse($alerts as $alert)
                                <tr class="hover:bg-brand-soft transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">
                                        <a href="{{ route('products.show', $alert->product->id) }}" class="text-brand-primary hover:text-brand-primary/80">
                                            {{ $alert->product->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">{{ $alert->product->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">{{ $alert->product->minStock }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">{{ $alert->createdAt->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">
                                        <form action="{{ route('low-stock-alerts.resolve', $alert->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-brand-primary hover:text-brand-primary/80">Marcar resuelta</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="hover:bg-brand-soft transition-colors duration-150">
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
