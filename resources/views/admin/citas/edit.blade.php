<x-app-layout>
    {{-- Encabezado consistente con Editar Cliente --}}
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Cita') }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Contenedor principal coincidente con Editar Cliente --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

                {{-- Encabezado de la tarjeta --}}
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                    <h4>{{ __('Formulario de Edición de Cita') }}</h4>
                </div>

                {{-- Cuerpo --}}
                <div class="p-6">
                    <form action="{{ route('admin.citas.update', $cita->id_cita) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- CLIENTE --}}
                        <div>
                            <x-input-label for="cliente_id" :value="__('Cliente')" />
                            <select name="cliente_id" id="cliente_id"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                                focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600
                                rounded-md shadow-sm"
                                required>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id_cliente }}"
                                        {{ $cliente->id_cliente == $cita->cliente_id ? 'selected' : '' }}>
                                        {{ $cliente->nombre }} {{ $cliente->apellido }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                        </div>

                        {{-- MOTO --}}
                        <div>
                            <x-input-label for="moto_id" :value="__('Moto')" />
                            <select name="moto_id" id="moto_id"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                                focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600
                                rounded-md shadow-sm"
                                required>
                                @foreach ($motos as $moto)
                                    <option value="{{ $moto->id_moto }}"
                                        {{ $moto->id_moto == $cita->moto_id ? 'selected' : '' }}>
                                        {{ $moto->placa }} - {{ $moto->marca }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('moto_id')" class="mt-2" />
                        </div>

                        {{-- MECÁNICO --}}
                        <div>
                            <x-input-label for="mecanico_id" :value="__('Mecánico')" />
                            <select name="mecanico_id" id="mecanico_id"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                                focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600
                                rounded-md shadow-sm"
                                required>
                                @foreach ($mecanicos as $mecanico)
                                    <option value="{{ $mecanico->id }}"
                                        {{ $mecanico->id == $cita->mecanico_id ? 'selected' : '' }}>
                                        {{ $mecanico->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('mecanico_id')" class="mt-2" />
                        </div>

                        {{-- FECHA --}}
                        <div>
                            <x-input-label for="fecha" :value="__('Fecha')" />
                            <x-text-input id="fecha" type="date" name="fecha"
                                class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700"
                                :value="$cita->fecha" required />
                            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                        </div>

                        {{-- HORA --}}
                        <div>
                            <x-input-label for="hora" :value="__('Hora')" />
                            <x-text-input id="hora" type="time" name="hora"
                                class="block mt-1 w-full"
                                :value="$cita->hora" required />
                            <x-input-error :messages="$errors->get('hora')" class="mt-2" />
                        </div>

                        {{-- ESTADO --}}
                        <div>
                            <x-input-label for="estado" :value="__('Estado')" />
                            <select name="estado" id="estado"
                                class="block mt-1 w-full 
                                rounded-md shadow-sm"
                                required>
                                <option value="Agendada"   {{ $cita->estado == 'Agendada' ? 'selected' : '' }}>Agendada</option>
                                <option value="En Proceso" {{ $cita->estado == 'En Proceso' ? 'selected' : '' }}>En proceso</option>
                                <option value="Finalizada" {{ $cita->estado == 'Finalizada' ? 'selected' : '' }}>Finalizada</option>
                                <option value="Cancelada"  {{ $cita->estado == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>

                        {{-- BOTONES --}}
                        <div class="flex justify-start space-x-4 pt-4">
                            
                            {{-- Botón azul idéntico a Actualizar Cliente --}}
                            <x-primary-button class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Guardar Cambios') }}
                            </x-primary-button>

                            {{-- Botón gris idéntico a Cancelar --}}
                            <a href="{{ route('admin.citas.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancelar') }}
                            </a>
                        </div>

                    </form>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
