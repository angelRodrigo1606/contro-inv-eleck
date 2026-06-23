<div class="space-y-4">
    <div>
        <x-input-label for="name" :value="__('Nombre')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $category->name ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="description" :value="__('Descripción')" />
        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-theme-input-border focus:border-brand-primary focus:ring-brand-primary rounded-md shadow-sm bg-theme-surface text-theme-text">{{ old('description', $category->description ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div>
        <label for="is_active" class="inline-flex items-center">
            <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-theme-input-border text-brand-primary shadow-sm focus:ring-brand-primary" {{ old('is_active', $category->isActive ?? true) ? 'checked' : '' }}>
            <span class="ms-2 text-sm text-theme-text-muted">{{ __('Activo') }}</span>
        </label>
    </div>
</div>

<div class="flex items-center gap-4 mt-6">
    <x-primary-button>{{ __('Guardar') }}</x-primary-button>
    <a href="{{ route('categories.index') }}" class="text-sm text-theme-text-muted hover:text-theme-text">Cancelar</a>
</div>
