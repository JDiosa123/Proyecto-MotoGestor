<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Listado de Citas') }}
        </h2>
    </x-slot>

    <div class="py-6"> {{-- Se cambió py-8 por py-6 para consistencia --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $rol = Auth::user()->role;
            @endphp

            {{-- Botón 'Crear Cita' con estilo adaptado --}}
            <div class="flex justify-end mb-4"> {{-- Se cambió mb-6 por mb-4 para consistencia --}}
                @if (in_array($rol, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_ASSISTANT], true))
                <a href="{{ route('admin.citas.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{-- Se quitó el '+' para coincidir con el estilo del botón 'Crear Moto' --}}
                    Agendar Cita 
                </a>
                @elseif ($rol === \App\Models\User::ROLE_CLIENT)
                    <a href="{{ route('mis-citas.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-xs font-semibold uppercase">
                        Solicitar Cita
                    </a>
                @endif
            </div>

            {{-- Mensaje de éxito (adaptado del código 'Motos') --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-200 text-green-800 rounded-md dark:bg-green-800 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
                <div class="bg-gray-700 p-5 text-lg font-semibold text-white sm:rounded-t-lg">
                    <h4>Gestionar Citas</h4>
                </div>

                <div class="hidden md:block">
                    <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
                        <colgroup>
                            <col class="w-[19%]">
                            <col class="w-[20%]">
                            <col class="w-[14%]">
                            <col class="w-[10%]">
                            <col class="w-[9%]">
                            <col class="w-[14%]">
                            <col class="w-[14%]">
                        </colgroup>
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                @foreach (['Cliente', 'Moto', 'Mecánico', 'Fecha', 'Hora', 'Estado', 'Acciones'] as $encabezado)
                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                        {{ __($encabezado) }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @forelse ($citas as $cita)
                                @php
                                    $estadoClases = [
                                        'solicitada' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        'aceptada' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                        'atendida' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        'cancelada' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    ];
                                    $claseEstado = $estadoClases[$cita->estado];
                                    $dialogId = 'cita-accion-'.$cita->id;
                                @endphp
                                <tr class="align-top">
                                    <td class="break-words px-3 py-4 text-sm text-gray-700 dark:text-gray-200">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $cita->cliente?->nombre }} {{ $cita->cliente?->apellido }}</div>
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">C.C. {{ $cita->cliente?->documento }}</div>
                                    </td>
                                    <td class="break-words px-3 py-4 text-sm text-gray-700 dark:text-gray-200">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $cita->moto?->placa }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $cita->moto?->marca }} {{ $cita->moto?->modelo }} @if ($cita->moto?->color) ({{ $cita->moto->color }}) @endif</div>
                                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-300"><span class="font-medium">Servicio:</span> {{ filled($cita->servicio) ? $cita->servicio : '-' }}</div>
                                    </td>
                                    <td class="break-words px-3 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $cita->mecanico?->name ?? 'Sin asignar' }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($cita->hora)->format('g:i A') }}</td>
                                    <td class="break-words px-3 py-4 text-sm text-gray-700 dark:text-gray-200">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $claseEstado }}">{{ ucfirst($cita->estado) }}</span>
                                        @if ($cita->estado === 'cancelada' && filled($cita->motivo_cancelacion))
                                            <p class="mt-2 text-xs text-gray-600 dark:text-gray-300" title="{{ $cita->motivo_cancelacion }}">{{ \Illuminate\Support\Str::limit($cita->motivo_cancelacion, 60) }}</p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4">
                                        <div class="flex flex-col items-start gap-1.5">
                                            @if (in_array($rol, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_ASSISTANT], true))
                                                <a href="{{ route('admin.citas.edit', $cita->id) }}" class="inline-flex w-24 items-center justify-center rounded-md bg-yellow-500 px-2 py-1.5 text-xs font-semibold text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                                    {{ __('Editar') }}
                                                </a>
                                            @endif
                                            @if (in_array($rol, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_ASSISTANT], true) && $cita->estado === 'solicitada')
                                                <button type="button" data-open-appointment-dialog="{{ $dialogId }}" class="inline-flex w-24 items-center justify-center rounded-md bg-red-600 px-2 py-1.5 text-xs font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">Rechazar</button>
                                            @endif
                                            @if ($rol === \App\Models\User::ROLE_ADMIN)
                                                <form action="{{ route('admin.citas.destroy', $cita->id) }}" method="POST" onsubmit="return confirm('{{ __('¿Seguro que deseas eliminar esta cita?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex w-24 items-center justify-center rounded-md bg-red-700 px-2 py-1.5 text-xs font-semibold text-white hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500">{{ __('Eliminar') }}</button>
                                                </form>
                                            @endif
                                            @if ($rol === \App\Models\User::ROLE_MECHANIC && !in_array($cita->estado, ['atendida', 'cancelada'], true))
                                                <form action="{{ route('mecanico.citas.atender', $cita) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex w-24 items-center justify-center rounded-md bg-green-600 px-2 py-1.5 text-xs font-semibold text-white hover:bg-green-700">{{ __('Atender') }}</button>
                                                </form>
                                            @elseif ($rol === \App\Models\User::ROLE_CLIENT && !in_array($cita->estado, ['atendida', 'cancelada'], true))
                                                <button type="button" data-open-appointment-dialog="{{ $dialogId }}" class="inline-flex w-24 items-center justify-center rounded-md bg-red-600 px-2 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Cancelar cita</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay citas registradas.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 p-3 md:hidden">
                    @forelse ($citas as $cita)
                        @php
                            $estadoClases = [
                                'solicitada' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                'aceptada' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                'atendida' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                'cancelada' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                            ];
                            $claseEstado = $estadoClases[$cita->estado];
                            $dialogId = 'cita-accion-'.$cita->id;
                        @endphp
                        <article class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="break-words font-semibold text-gray-900 dark:text-gray-100">{{ $cita->cliente?->nombre }} {{ $cita->cliente?->apellido }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">C.C. {{ $cita->cliente?->documento }}</p>
                                </div>
                                <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $claseEstado }}">{{ ucfirst($cita->estado) }}</span>
                            </div>
                            <dl class="mt-4 grid grid-cols-2 gap-x-3 gap-y-3 text-sm">
                                <div class="min-w-0">
                                    <dt class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Moto / servicio</dt>
                                    <dd class="break-words font-medium text-gray-900 dark:text-gray-100">{{ $cita->moto?->placa }}</dd>
                                    <dd class="break-words text-gray-600 dark:text-gray-300">{{ $cita->moto?->marca }} {{ $cita->moto?->modelo }}</dd>
                                    <dd class="mt-1 break-words text-xs text-gray-600 dark:text-gray-300">{{ filled($cita->servicio) ? $cita->servicio : '-' }}</dd>
                                </div>
                                <div class="min-w-0">
                                    <dt class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Mecánico</dt>
                                    <dd class="break-words text-gray-800 dark:text-gray-200">{{ $cita->mecanico?->name ?? 'Sin asignar' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Fecha</dt>
                                    <dd class="text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Hora</dt>
                                    <dd class="text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($cita->hora)->format('g:i A') }}</dd>
                                </div>
                            </dl>
                            @if ($cita->estado === 'cancelada' && filled($cita->motivo_cancelacion))
                                <p class="mt-3 break-words text-xs text-gray-600 dark:text-gray-300" title="{{ $cita->motivo_cancelacion }}">Motivo: {{ \Illuminate\Support\Str::limit($cita->motivo_cancelacion, 60) }}</p>
                            @endif
                            <div class="mt-4 flex flex-wrap items-start gap-2 border-t border-gray-200 pt-3 dark:border-gray-700">
                                @if (in_array($rol, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_ASSISTANT], true))
                                    <a href="{{ route('admin.citas.edit', $cita->id) }}" class="inline-flex min-w-24 items-center justify-center rounded-md bg-yellow-500 px-3 py-2 text-xs font-semibold text-white hover:bg-yellow-600">{{ __('Editar') }}</a>
                                @endif
                                @if (in_array($rol, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_ASSISTANT], true) && $cita->estado === 'solicitada')
                                    <button type="button" data-open-appointment-dialog="{{ $dialogId }}" class="inline-flex min-w-24 items-center justify-center rounded-md bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Rechazar</button>
                                @endif
                                @if ($rol === \App\Models\User::ROLE_ADMIN)
                                    <form action="{{ route('admin.citas.destroy', $cita->id) }}" method="POST" onsubmit="return confirm('{{ __('¿Seguro que deseas eliminar esta cita?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex min-w-24 items-center justify-center rounded-md bg-red-700 px-3 py-2 text-xs font-semibold text-white hover:bg-red-800">{{ __('Eliminar') }}</button>
                                    </form>
                                @endif
                                @if ($rol === \App\Models\User::ROLE_MECHANIC && !in_array($cita->estado, ['atendida', 'cancelada'], true))
                                    <form action="{{ route('mecanico.citas.atender', $cita) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex min-w-24 items-center justify-center rounded-md bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700">{{ __('Atender') }}</button>
                                    </form>
                                @elseif ($rol === \App\Models\User::ROLE_CLIENT && !in_array($cita->estado, ['atendida', 'cancelada'], true))
                                    <button type="button" data-open-appointment-dialog="{{ $dialogId }}" class="inline-flex min-w-24 items-center justify-center rounded-md bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Cancelar cita</button>
                                @endif
                            </div>
                        </article>
                    @empty
                        <p class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay citas registradas.') }}</p>
                    @endforelse
                </div>

                @foreach ($citas as $cita)
                    @if ((in_array($rol, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_ASSISTANT], true) && $cita->estado === 'solicitada') || ($rol === \App\Models\User::ROLE_CLIENT && !in_array($cita->estado, ['atendida', 'cancelada'], true)))
                        @php
                            $dialogId = 'cita-accion-'.$cita->id;
                            $reopenDialog = (string) request('cita_accion') === (string) $cita->id && $errors->has('motivo_cancelacion');
                        @endphp
                        <dialog id="{{ $dialogId }}" data-appointment-dialog data-reopen="{{ $reopenDialog ? 'true' : 'false' }}" class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-xl bg-white p-0 text-gray-900 shadow-2xl backdrop:bg-black/60 dark:bg-gray-800 dark:text-gray-100">
                            @if (in_array($rol, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_ASSISTANT], true))
                                <form action="{{ route('admin.citas.reject', $cita) }}" method="POST" class="space-y-4 p-5">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <h2 class="text-lg font-semibold">Rechazar cita</h2>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Indica el motivo del rechazo (de 5 a 255 caracteres).</p>
                                    </div>
                                    <div>
                                        <label for="motivo-rechazo-{{ $cita->id }}" class="mb-1 block text-sm font-medium">Motivo</label>
                                        <textarea id="motivo-rechazo-{{ $cita->id }}" name="motivo_cancelacion" required minlength="5" maxlength="255" rows="4" data-character-count-field class="w-full rounded-md border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('motivo_cancelacion') }}</textarea>
                                        <div class="mt-1 flex items-start justify-between gap-3">
                                            <x-input-error :messages="$errors->get('motivo_cancelacion')" />
                                            <span class="shrink-0 text-xs text-gray-500 dark:text-gray-400"><span data-character-count>0</span>/255</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" data-close-appointment-dialog class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Cancelar</button>
                                        <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">Confirmar rechazo</button>
                                    </div>
                                </form>
                            @else
                                <form action="{{ route('mis-citas.cancelar', $cita) }}" method="POST" class="space-y-4 p-5">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <h2 class="text-lg font-semibold">Cancelar cita</h2>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Indica el motivo de cancelación.</p>
                                    </div>
                                    <div>
                                        <label for="motivo-cancelacion-{{ $cita->id }}" class="mb-1 block text-sm font-medium">Motivo</label>
                                        <input id="motivo-cancelacion-{{ $cita->id }}" name="motivo_cancelacion" required maxlength="1000" value="{{ old('motivo_cancelacion') }}" data-character-count-field class="w-full rounded-md border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                        <div class="mt-1 flex items-start justify-between gap-3">
                                            <x-input-error :messages="$errors->get('motivo_cancelacion')" />
                                            <span class="shrink-0 text-xs text-gray-500 dark:text-gray-400"><span data-character-count>0</span>/1000</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" data-close-appointment-dialog class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Cancelar</button>
                                        <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">Confirmar cancelación</button>
                                    </div>
                                </form>
                            @endif
                        </dialog>
                    @endif
                @endforeach
            </div>

            <script>
                document.querySelectorAll('[data-open-appointment-dialog]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const dialog = document.getElementById(button.dataset.openAppointmentDialog);
                        const url = new URL(window.location.href);
                        url.searchParams.set('cita_accion', dialog.id.replace('cita-accion-', ''));
                        window.history.replaceState({}, '', url);
                        dialog.showModal();
                    });
                });

                document.querySelectorAll('[data-close-appointment-dialog]').forEach((button) => {
                    button.addEventListener('click', () => button.closest('dialog').close());
                });

                document.querySelectorAll('[data-character-count-field]').forEach((field) => {
                    const counter = field.closest('div').querySelector('[data-character-count]');
                    const updateCount = () => counter.textContent = field.value.length;
                    field.addEventListener('input', updateCount);
                    updateCount();
                });

                document.querySelectorAll('dialog[data-appointment-dialog]').forEach((dialog) => {
                    dialog.addEventListener('close', () => {
                        const url = new URL(window.location.href);
                        if (url.searchParams.get('cita_accion') === dialog.id.replace('cita-accion-', '')) {
                            url.searchParams.delete('cita_accion');
                            window.history.replaceState({}, '', url);
                        }
                    });
                    if (dialog.dataset.reopen === 'true') {
                        dialog.showModal();
                    }
                });
            </script>
            
        </div>
    </div>
</x-app-layout>