<x-app-layout>
    <x-slot name="title">Nuevo Registro - Admin</x-slot>
    <x-slot name="logo_url">{{ route('admin.registros.index') }}</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-2xl font-bold mb-6">Crear Nuevo Registro</h2>

                    <form method="POST" action="{{ route('admin.registros.store') }}">
                        @include('admin.registros._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>