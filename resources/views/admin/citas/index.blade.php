<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Listado de Citas') }}
        </h2>
    </x-slot>

    <div class="py-6"> {{-- Se cambió py-8 por py-6 para consistencia --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Botón 'Crear Cita' con estilo adaptado --}}
            <div class="flex justify-end mb-4"> {{-- Se cambió mb-6 por mb-4 para consistencia --}}
                <a href="{{ route('admin.citas.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{-- Se quitó el '+' para coincidir con el estilo del botón 'Crear Moto' --}}
                    Agendar Cita 
                </a>
            </div>

            {{-- Mensaje de éxito (adaptado del código 'Motos') --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-200 text-green-800 rounded-md dark:bg-green-800 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Contenedor principal de la tabla con encabezado de tarjeta (adaptado del código 'Motos') --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">

                    {{-- Encabezado de la Tarjeta (adaptado del código 'Motos') --}}
                    <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                        <h4>Gestionar Citas</h4>
                    </div>

                    <table class="w-full table-auto divide-y divide-gray-200 dark:divide-gray-700"> {{-- w-full table-auto para consistencia --}}
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Cliente') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Moto') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Servicio') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Mecánico') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Fecha') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Hora') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Estado') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Motivo') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"> {{-- text-right para consistencia con 'Motos' --}}
                                    {{ __('Acciones') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($citas as $cita) {{-- Se cambió @foreach a @forelse para usar la estructura de "No hay datos" --}}
                                <tr>
                                    {{-- Cliente --}}
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $cita->cliente?->nombre }} {{ $cita->cliente?->apellido }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400"> C.C {{ $cita->cliente?->documento }}</div>
                                    </td>

                                    {{-- Moto --}}
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $cita->moto?->placa }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $cita->moto?->marca }} {{ $cita->moto?->modelo }} @if ($cita->moto?->color) ({{ $cita->moto->color }}) @endif</div>
                                    </td>

                                    {{-- Servicio --}}
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ filled($cita->servicio) ? $cita->servicio : '-' }}
                                    </td>

                                    {{-- Mecánico --}}
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $cita->mecanico?->name ?? 'Sin asignar' }}
                                    </td>

                                    {{-- Fecha --}}
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                    </td>

                                    {{-- Hora --}}
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($cita->hora)->format('g:i A') }}
                                    </td>

                                    {{-- Estado --}}
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        @php
                                            $estadoClases = [
                                                'solicitada' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                'aceptada' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                'atendida' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                'cancelada' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            ];
                                            $claseEstado = $estadoClases[$cita->estado];
                                        @endphp
                                        <span class="px-3 py-1 text-xs rounded-full font-semibold {{ $claseEstado }}">
                                            {{ ucfirst($cita->estado) }}
                                        </span>
                                    </td>

                                    {{-- Motivo de cancelación --}}
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        @if ($cita->estado === 'cancelada' && filled($cita->motivo_cancelacion))
                                            <span title="{{ $cita->motivo_cancelacion }}">
                                                {{ \Illuminate\Support\Str::limit($cita->motivo_cancelacion, 40) }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- Acciones (Alineación a la derecha y estilo adaptado del código 'Motos') --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                        <div class="flex justify-end space-x-2">
                                            {{-- Botón Editar con estilo detallado (adaptado de 'Motos') --}}
                                            <a href="{{ route('admin.citas.edit', $cita->id) }}"
                                                class="inline-flex items-center px-3 py-1.5 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                {{ __('Editar') }}
                                            </a>

                                            {{-- Formulario y Botón Eliminar con estilo detallado (adaptado de 'Motos') --}}
                                            <form action="{{ route('admin.citas.destroy', $cita->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('{{ __('¿Seguro que deseas eliminar esta cita?') }}');"> {{-- Mensaje de confirmación adaptado --}}
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    {{ __('Eliminar') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('No hay citas registradas.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>