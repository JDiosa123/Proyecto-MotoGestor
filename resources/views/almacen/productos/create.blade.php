<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Crear Producto') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg">
                    {{ __('Formulario de Producto') }}
                </div>

                <div class="p-6">
                    <form action="{{ route('productos.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="nombre" :value="__('Nombre')" />
                            <x-text-input id="nombre" type="text" name="nombre" class="mt-1 block w-full"
                                :value="old('nombre')" required />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="categoria" :value="__('Categoría')" />
                            <x-text-input id="categoria" type="text" name="categoria" class="mt-1 block w-full"
                                :value="old('categoria')" />
                            <x-input-error :messages="$errors->get('categoria')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="precio" :value="__('Precio')" />
                            <x-text-input id="precio" type="number" step="0.01" min="0" name="precio" class="mt-1 block w-full"
                                :value="old('precio')" required />
                            <x-input-error :messages="$errors->get('precio')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="descripcion" :value="__('Descripción')" />
                            <textarea id="descripcion" name="descripcion" rows="3"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{ old('descripcion') }}</textarea>
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>

                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            El producto inicia con stock 0. Registra una entrada para añadir unidades.
                        </p>

                        <div class="flex gap-4">
                            <x-primary-button>{{ __('Crear producto') }}</x-primary-button>
                            <a href="{{ route('productos.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
