<div class="space-y-4">
    <div>
        <x-input-label for="name" :value="__('Nombre')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $product->name ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="sku" :value="__('SKU')" />
        <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full" :value="old('sku', $product->sku ?? '')" required :disabled="isset($product)" />
        <x-input-error class="mt-2" :messages="$errors->get('sku')" />
        @if(isset($product))
            <p class="text-sm text-brand-midnight/60 mt-1">El SKU no se puede modificar.</p>
        @endif
    </div>

    <div>
        <x-input-label for="description" :value="__('Descripción')" />
        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-brand-midnight/30 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm">{{ old('description', $product->description ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="category_id" :value="__('Categoría')" />
            <select id="category_id" name="category_id" class="mt-1 block w-full border-brand-midnight/30 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm" required>
                <option value="">Selecciona una categoría</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category?->id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
        </div>

        <div>
            <x-input-label for="supplier_id" :value="__('Proveedor')" />
            <select id="supplier_id" name="supplier_id" class="mt-1 block w-full border-brand-midnight/30 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm" required>
                <option value="">Selecciona un proveedor</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier?->id ?? '') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('supplier_id')" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-input-label for="price" :value="__('Precio unitario')" />
            <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price', $product->price ?? '')" required />
            <x-input-error class="mt-2" :messages="$errors->get('price')" />
        </div>

        <div>
            <x-input-label for="quantity" :value="__('Cantidad inicial')" />
            <x-text-input id="quantity" name="quantity" type="number" min="0" class="mt-1 block w-full" :value="old('quantity', $product->quantity ?? 0)" required :disabled="isset($product)" />
            <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
        </div>

        <div>
            <x-input-label for="min_stock" :value="__('Stock mínimo')" />
            <x-text-input id="min_stock" name="min_stock" type="number" min="0" class="mt-1 block w-full" :value="old('min_stock', $product->minStock ?? 0)" required />
            <x-input-error class="mt-2" :messages="$errors->get('min_stock')" />
        </div>
    </div>
</div>

<div class="flex items-center gap-4 mt-6">
    <x-primary-button>{{ __('Guardar') }}</x-primary-button>
    <a href="{{ route('products.index') }}" class="text-sm text-brand-midnight/80 hover:text-brand-midnight">Cancelar</a>
</div>
