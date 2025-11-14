<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Nuevo Cliente') }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Contenedor principal de la tarjeta del formulario --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                
                {{-- Encabezado de la Tarjeta --}}
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                    <h4>{{ __('Formulario de Registro de Cliente') }}</h4>
                </div>

                {{-- Cuerpo del Formulario --}}
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.clientes.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="nombre" :value="__('Nombre')" />
                            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" required autofocus autocomplete="nombre" />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="apellido" :value="__('Apellido')" />
                            <x-text-input id="apellido" class="block mt-1 w-full" type="text" name="apellido" :value="old('apellido')" required autocomplete="apellido" />
                            <x-input-error :messages="$errors->get('apellido')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="documento" :value="__('Documento')" />
                            <x-text-input id="documento" class="block mt-1 w-full" type="text" name="documento" :value="old('documento')" required autocomplete="documento" />
                            <x-input-error :messages="$errors->get('documento')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="fecha_nacimiento" :value="__('Fecha de Nacimiento')" />
                            {{-- Se asegura que el campo de fecha mantenga los estilos de dark mode --}}
                            <x-text-input id="fecha_nacimiento" 
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                type="date" name="fecha_nacimiento" :value="old('fecha_nacimiento')" required />
                            <x-input-error :messages="$errors->get('fecha_nacimiento')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="direccion" :value="__('Dirección')" />
                            <x-text-input id="direccion" class="block mt-1 w-full" type="text" name="direccion" :value="old('direccion')" required autocomplete="direccion" />
                            <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="ciudad" :value="__('Ciudad')" />
                            <x-text-input id="ciudad" class="block mt-1 w-full" type="text" name="ciudad" :value="old('ciudad')" required autocomplete="ciudad" />
                            <x-input-error :messages="$errors->get('ciudad')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="telefono" :value="__('Teléfono')" />
                            <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono')" required autocomplete="telefono" />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Correo Electrónico')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Botones de acción con estilos detallados y alineados a la izquierda --}}
                        <div class="flex justify-start space-x-4 pt-4">
                            <x-primary-button class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Guardar Cliente') }}
                            </x-primary-button>

                            <a href="{{ route('admin.clientes.index') }}" 
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