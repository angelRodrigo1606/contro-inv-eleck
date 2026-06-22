<div class="space-y-4">
    <div>
        <x-input-label for="name" :value="__('Nombre')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $supplier->name ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="contact_name" :value="__('Nombre de contacto')" />
        <x-text-input id="contact_name" name="contact_name" type="text" class="mt-1 block w-full" :value="old('contact_name', $supplier->contactName ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('contact_name')" />
    </div>

    <div>
        <x-input-label for="phone" :value="__('Teléfono')" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $supplier->phone ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
    </div>

    <div>
        <x-input-label for="address" :value="__('Dirección')" />
        <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-brand-midnight/30 focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm">{{ old('address', $supplier->address ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('address')" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Correo electrónico')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $supplier->email ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div>
        <label for="is_active" class="inline-flex items-center">
            <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-brand-midnight/30 text-brand-primary shadow-sm focus:ring-brand-primary" {{ old('is_active', $supplier->isActive ?? true) ? 'checked' : '' }}>
            <span class="ms-2 text-sm text-brand-midnight/80">{{ __('Activo') }}</span>
        </label>
    </div>
</div>

<div class="flex items-center gap-4 mt-6">
    <x-primary-button>{{ __('Guardar') }}</x-primary-button>
    <a href="{{ route('suppliers.index') }}" class="text-sm text-brand-midnight/80 hover:text-brand-midnight">Cancelar</a>
</div>
