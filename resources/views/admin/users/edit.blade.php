<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Usuario') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Contenedor principal de la tarjeta del formulario --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                
                {{-- Encabezado de la Tarjeta --}}
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                    <h4>{{ __('Formulario de Edición de Usuario') }}</h4>
                </div>

                {{-- Cuerpo del Formulario --}}
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                        @csrf
                        @method('PUT') 

                        <div>
                            <x-input-label for="name" :value="__('Nombre')" />
                            {{-- Se usa old() para repoblar en caso de error de validación --}}
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Correo')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="role" :value="__('Rol')" />
                            
                            {{-- Select con estilos de dark mode para consistencia --}}
                            <select id="role" name="role" 
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                required>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="mecanico" {{ old('role', $user->role) == 'mecanico' ? 'selected' : '' }}>Mecánico</option>
                                <option value="almacenista" {{ old('role', $user->role) == 'almacenista' ? 'selected' : '' }}>Almacenista</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Estado')" />
                            
                            {{-- Select con estilos de dark mode para consistencia --}}
                            <select id="status" name="status" 
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                required>
                                <option value="activo" {{ old('status', $user->status) == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('status', $user->status) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        {{-- Campos de Contraseña (Opcional) --}}
                        <div>
                            <x-input-label for="password" :value="__('Nueva Contraseña (Opcional)')" />
                            <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirmar Nueva Contraseña')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" 
                                autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        {{-- Botones de acción con estilos detallados y alineados a la izquierda --}}
                        <div class="flex justify-start space-x-4 pt-4">
                            <x-primary-button class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Actualizar Usuario') }}
                            </x-primary-button>

                            <a href="{{ route('admin.users.index') }}" 
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