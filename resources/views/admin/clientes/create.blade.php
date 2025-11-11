<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Crear Nuevo Cliente') }}
        </h1>
    </x-slot>

    <div class="p-6 max-w-xl mx-auto">
        <form method="POST" action="{{ route('admin.clientes.store') }}" class="space-y-4">
            @csrf

            <!-- Nombre -->
            <div>
                <x-input-label for="nombre" :value="__('Nombre')" />
                <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" required autofocus autocomplete="nombre" />
                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
            </div>

            <!-- Apellido -->
            <div>
                <x-input-label for="apellido" :value="__('Apellido')" />
                <x-text-input id="apellido" class="block mt-1 w-full" type="text" name="apellido" :value="old('apellido')" required autocomplete="apellido" />
                <x-input-error :messages="$errors->get('apellido')" class="mt-2" />
            </div>

            <!-- Documento -->
            <div>
                <x-input-label for="documento" :value="__('Documento')" />
                <x-text-input id="documento" class="block mt-1 w-full" type="text" name="documento" :value="old('documento')" required autocomplete="documento" />
                <x-input-error :messages="$errors->get('documento')" class="mt-2" />
            </div>

            <!-- Fecha de Nacimiento -->
            <div>
                <x-input-label for="fecha_nacimiento" :value="__('Fecha de Nacimiento')" />
                <x-text-input id="fecha_nacimiento" class="block mt-1 w-full" type="date" name="fecha_nacimiento" :value="old('fecha_nacimiento')" required />
                <x-input-error :messages="$errors->get('fecha_nacimiento')" class="mt-2" />
            </div>

            <!-- Dirección -->
            <div>
                <x-input-label for="direccion" :value="__('Dirección')" />
                <x-text-input id="direccion" class="block mt-1 w-full" type="text" name="direccion" :value="old('direccion')" required autocomplete="direccion" />
                <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
            </div>

            <!-- Ciudad -->
            <div>
                <x-input-label for="ciudad" :value="__('Ciudad')" />
                <x-text-input id="ciudad" class="block mt-1 w-full" type="text" name="ciudad" :value="old('ciudad')" required autocomplete="ciudad" />
                <x-input-error :messages="$errors->get('ciudad')" class="mt-2" />
            </div>

            <!-- Teléfono -->
            <div>
                <x-input-label for="telefono" :value="__('Teléfono')" />
                <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono')" required autocomplete="telefono" />
                <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Correo Electrónico')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Botón -->
            <div class="flex items-center justify-center space-x-4 mt-6">
                <x-primary-button>
                    {{ __('Guardar Cliente') }}
                </x-primary-button>
            </div><br>
        
            <div class="flex items-center justify-center space-x-4">
                <a href="{{ route('admin.clientes.index') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Cancelar') }}
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
