<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-theme-text leading-tight">
            {{ __('Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-theme-surface overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-theme-text">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Listado de usuarios</h3>
                        <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-primary/80">Nuevo usuario</a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-brand-secondary/20 text-theme-text rounded">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-brand-secondary/10 text-brand-secondary rounded">{{ session('error') }}</div>
                    @endif

                    <table class="w-full border-separate border-spacing-0 border border-theme-border rounded-xl overflow-hidden shadow-sm">
                        <thead class="bg-brand-midnight dark:bg-slate-950">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-theme-surface divide-y divide-theme-border">
                            @foreach($users as $user)
                                <tr class="hover:bg-theme-surface-alt transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-muted">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-muted">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-muted capitalize">{{ $user->role }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                        <a href="{{ route('users.edit', $user->id) }}" class="text-sm font-medium text-brand-primary hover:text-brand-primary/80 transition-colors">Editar</a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-brand-secondary hover:text-brand-secondary/80">Eliminar</button>
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
