<x-app-layout>
    <x-slot name="title">Aprobaciones</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Aprobaciones Pendientes') }}
        </h2>
    </x-slot>

    <div class="py-6 md:py-12" x-data="{ selectedPersonId: null, selectedUserId: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 md:space-y-12" x-data="{ selectedTerritoryRequestId: null }">

            {{-- Pending Territory Requests --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl md:rounded-[2rem] border border-gray-100 dark:border-gray-700">
                <div class="p-4 sm:p-6 md:p-10 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-black mb-6 md:mb-10 flex items-center gap-3 tracking-tight">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        Solicitudes de Territorio Personal
                    </h3>
                    
                    @if($pendingTerritoryRequests->isEmpty())
                        <div class="flex flex-col items-center justify-center py-20 text-gray-400 bg-gray-50/50 dark:bg-gray-900/30 rounded-[2rem] border-2 border-dashed border-gray-100 dark:border-gray-800">
                            <svg class="w-16 h-16 mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6"></path></svg>
                            <p class="text-sm font-medium">No hay solicitudes de territorio pendientes.</p>
                        </div>
                    @else
                        <!-- Mobile View for Territory Requests -->
                        <div class="block md:hidden space-y-8">
                            @foreach($pendingTerritoryRequests as $request)
                                <div class="p-6 sm:p-7 bg-gray-50/50 dark:bg-gray-900/40 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                                    <div class="flex flex-row items-center text-left gap-5 sm:gap-6 mb-4">
                                        <div class="min-w-0 flex-1 py-1">
                                            <div class="text-[1.1rem] font-black text-gray-900 dark:text-white leading-tight truncate mb-0.5">
                                                [{{ $request->territory->code }}] {{ $request->territory->neighborhood_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 font-medium truncate">
                                                Publicador: <span class="text-gray-900 dark:text-gray-300">{{ $request->user->name }}</span>
                                            </div>
                                            <div class="mt-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 inline-block px-3 py-1 rounded-lg">
                                                Devolución estimada: {{ \Carbon\Carbon::parse($request->expected_return_date)->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <form action="{{ route('approvals.territory-request.approve', $request) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl text-xs font-black shadow-md shadow-emerald-200 dark:shadow-none transition-all active:scale-95">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                APROBAR ASIGNACIÓN
                                            </button>
                                        </form>
                                        <button type="button" @click="selectedTerritoryRequestId = {{ $request->id }}; $dispatch('open-modal', 'confirm-reject-territory-request')" 
                                                class="px-5 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 rounded-xl transition-all active:scale-95">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <form id="reject-territory-request-form-{{ $request->id }}" action="{{ route('approvals.territory-request.reject', $request) }}" method="POST" class="hidden">@csrf</form>
                                    <div class="mt-3 text-[10px] text-gray-400 text-center">Solicitado: {{ $request->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table for Territory Requests -->
                        <div class="hidden md:block overflow-x-auto w-full">
                            <table class="w-full min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                <thead class="bg-gray-50/50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Publicador</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Territorio Solicitado</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Devolución Estimada</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha Solicitud</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                    @foreach($pendingTerritoryRequests as $request)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $request->user->profile_photo_url }}" class="h-9 w-9 rounded-full object-cover">
                                                    <div class="font-bold text-gray-900 dark:text-white">{{ $request->user->name }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-bold text-gray-900 dark:text-white mb-0.5">[{{ $request->territory->code }}]</div>
                                                <div class="text-[11px] text-gray-500 truncate max-w-xs">{{ $request->territory->neighborhood_name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-emerald-600 dark:text-emerald-400 font-semibold">
                                                {{ \Carbon\Carbon::parse($request->expected_return_date)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $request->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="flex items-center justify-end gap-3">
                                                    <form action="{{ route('approvals.territory-request.approve', $request) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-2xl text-xs font-black shadow-lg shadow-emerald-200 dark:shadow-none transition-all hover:-translate-y-0.5 active:scale-95">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                            APROBAR
                                                        </button>
                                                    </form>
                                                    <button type="button" @click="selectedTerritoryRequestId = {{ $request->id }}; $dispatch('open-modal', 'confirm-reject-territory-request')" 
                                                            class="group bg-red-50 hover:bg-red-600 text-red-600 hover:text-white px-4 py-2.5 rounded-2xl text-xs font-black border border-red-100 dark:border-red-900/30 dark:bg-red-900/20 dark:hover:bg-red-900 transition-all flex items-center gap-2 active:scale-95">
                                                        <svg class="w-4 h-4 text-red-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        RECHAZAR
                                                    </button>
                                                    <form id="reject-territory-request-form-{{ $request->id }}" action="{{ route('approvals.territory-request.reject', $request) }}" method="POST" class="hidden">@csrf</form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $pendingTerritoryRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Pending Users --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl md:rounded-[2rem] border border-gray-100 dark:border-gray-700">
                <div class="p-4 sm:p-6 md:p-10 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-black mb-6 md:mb-10 flex items-center gap-3 tracking-tight">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Usuarios Pendientes
                    </h3>
                    
                    @if($pendingUsers->isEmpty())
                        <div class="flex flex-col items-center justify-center py-20 text-gray-400 bg-gray-50/50 dark:bg-gray-900/30 rounded-[2rem] border-2 border-dashed border-gray-100 dark:border-gray-800">
                            <svg class="w-16 h-16 mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <p class="text-sm font-medium">No hay nuevos usuarios pendientes.</p>
                        </div>
                    @else
                        <!-- Mobile View for Users -->
                        <div class="block md:hidden space-y-8">
                            @foreach($pendingUsers as $user)
                                <div class="p-6 sm:p-7 bg-gray-50/50 dark:bg-gray-900/40 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                                    <div class="flex flex-row items-center text-left gap-5 sm:gap-6 mb-6">
                                        <div class="relative flex-shrink-0 mr-2 sm:mr-3">
                                            <img src="{{ $user->profile_photo_url }}" class="h-[4.5rem] w-[4.5rem] rounded-full object-cover shadow-sm bg-white ring-4 ring-white dark:ring-gray-800">
                                            <div class="absolute bottom-0 right-0 translate-x-1 translate-y-1 bg-green-500 w-4 h-4 rounded-full border-2 border-white dark:border-gray-800"></div>
                                        </div>
                                        <div class="min-w-0 flex-1 py-1">
                                            <div class="text-[1.1rem] font-black text-gray-900 dark:text-white leading-tight truncate mb-0.5">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500 font-medium truncate">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <form action="{{ route('approvals.user.approve', $user) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl text-xs font-black shadow-md shadow-blue-200 dark:shadow-none transition-all active:scale-95">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                HABILITAR
                                            </button>
                                        </form>
                                        <button type="button" @click="selectedUserId = {{ $user->id }}; $dispatch('open-modal', 'confirm-reject-user')" 
                                                class="px-5 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 rounded-xl transition-all active:scale-95">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <form id="reject-user-form-{{ $user->id }}" action="{{ route('approvals.user.reject', $user) }}" method="POST" class="hidden">@csrf</form>
                                    <div class="mt-3 text-[10px] text-gray-400 text-center">Registrado: {{ $user->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table for Users -->
                        <div class="hidden md:block overflow-x-auto w-full">
                            <table class="w-full min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                <thead class="bg-gray-50/50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuario</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha Registro</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                    @foreach($pendingUsers as $user)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $user->profile_photo_url }}" class="h-9 w-9 rounded-full object-cover">
                                                    <div class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                {{ $user->email }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $user->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="flex items-center justify-end gap-3">
                                                    <form action="{{ route('approvals.user.approve', $user) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-2xl text-xs font-black shadow-lg shadow-blue-200 dark:shadow-none transition-all hover:-translate-y-0.5 active:scale-95">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                            HABILITAR
                                                        </button>
                                                    </form>
                                                    <button type="button" @click="selectedUserId = {{ $user->id }}; $dispatch('open-modal', 'confirm-reject-user')" 
                                                            class="group bg-red-50 hover:bg-red-600 text-red-600 hover:text-white px-4 py-2.5 rounded-2xl text-xs font-black border border-red-100 dark:border-red-900/30 dark:bg-red-900/20 dark:hover:bg-red-900 transition-all flex items-center gap-2 active:scale-95">
                                                        <svg class="w-4 h-4 text-red-500 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        ELIMINAR
                                                    </button>
                                                    <form id="reject-user-form-{{ $user->id }}" action="{{ route('approvals.user.reject', $user) }}" method="POST" class="hidden">@csrf</form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $pendingUsers->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Pending Persons --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl md:rounded-[2rem] border border-gray-100 dark:border-gray-700">
                <div class="p-4 sm:p-6 md:p-10 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-black mb-6 md:mb-10 flex items-center gap-3 tracking-tight">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Datos Pendientes
                    </h3>
                    
                    @if($pendingPersons->isEmpty())
                        <div class="flex flex-col items-center justify-center py-20 text-gray-400 bg-gray-50/50 dark:bg-gray-900/30 rounded-[2rem] border-2 border-dashed border-gray-100 dark:border-gray-800">
                            <svg class="w-16 h-16 mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            <p class="text-sm font-medium">No hay registros de personas pendientes.</p>
                        </div>
                    @else
                        <!-- Mobile View for Persons -->
                        <div class="block md:hidden space-y-6" x-data="{ openDiff: null }">
                            @foreach($pendingPersons as $person)
                                @php $isUpdate = !is_null($person->pending_changes); @endphp
                                <div class="bg-gray-50/50 dark:bg-gray-900/40 rounded-2xl md:rounded-[2.5rem] border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                                    <div class="p-5 sm:p-6 md:p-8 border-b border-gray-100 dark:border-gray-800">
                                        <div class="flex justify-between items-center mb-4 md:mb-6">
                                            @if($isUpdate)
                                                <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 ring-2 md:ring-4 ring-blue-50 dark:ring-0">Actualización</span>
                                            @else
                                                <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 ring-2 md:ring-4 ring-emerald-50 dark:ring-0">Nuevo</span>
                                            @endif
                                            <div class="text-xs font-black text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-800 px-3 py-1 rounded-full shadow-sm">{{ $person->territory->code }}</div>
                                        </div>
                                        <div class="font-black text-lg md:text-xl text-gray-900 dark:text-white leading-tight mb-2 uppercase tracking-tight">{{ $person->full_name }}</div>
                                        <div class="text-sm text-gray-500 font-medium leading-relaxed">{{ $person->address }}</div>
                                    </div>
                                    
                                    <div class="p-5 flex flex-col sm:flex-row gap-4 sm:justify-between sm:items-center bg-white/50 dark:bg-black/10">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                @php $actionUser = $isUpdate ? $person->pendingUser : $person->creator; @endphp
                                                @if($actionUser)
                                                    <img src="{{ $actionUser->profile_photo_url }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-white shadow-md">
                                                    <div class="flex flex-col">
                                                        <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-wider">
                                                            {{ $isUpdate ? 'Actualizado por:' : 'Registrado por:' }}
                                                        </span>
                                                        <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">{{ $actionUser->name }}</span>
                                                        <span class="text-[9px] text-gray-400">{{ $person->updated_at->diffForHumans() }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <form action="{{ route('approvals.approve', $person) }}" method="POST" class="m-0 inline-block">
                                                @csrf
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-[10px] font-black shadow-md shadow-green-100 dark:shadow-none transition-all flex items-center gap-1 active:scale-95">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    APROBAR
                                                </button>
                                            </form>
                                            @if($isUpdate)
                                                <button @click="openDiff === {{ $person->id }} ? openDiff = null : openDiff = {{ $person->id }}" 
                                                        class="bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 px-3 py-2 rounded-xl text-[10px] font-black border border-blue-100 dark:border-blue-800 transition-colors">COMPARAR</button>
                                            @endif
                                            <button type="button" @click="selectedPersonId = {{ $person->id }}; $dispatch('open-modal', 'confirm-reject')" 
                                                    class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>

                                    @if($isUpdate)
                                        <div x-show="openDiff === {{ $person->id }}" x-cloak class="p-3 bg-blue-100/30 dark:bg-blue-900/20 border-t dark:border-blue-900/50">
                                            <div class="space-y-3">
                                                @foreach($person->pending_changes as $key => $newValue)
                                                    @php 
                                                        $oldValue = $person->$key; 
                                                        $displayKey = $key;
                                                        if ($key === 'notes') {
                                                            $displayKey = 'Notas';
                                                        } elseif ($key === 'territory_id') {
                                                            $displayKey = 'Territorio';
                                                        } else {
                                                            $displayKey = ucfirst($displayKey);
                                                        }
                                                    @endphp
                                                    @if($oldValue != $newValue)
                                                        <div class="text-[10px]">
                                                            <div class="font-black text-gray-500 uppercase mb-1">{{ $displayKey }}</div>
                                                            <div class="grid grid-cols-2 gap-2">
                                                                <div class="p-2 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-lg line-through truncate">{{ $oldValue }}</div>
                                                                <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 rounded-lg font-bold truncate">{{ $newValue }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table for Persons -->
                        <div class="hidden md:block overflow-x-auto w-full" x-data="{ openDiff: null }">
                            <table class="w-full min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                <thead class="bg-gray-50/50 dark:bg-gray-900/50 text-gray-500">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Nombre / Dirección</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Territorio</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Solicitado Por</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                    @foreach($pendingPersons as $person)
                                        @php $isUpdate = !is_null($person->pending_changes); @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                                @if($isUpdate)
                                                    <span class="px-2 py-1 font-bold uppercase rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Actualización</span>
                                                @else
                                                    <span class="px-2 py-1 font-bold uppercase rounded-full bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 border border-green-200 dark:border-green-800">Nueva Persona</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-gray-900 dark:text-white leading-tight mb-1">{{ $person->full_name }}</div>
                                                <div class="text-[11px] text-gray-500 truncate max-w-xs">{{ $person->address }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                {{ $person->territory->code }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    @php $actionUser = $isUpdate ? $person->pendingUser : $person->creator; @endphp
                                                    @if($actionUser)
                                                        <img src="{{ $actionUser->profile_photo_url }}" class="h-8 w-8 rounded-full object-cover">
                                                        <div class="text-[10px]">
                                                            <div class="text-[9px] text-gray-400 uppercase font-bold mb-0.5">{{ $isUpdate ? 'Editado por' : 'Creado por' }}</div>
                                                            <div class="font-bold text-gray-900 dark:text-white leading-none mb-1">{{ $actionUser->name }}</div>
                                                            <div class="text-gray-400">{{ $person->updated_at->diffForHumans() }}</div>
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-400">N/A</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="flex items-center justify-end gap-3">
                                                    @if($isUpdate)
                                                        <button @click="openDiff === {{ $person->id }} ? openDiff = null : openDiff = {{ $person->id }}" 
                                                                class="text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 px-4 py-2 rounded-2xl text-xs font-black transition-all">
                                                            COMPARAR
                                                        </button>
                                                    @endif

                                                    <form action="{{ route('approvals.approve', $person) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-2xl text-xs font-black shadow-lg shadow-green-100 dark:shadow-none transition-all hover:-translate-y-0.5 active:scale-95">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                            APROBAR
                                                        </button>
                                                    </form>
                                                    
                                                    <a href="{{ route('persons.edit', ['person' => $person, 'redirect_to' => 'approvals']) }}" class="p-2.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-2xl transition-all" title="Editar manual">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </a>

                                                    <button type="button" 
                                                            @click="selectedPersonId = {{ $person->id }}; $dispatch('open-modal', 'confirm-reject')"
                                                            class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-2xl transition-all" title="Eliminar">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </div>

                                                <form id="reject-form-{{ $person->id }}" action="{{ route('approvals.reject', $person) }}" method="POST" class="hidden">
                                                    @csrf
                                                </form>
                                            </td>
                                        </tr>

                                        @if($isUpdate)
                                            <tr x-show="openDiff === {{ $person->id }}" x-cloak class="bg-blue-50/50 dark:bg-blue-900/10">
                                                <td colspan="5" class="px-6 py-4">
                                                    <div class="text-xs font-bold uppercase text-blue-600 dark:text-blue-400 mb-2">Comparación de Cambios</div>
                                                    <div class="grid grid-cols-2 gap-4 border dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-900">
                                                        <div class="p-3 border-r dark:border-gray-700">
                                                            <div class="text-[10px] text-gray-400 uppercase font-black mb-2">Dato Actual (En el sistema)</div>
                                                            <table class="w-full text-sm">
                                                                @foreach($person->pending_changes as $key => $newValue)
                                                                    @php 
                                                                        $oldValue = $person->$key; 
                                                                        $displayKey = $key;
                                                                        $displayOld = $oldValue;

                                                                        if ($key === 'territory_id') {
                                                                            $displayKey = 'Territorio';
                                                                            $oldTerr = $territories->firstWhere('id', $oldValue);
                                                                            $displayOld = $oldTerr ? "$oldTerr->code - $oldTerr->neighborhood_name" : 'Sin territorio';
                                                                        } elseif ($key === 'notes') {
                                                                            $displayKey = 'Notas';
                                                                        } else {
                                                                            $displayKey = ucfirst($displayKey);
                                                                        }
                                                                    @endphp
                                                                    @if($oldValue != $newValue)
                                                                        <tr class="border-b dark:border-gray-800 last:border-0 text-red-600 dark:text-red-400 font-medium">
                                                                            <td class="py-1 font-bold w-[1%] whitespace-nowrap pr-4 align-top text-[10px]">{{ $displayKey }}:</td>
                                                                            <td class="py-1 text-left">{{ $displayOld }}</td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </table>
                                                        </div>
                                                        <div class="p-3 bg-emerald-50/30 dark:bg-emerald-900/10">
                                                            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 uppercase font-black mb-2">Dato Nuevo (Pendiente)</div>
                                                            <table class="w-full text-sm">
                                                                @foreach($person->pending_changes as $key => $newValue)
                                                                    @php 
                                                                        $oldValue = $person->$key; 
                                                                        $displayKey = $key;
                                                                        $displayNew = $newValue;

                                                                        if ($key === 'territory_id') {
                                                                            $displayKey = 'Territorio';
                                                                            $newTerr = $territories->firstWhere('id', $newValue);
                                                                            $displayNew = $newTerr ? "$newTerr->code - $newTerr->neighborhood_name" : 'Sin territorio';
                                                                        } elseif ($key === 'notes') {
                                                                            $displayKey = 'Notas';
                                                                        } else {
                                                                            $displayKey = ucfirst($displayKey);
                                                                        }
                                                                    @endphp
                                                                    @if($oldValue != $newValue)
                                                                        <tr class="border-b dark:border-gray-800 last:border-0 text-emerald-700 dark:text-emerald-300 font-bold">
                                                                            <td class="py-1 font-bold w-[1%] whitespace-nowrap pr-4 align-top text-[10px]">{{ $displayKey }}:</td>
                                                                            <td class="py-1 text-left">{{ $displayNew }}</td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $pendingPersons->links() }}
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- Modales de Confirmación --}}
            <x-modal-confirm name="confirm-reject" title="Rechazar Persona"
                content="¿Estás seguro de que deseas eliminar este registro de persona?">
                <x-slot name="footer">
                    <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                    <x-danger-button class="ml-3" x-on:click="document.getElementById('reject-form-' + selectedPersonId).submit()">Sí, eliminar</x-danger-button>
                </x-slot>
            </x-modal-confirm>

            <x-modal-confirm name="confirm-reject-user" title="Eliminar Solicitud de Usuario"
                content="¿Estás seguro de que deseas eliminar este registro de usuario? El usuario no podrá entrar y su registro será borrado.">
                <x-slot name="footer">
                    <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                    <x-danger-button class="ml-3" x-on:click="document.getElementById('reject-user-form-' + selectedUserId).submit()">Sí, eliminar</x-danger-button>
                </x-slot>
            </x-modal-confirm>

            <x-modal-confirm name="confirm-reject-territory-request" title="Rechazar Solicitud de Territorio"
                content="¿Estás seguro de que deseas rechazar esta solicitud de territorio personal? El publicador no recibirá el territorio.">
                <x-slot name="footer">
                    <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                    <x-danger-button class="ml-3" x-on:click="document.getElementById('reject-territory-request-form-' + selectedTerritoryRequestId).submit()">Sí, rechazar</x-danger-button>
                </x-slot>
            </x-modal-confirm>
        </div>
    </div>
</x-app-layout>
