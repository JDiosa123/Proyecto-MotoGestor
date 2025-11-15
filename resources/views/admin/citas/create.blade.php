<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Agendar Nueva Cita') }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Contenedor principal de la tarjeta del formulario --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                
                {{-- Encabezado de la Tarjeta --}}
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                    <h4>{{ __('Formulario de Agendamiento de Cita') }}</h4>
                </div>

                {{-- Cuerpo del Formulario --}}
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.citas.store') }}" class="space-y-6">
                        @csrf

                        {{-- Cliente --}}
                        <div>
                            <x-input-label for="cliente_id" :value="__('Cliente')" />
                            {{-- Se utiliza un select nativo, aplicando las clases de Tailwind de los componentes de entrada --}}
                            <select id="cliente_id" name="cliente_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="">{{ __('Seleccione un cliente') }}</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id_cliente }}" {{ old('cliente_id') == $cliente->id_cliente ? 'selected' : '' }}>
                                        {{ $cliente->documento }} - {{ $cliente->nombre }}  {{ $cliente->apellido }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                        </div>

                        {{-- Moto --}}
                        <div>
                            <x-input-label for="moto_id" :value="__('Moto')" />
                            {{-- Select dinámico para las motos del cliente --}}
                            <select name="moto_id" id="moto_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="">{{ __('Seleccione primero un cliente') }}</option>
                                {{-- Las opciones de motos se llenarán con JavaScript --}}
                            </select>
                            <x-input-error :messages="$errors->get('moto_id')" class="mt-2" />
                        </div>

                        {{-- Mecánico --}}
                        <div>
                            <x-input-label for="mecanico_id" :value="__('Mecánico Encargado')" />
                            <select name="mecanico_id" id="mecanico_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="">{{ __('Seleccione un mecánico') }}</option>
                                @foreach ($mecanicos as $mecanico)
                                    <option value="{{ $mecanico->id }}" {{ old('mecanico_id') == $mecanico->id ? 'selected' : '' }}>
                                        {{ $mecanico->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('mecanico_id')" class="mt-2" />
                        </div>

                        {{-- Fecha --}}
                        <div>
                            <x-input-label for="fecha" :value="__('Fecha')" />
                            {{-- Input de fecha con los estilos de dark mode aplicados --}}
                            <x-text-input id="fecha" 
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                type="date" name="fecha" :value="old('fecha')" required />
                            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                        </div>

                        {{-- Hora --}}
                        <div>
                            <x-input-label for="hora" :value="__('Hora')" />
                            {{-- Input de hora con los estilos de dark mode aplicados --}}
                            <x-text-input id="hora" 
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                type="time" name="hora" :value="old('hora')" required />
                            <x-input-error :messages="$errors->get('hora')" class="mt-2" />
                        </div>

                        {{-- Botones de acción --}}
                        <div class="flex justify-start space-x-4 pt-4">
                            {{-- Usando el componente x-primary-button y el estilo azul que habías usado --}}
                            <x-primary-button class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Agendar Cita') }}
                            </x-primary-button>

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
    
    {{-- La sección de script se mantiene igual, ya que no necesita adaptación de estilo Tailwind --}}
    <script>
        document.getElementById('cliente_id').addEventListener('change', function () {
            let clienteId = this.value;

            // Almacenar el valor seleccionado actual antes de la actualización
            const oldMotoId = '{{ old('moto_id') }}';

            if (!clienteId) {
                document.getElementById('moto_id').innerHTML = '<option value="">Seleccione un cliente</option>';
                return;
            }

            // Llamada AJAX para obtener las motos
            fetch(`/api/clientes/${clienteId}/motos`)
                .then(response => response.json())
                .then(data => {
                    let motoSelect = document.getElementById('moto_id');
                    motoSelect.innerHTML = '';
                    motoSelect.disabled = false; // Habilitar si hay datos

                    if (data.length === 0) {
                        motoSelect.innerHTML = '<option value="">No hay motos registradas para este cliente</option>';
                        motoSelect.disabled = true;
                        return;
                    }

                    // Agregar una opción inicial
                    motoSelect.innerHTML = '<option value="">Seleccione una moto</option>';

                    data.forEach(moto => {
                        const isSelected = oldMotoId == moto.id_moto ? 'selected' : '';
                        motoSelect.innerHTML += `
                            <option value="${moto.id_moto}" ${isSelected}>
                                ${moto.placa} - ${moto.marca} ${moto.modelo}
                            </option>
                        `;
                    });
                })
                .catch(error => {
                    console.error('Error al cargar motos:', error);
                    let motoSelect = document.getElementById('moto_id');
                    motoSelect.innerHTML = '<option value="">Error al cargar las motos</option>';
                });
        });

        // Simular el evento 'change' en la carga inicial si hay un old('cliente_id')
        document.addEventListener('DOMContentLoaded', function() {
            const clienteIdField = document.getElementById('cliente_id');
            if (clienteIdField.value && '{{ old("moto_id") }}') {
                clienteIdField.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-app-layout>