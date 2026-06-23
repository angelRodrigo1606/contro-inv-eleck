<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-theme-text leading-tight">
            {{ __('Movimientos de stock') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-theme-surface overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-theme-text">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Historial de movimientos</h3>
                        <a href="{{ route('stock-movements.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-primary/80 focus:bg-brand-primary/80 active:bg-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 transition ease-in-out duration-150">
                            Registrar movimiento
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-brand-secondary/20 text-theme-text rounded">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-brand-secondary/10 text-brand-secondary rounded">{{ session('error') }}</div>
                    @endif

                    <form method="GET" action="{{ route('stock-movements.index') }}" class="mb-4 flex flex-wrap gap-2">
                        <select name="type" class="border-theme-input-border focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm bg-theme-surface text-theme-text">
                            <option value="">Todos los tipos</option>
                            <option value="entry" {{ request('type') == 'entry' ? 'selected' : '' }}>Entrada</option>
                            <option value="exit" {{ request('type') == 'exit' ? 'selected' : '' }}>Salida</option>
                        </select>
                        <select name="product_id" class="border-theme-input-border focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm bg-theme-surface text-theme-text">
                            <option value="">Todos los productos</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <x-text-input name="from" type="date" class="block" :value="request('from')" />
                        <x-text-input name="to" type="date" class="block" :value="request('to')" />
                        <x-secondary-button type="submit">Filtrar</x-secondary-button>
                    </form>

                    <table class="w-full border-separate border-spacing-0 border border-theme-border rounded-xl overflow-hidden shadow-sm">
                        <thead class="bg-brand-midnight dark:bg-slate-950">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Referencia</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Usuario</th>
                            </tr>
                        </thead>
                        <tbody class="bg-theme-surface divide-y divide-theme-border">
                            @foreach($movements as $movement)
                                <tr class="hover:bg-theme-surface-alt transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-muted">{{ $movement->createdAt->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-muted">{{ $movement->product?->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap capitalize">
                                        @if($movement->type === 'entry')
                                            <span class="text-brand-primary font-semibold">Entrada</span>
                                        @else
                                            <span class="text-brand-secondary font-semibold">Salida</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-muted">{{ $movement->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-muted">{{ $movement->reference }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-muted">{{ $movement->user?->name }}</td>
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
