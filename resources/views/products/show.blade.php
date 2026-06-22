<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-midnight leading-tight">
            {{ __('Producto') }}: {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-brand-midnight">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <p><strong>SKU:</strong> {{ $product->sku }}</p>
                        <p><strong>Categoría:</strong> {{ $product->category?->name }}</p>
                        <p><strong>Proveedor:</strong> {{ $product->supplier?->name }}</p>
                        <p><strong>Precio:</strong> ${{ number_format($product->price, 2) }}</p>
                        <p class="{{ $product->isLowStock ? 'text-brand-secondary font-bold' : '' }}">
                            <strong>Stock actual:</strong> {{ $product->quantity }}
                        </p>
                        <p><strong>Stock mínimo:</strong> {{ $product->minStock }}</p>
                    </div>
                    <p class="mt-4"><strong>Descripción:</strong> {{ $product->description ?: 'Sin descripción' }}</p>

                    @if(auth()->user()->isAdmin())
                        <div class="mt-6 p-4 bg-white rounded">
                            <h4 class="font-medium mb-2">Ajustar stock</h4>
                            <form method="POST" action="{{ route('products.adjust', $product->id) }}" class="flex gap-2 items-end">
                                @csrf
                                <div>
                                    <x-input-label for="quantity" :value="__('Nueva cantidad')" />
                                    <x-text-input id="quantity" name="quantity" type="number" min="0" class="mt-1 block w-32" :value="$product->quantity" required />
                                </div>
                                <div class="flex-1">
                                    <x-input-label for="reason" :value="__('Motivo')" />
                                    <x-text-input id="reason" name="reason" type="text" class="mt-1 block w-full" placeholder="Opcional" />
                                </div>
                                <x-secondary-button type="submit">Ajustar</x-secondary-button>
                            </form>
                            <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                        </div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('products.index') }}" class="text-brand-primary hover:text-brand-primary/80">Volver al listado</a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-brand-midnight">
                    <h3 class="text-lg font-medium mb-4">Historial de movimientos</h3>
                    <table class="min-w-full divide-y divide-brand-midnight/20">
                        <thead class="bg-brand-cream">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Referencia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase tracking-wider">Usuario</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-brand-midnight/20">
                            @forelse($product->stockMovements as $movement)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $movement->createdAt->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $movement->type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $movement->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $movement->reference }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $movement->user?->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-brand-midnight/60">No hay movimientos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
