<div class="space-y-4">
    <div>
        <x-input-label for="name" :value="__('Nombre')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Correo electrónico')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div>
        <x-input-label for="role" :value="__('Rol')" />
        <select id="role" name="role" class="mt-1 block w-full border-brand-midnight/30 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm" required>
            <option value="">Selecciona un rol</option>
            <option value="administrador" {{ old('role', $user->role ?? '') == 'administrador' ? 'selected' : '' }}>Administrador</option>
            <option value="empleado" {{ old('role', $user->role ?? '') == 'empleado' ? 'selected' : '' }}>Empleado</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('role')" />
    </div>

    <div>
        <x-input-label for="password" :value="__('Contraseña')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
        @if(isset($user))
            <p class="text-sm text-brand-midnight/60 mt-1">Deja en blanco para mantener la contraseña actual.</p>
        @endif
    </div>
</div>

<div class="flex items-center gap-4 mt-6">
    <x-primary-button>{{ __('Guardar') }}</x-primary-button>
    <a href="{{ route('users.index') }}" class="text-sm text-brand-midnight/80 hover:text-brand-midnight">Cancelar</a>
</div>
