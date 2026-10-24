<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel del Mecánico') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-6 shadow-sm sm:rounded-lg">
                <p class="text-gray-900 dark:text-gray-100">Consulta únicamente tus citas asignadas y márcalas atendidas al finalizar el servicio.</p>
                <a class="mt-4 inline-flex rounded-md bg-blue-600 px-4 py-2 text-white" href="{{ route('mecanico.citas.index') }}">
                    Ver mis citas
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
