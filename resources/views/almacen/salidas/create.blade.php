<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Salida') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                
                
                <div class="p-6 bg-red-600 text-white font-semibold text-lg sm:rounded-t-lg">
                    Registrar Salida
                </div>

                <div class="p-6">

                    
                    @if(session('error'))
                        <div class="mb-4 p-3 bg-red-100 border border-red-200 text-red-800 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 border border-red-200 text-red-800 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('salidas.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                            
                            <div class="col-span-full md:col-span-6">
                                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Producto</label>

                                <select name="id_producto"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 
                                    dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm @error('id_producto') border-red-500 @enderror"
                                    required>

                                    <option value="">Seleccionar producto</option>

                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id_producto }}"
                                            {{ old('id_producto') == $producto->id_producto ? 'selected' : '' }}>
                                            {{ $producto->nombre }} (Stock: {{ $producto->cantidad }})
                                        </option>
                                    @endforeach

                                </select>

                                @error('id_producto')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            
                            <div class="col-span-full md:col-span-3">
                                <label for="cantidad" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Cantidad</label>

                                <input id="cantidad" type="number" name="cantidad" min="1"
                                    value="{{ old('cantidad', 1) }}"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 
                                    dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm @error('cantidad') border-red-500 @enderror"
                                    required>

                                @error('cantidad')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            
                            <div class="col-span-full md:col-span-12">
                                <label for="descripcion" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Descripción</label>

                                <input id="descripcion" type="text" name="descripcion"
                                    value="{{ old('descripcion') }}"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 
                                    dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm @error('descripcion') border-red-500 @enderror"
                                    placeholder="Opcional">

                                @error('descripcion')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            
                            <div class="col-span-full mt-3 flex justify-end gap-2">

                                <a href="{{ route('salidas.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 rounded-md 
                                    font-semibold text-sm text-gray-700 hover:bg-gray-400">
                                    Cancelar
                                </a>

                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md 
                                    font-semibold text-sm hover:bg-red-700">
                                    Registrar Salida
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
