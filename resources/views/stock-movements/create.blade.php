<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-theme-text leading-tight">
            {{ __('Registrar movimiento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-theme-surface overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-theme-text">
                    <form method="POST" action="{{ route('stock-movements.store') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="product_id" :value="__('Producto')" />
                                <select id="product_id" name="product_id" class="mt-1 block w-full border-theme-input-border focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm bg-theme-surface text-theme-text" required>
                                    <option value="">Selecciona un producto</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} (Stock: {{ $product->quantity }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('product_id')" />
                            </div>

                            <div>
                                <x-input-label for="type" :value="__('Tipo')" />
                                <select id="type" name="type" class="mt-1 block w-full border-theme-input-border focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm bg-theme-surface text-theme-text" required>
                                    <option value="">Selecciona el tipo</option>
                                    <option value="entry" {{ old('type') == 'entry' ? 'selected' : '' }}>Entrada</option>
                                    <option value="exit" {{ old('type') == 'exit' ? 'selected' : '' }}>Salida</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('type')" />
                            </div>

                            <div>
                                <x-input-label for="quantity" :value="__('Cantidad')" />
                                <x-text-input id="quantity" name="quantity" type="number" min="1" class="mt-1 block w-full" :value="old('quantity')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                            </div>

                            <div>
                                <x-input-label for="reference" :value="__('Referencia')" />
                                <x-text-input id="reference" name="reference" type="text" class="mt-1 block w-full" :value="old('reference')" placeholder="Número de factura, orden, cliente/destino" />
                                <x-input-error class="mt-2" :messages="$errors->get('reference')" />
                            </div>

                            <div>
                                <x-input-label for="notes" :value="__('Notas')" />
                                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-theme-input-border focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm bg-theme-surface text-theme-text">{{ old('notes') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                            <a href="{{ route('stock-movements.index') }}" class="text-sm text-theme-text-muted hover:text-theme-text">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
