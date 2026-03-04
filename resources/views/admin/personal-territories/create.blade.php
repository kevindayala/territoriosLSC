<x-app-layout>
    <x-slot name="title">Asignar Territorio Personal</x-slot>
    <x-slot name="logo_url">{{ route('admin.personal-territories.index') }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Asignar Territorio Personal') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form action="{{ route('admin.personal-territories.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Publicador -->
                <div>
                    <label for="user_id"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Publicador</label>
                    <select id="user_id" name="user_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccione un publicador...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Territorio -->
                <div>
                    <label for="territory_id"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Territorio
                        (Disponibles)</label>
                    <select id="territory_id" name="territory_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccione un territorio...</option>
                        @foreach($territories as $territory)
                            <option value="{{ $territory->id }}" {{ old('territory_id') == $territory->id ? 'selected' : '' }}>
                                {{ $territory->code }} - {{ $territory->city->name }} - {{ $territory->neighborhood_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('territory_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha
                        Límite (Opcional)</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}"
                        min="{{ date('Y-m-d') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">La fecha hasta la cual el publicador debe completar el
                        territorio.</p>
                    @error('due_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Asignar Territorio
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tom Select for searchable dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <style>
        .ts-control {
            border-radius: 0.375rem;
            min-height: 42px;
            display: flex;
            align-items: center;
        }

        .dark .ts-control {
            background-color: #374151;
            border-color: #4B5563;
            color: #F9FAFB;
        }

        .dark .ts-wrapper.single .ts-control,
        .dark .ts-wrapper.single .ts-control input {
            color: #F9FAFB;
        }

        .dark .ts-dropdown {
            background-color: #374151;
            border-color: #4B5563;
            color: #F9FAFB;
        }

        .dark .ts-dropdown .option.active {
            background-color: #4B5563;
            color: #FFF;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            new TomSelect("#user_id", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });

            new TomSelect("#territory_id", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        });
    </script>
</x-app-layout>