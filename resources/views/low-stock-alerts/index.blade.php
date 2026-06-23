<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-theme-text leading-tight">
            {{ __('Alertas de stock bajo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-theme-surface overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-theme-text">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-brand-secondary/20 text-theme-text rounded">{{ session('success') }}</div>
                    @endif

                    <table class="w-full border-separate border-spacing-0 border border-theme-border rounded-xl overflow-hidden shadow-sm">
                        <thead class="bg-brand-midnight dark:bg-slate-950">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Stock actual</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Stock mínimo</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-theme-surface divide-y divide-theme-border">
                            @forelse($alerts as $alert)
                                <tr class="hover:bg-theme-surface-alt transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-muted">
                                        <a href="{{ route('products.show', $alert->product->id) }}" class="text-brand-primary hover:text-brand-primary/80">
                                            {{ $alert->product->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-muted">{{ $alert->product->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-muted">{{ $alert->product->minStock }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-muted">{{ $alert->createdAt->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-muted">
                                        <form action="{{ route('low-stock-alerts.resolve', $alert->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-brand-primary hover:text-brand-primary/80">Marcar resuelta</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="hover:bg-theme-surface-alt transition-colors duration-150">
                                    <td colspan="5" class="px-6 py-4 text-center text-theme-text-muted">No hay alertas pendientes.</td>
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
