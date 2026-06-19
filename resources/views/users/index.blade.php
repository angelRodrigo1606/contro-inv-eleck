<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-midnight leading-tight">
            {{ __('Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-brand-midnight">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Listado de usuarios</h3>
                        <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-primary/80">Nuevo usuario</a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-brand-secondary/20 text-brand-midnight rounded">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
                    @endif

                    <table class="min-w-full divide-y divide-brand-midnight/20">
                        <thead class="bg-brand-cream">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-brand-midnight uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-brand-midnight/20">
                            @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $user->role }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                        <a href="{{ route('users.edit', $user) }}" class="text-brand-primary hover:text-brand-primary/80">Editar</a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Estás seguro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
