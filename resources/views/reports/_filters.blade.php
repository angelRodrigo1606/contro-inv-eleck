<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <x-text-input name="from" type="date" class="block" :value="request('from')" placeholder="Desde" />
    <x-text-input name="to" type="date" class="block" :value="request('to')" placeholder="Hasta" />
    <select name="product_id" class="border-theme-input-border focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm bg-theme-surface text-theme-text">
        <option value="">Producto</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
        @endforeach
    </select>
    <select name="category_id" class="border-theme-input-border focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm bg-theme-surface text-theme-text">
        <option value="">Categoría</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
        @endforeach
    </select>
    <select name="supplier_id" class="border-theme-input-border focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm bg-theme-surface text-theme-text">
        <option value="">Proveedor</option>
        @foreach($suppliers as $supplier)
            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
        @endforeach
    </select>
    <x-secondary-button type="submit">Filtrar</x-secondary-button>
</form>
