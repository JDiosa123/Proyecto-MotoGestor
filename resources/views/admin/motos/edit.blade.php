<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Moto') }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Contenedor principal de la tarjeta del formulario --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                
                {{-- Encabezado de la Tarjeta --}}
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                    <h4>{{ __('Formulario de Edición de Moto') }}</h4>
                </div>

                {{-- Cuerpo del Formulario --}}
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.motos.update', $moto->id_moto) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="placa" :value="__('Placa')" />
                            {{-- Se usa old() para repoblar en caso de error de validación --}}
                            <x-text-input id="placa" oninput="this.value = this.value.toUpperCase()" 
                                maxlength="6" class="block mt-1 w-full"
                                type="text" name="placa" value="{{ old('placa', $moto->placa) }}" required />
                            <x-input-error :messages="$errors->get('placa')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="marca" :value="__('Marca')" />
                            <x-text-input id="marca" class="block mt-1 w-full"
                                type="text" name="marca" value="{{ old('marca', $moto->marca) }}" required />
                            <x-input-error :messages="$errors->get('marca')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="modelo" :value="__('Modelo')" />
                            <x-text-input id="modelo" class="block mt-1 w-full"
                                type="text" name="modelo" value="{{ old('modelo', $moto->modelo) }}" required />
                            <x-input-error :messages="$errors->get('modelo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="cilindraje" :value="__('Cilindraje')" />
                            <x-text-input id="cilindraje" class="block mt-1 w-full"
                                type="number" name="cilindraje" value="{{ old('cilindraje', $moto->cilindraje) }}" required />
                            <x-input-error :messages="$errors->get('cilindraje')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="color" :value="__('Color')" />
                            <x-text-input id="color" class="block mt-1 w-full"
                                type="text" name="color" value="{{ old('color', $moto->color) }}" required />
                            <x-input-error :messages="$errors->get('color')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="cliente_id" :value="__('Cliente')" />

                            {{-- Select con estilos de dark mode para consistencia --}}
                            <select id="cliente_id" name="cliente_id"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id_cliente }}"
                                        {{-- Lógica para seleccionar el cliente actual --}}
                                        {{ old('cliente_id', $moto->cliente_id) == $cliente->id_cliente ? 'selected' : '' }}>
                                        {{ $cliente->nombre }} {{ $cliente->apellido }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                        </div>

                        {{-- Botones de acción con estilos detallados y alineados a la izquierda --}}
                        <div class="flex justify-start space-x-4 pt-4">
                            <x-primary-button class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Actualizar Moto
                            </x-primary-button>

                            <a href="{{ route('admin.motos.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>