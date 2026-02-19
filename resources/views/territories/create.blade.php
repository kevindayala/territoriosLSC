<x-app-layout>
    <x-slot name="logo_url">{{ route('admin.territories.index') }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Crear Territorio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('territories.store') }}">
                        @csrf
                        <input type="hidden" name="redirect_to"
                            value="{{ str_contains(url()->previous(), 'admin/territories') ? 'admin' : 'public' }}">

                        {{-- Code --}}
                        <div class="mb-4">
                            <label for="code"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código</label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- City --}}
                        <div class="mb-4">
                            <label for="city_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ciudad</label>
                            <select name="city_id" id="city_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Seleccione una ciudad</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('city_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Neighborhood --}}
                        <div class="mb-4">
                            <label for="neighborhood_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Barrio</label>
                            <input type="text" name="neighborhood_name" id="neighborhood_name"
                                value="{{ old('neighborhood_name') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Nombre del barrio">
                            @error('neighborhood_name') <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <label for="status"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                            <select name="status" id="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo
                                </option>
                            </select>
                            @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="mb-4">
                            <label for="notes"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notas</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                            @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Guardar
                            </button>
                            <a href="{{ str_contains(url()->previous(), 'admin/territories') ? route('admin.territories.index') : route('territories.index') }}"
                                class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>