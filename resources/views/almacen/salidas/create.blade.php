<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Salida') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                
                {{-- Encabezado de la Tarjeta con estilo consistente (Rojo) --}}
                <div class="p-6 bg-red-600 text-white font-extrabold text-xl sm:rounded-t-lg border-b-2 border-red-700">
                    Registrar Salida de Inventario
                </div>

                <div class="p-6">

                    {{-- Mensajes de Error de Sesión --}}
                    @if(session('error'))
                        <div class="mb-4 p-3 bg-red-100 dark:bg-red-800 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-100 rounded-lg font-medium">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Errores de Validación --}}
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 dark:bg-red-800 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-100 rounded-lg">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('salidas.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                            {{-- Campo Producto --}}
                            <div class="col-span-full md:col-span-6">
                                <label for="id_producto" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">
                                    Producto
                                </label>

                                <select name="id_producto" id="id_producto"
                                    class="w-full border-gray-300 dark:border-gray-700 
                                    dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500
                                    @error('id_producto') border-red-500 @enderror"
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
                                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Campo Cantidad --}}
                            <div class="col-span-full md:col-span-3">
                                <label for="cantidad" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">
                                    Cantidad
                                </label>

                                <input id="cantidad" type="number" name="cantidad" min="1"
                                    value="{{ old('cantidad', 1) }}"
                                    class="w-full border-gray-300 dark:border-gray-700 
                                    dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500
                                    @error('cantidad') border-red-500 @enderror"
                                    required>

                                @error('cantidad')
                                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Campo Fecha (Opcional, si lo usas) --}}
                            <div class="col-span-full md:col-span-3">
                                <label for="fecha" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">
                                    Fecha (Opcional)
                                </label>

                                <input id="fecha" type="date" name="fecha"
                                    value="{{ old('fecha') }}"
                                    class="w-full border-gray-300 dark:border-gray-700 
                                    dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500
                                    @error('fecha') border-red-500 @enderror">

                                @error('fecha')
                                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Campo Descripción --}}
                            <div class="col-span-full">
                                <label for="descripcion" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">
                                    Descripción (Motivo de la salida)
                                </label>

                                <textarea id="descripcion" name="descripcion" rows="3"
                                    class="w-full border-gray-300 dark:border-gray-700 
                                    dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500
                                    @error('descripcion') border-red-500 @enderror"
                                    placeholder="Detalles sobre el motivo de la salida del inventario.">{{ old('descripcion') }}</textarea>

                                @error('descripcion')
                                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Botones de Acción --}}
                            <div class="col-span-full pt-4 flex justify-end gap-3 border-t dark:border-gray-700">

                                <a href="{{ route('salidas.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg 
                                    font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 transition ease-in-out duration-150 shadow-sm">
                                    Cancelar
                                </a>

                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
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