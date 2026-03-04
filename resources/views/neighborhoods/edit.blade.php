<x-app-layout>
    <x-slot name="logo_url">{{ route('neighborhoods.index') }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Barrio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('neighborhoods.update', $neighborhood) }}">
                        @csrf
                        @method('PUT')

                        {{-- City --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ciudad</label>
                            <select name="city_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Seleccione una ciudad</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id', $neighborhood->city_id) == $city->id ? 'selected' : '' }}>
                                        {{ $city->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('city_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Name --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre
                                del Barrio</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $neighborhood->name) }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Active --}}
                        <div class="mb-4">
                            <label for="is_active" class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $neighborhood->is_active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Barrio Activo</span>
                            </label>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Actualizar
                            </button>
                            <a href="{{ route('neighborhoods.index') }}"
                                class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">Cancelar</a>
                        </div>
                    </form>

                    <div class="mt-8 border-t pt-6" x-data>
                        <form id="delete-neighborhood-form" action="{{ route('neighborhoods.destroy', $neighborhood) }}"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="$dispatch('open-modal', 'confirm-delete')"
                                class="text-red-600 hover:text-red-800 text-sm font-bold">
                                Eliminar Barrio
                            </button>
                        </form>

                        <x-modal-confirm name="confirm-delete" title="Eliminar Barrio"
                            content="¿Estás seguro de eliminar este barrio? Esta acción no se puede deshacer.">
                            <x-slot name="footer">
                                <x-secondary-button x-on:click="$dispatch('close')">
                                    Cancelar
                                </x-secondary-button>

                                <x-danger-button class="ml-3"
                                    x-on:click="document.getElementById('delete-neighborhood-form').submit()">
                                    Sí, eliminar
                                </x-danger-button>
                            </x-slot>
                        </x-modal-confirm>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>