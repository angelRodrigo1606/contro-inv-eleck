<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-midnight leading-tight">
            {{ __('Productos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-brand-midnight">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Listado de productos</h3>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-primary/80 focus:bg-brand-primary/80 active:bg-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 transition ease-in-out duration-150">
                                Nuevo producto
                            </a>
                        @endif
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-brand-secondary/20 text-brand-midnight rounded">{{ session('success') }}</div>
                    @endif

                    <form method="GET" action="{{ route('products.index') }}" class="mb-4 flex gap-2">
                        <x-text-input name="search" type="text" class="block w-full md:w-1/3" placeholder="Buscar por nombre o SKU" :value="request('search')" />
                        <select name="category_id" class="border-brand-midnight/30 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm">
                            <option value="">Todas las categorías</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-secondary-button type="submit">Buscar</x-secondary-button>
                    </form>

                    <table class="w-full border-separate border-spacing-0 border border-brand-midnight/10 rounded-xl overflow-hidden shadow-sm">
                        <thead class="bg-brand-midnight">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-brand-midnight/10">
                            @foreach($products as $product)
                                <tr class="{{ $product->isLowStock ? 'bg-brand-secondary/20' : '' }} hover:bg-brand-soft transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">{{ $product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">{{ $product->sku }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">{{ $product->category?->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90">${{ number_format($product->price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-brand-midnight/90 {{ $product->isLowStock ? 'text-brand-secondary font-bold' : '' }}">
                                        {{ $product->quantity }} / {{ $product->minStock }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                        <a href="{{ route('products.show', $product->id) }}" class="text-sm font-medium text-brand-primary hover:text-brand-primary/80 transition-colors">Ver</a>
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('products.edit', $product->id) }}" class="text-sm font-medium text-brand-primary hover:text-brand-primary/80 transition-colors">Editar</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
