{{-- <x-app-layout>
    Encabezado de la página consistente 
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Producto') }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">  Ancho ajustado a 3xl para consistencia
            
             Contenedor principal consistente 
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

                Encabezado de la tarjeta consistente 
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                    <h4>{{ __('Formulario de Edición de Producto') }}</h4>
                </div>

               Cuerpo del formulario
                <div class="p-6">
                    <form action="{{ route('productos.actualizar', $producto->id_producto) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                       NOMBRE (Deshabilitado, solo lectura) 
                        <div>
                            <x-input-label for="nombre" :value="__('Nombre (No modificable)')" />
                            
                            Campo hidden para asegurar que se envíe el valor 
                            <input type="hidden" name="nombre" value="{{ $producto->nombre }}">

                            <x-text-input id="nombre_display" type="text" 
                                class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 dark:text-gray-400"
                                value="{{ $producto->nombre }}" disabled />
                        </div>

                        CATEGORÍA 
                        <div>
                            <x-input-label for="categoria" :value="__('Categoría')" />
                            <x-text-input id="categoria" type="text" name="categoria" 
                                class="mt-1 block w-full"
                                :value="old('categoria', $producto->categoria)" />
                            <x-input-error :messages="$errors->get('categoria')" class="mt-2" />
                        </div>

                        PRECIO
                        <div>
                            <x-input-label for="precio" :value="__('Precio')" />
                            <x-text-input id="precio" type="number" name="precio" 
                                class="mt-1 block w-full"
                                :value="old('precio', $producto->precio)" min="0" required />
                            <x-input-error :messages="$errors->get('precio')" class="mt-2" />
                        </div>

                         CANTIDAD 
                        <div>
                            <x-input-label for="cantidad" :value="__('Cantidad')" />
                            <x-text-input id="cantidad" type="number" name="cantidad" 
                                class="mt-1 block w-full"
                                :value="old('cantidad', $producto->cantidad)" min="0" required />
                            <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
                        </div>

                         DESCRIPCIÓN 
                        <div>
                            <x-input-label for="descripcion" :value="__('Descripción')" />
                            <textarea id="descripcion" name="descripcion" rows="3"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                                focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600
                                rounded-md shadow-sm">{{ old('descripcion', $producto->descripcion) }}</textarea>
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>
                        
                         BOTONES
                        <div class="flex justify-start space-x-4 pt-4">

                            Botón azul para Guardar Cambios (consistente con el ejemplo de Cita)
                            <x-primary-button class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Guardar Cambios') }}
                            </x-primary-button>

                            Botón gris para Cancelar (consistente con el ejemplo de Cita)
                            <a href="{{ route('productos.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</x-app-layout> --}}