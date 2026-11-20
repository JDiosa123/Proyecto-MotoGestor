<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Agendar Nueva Cita') }}
        </h1>
    </x-slot>

    @php
        $servicioSeleccionado = old('servicio_id', $servicios->first()?->id);
        $sinMotos = ($esCliente ?? false) && $motos->isEmpty();
        $sinDias = empty($diasDisponiblesPorServicio[$servicioSeleccionado] ?? []);
        $servicioInicial = $servicios->firstWhere('id', (int) $servicioSeleccionado);
        $horaInicialCarbon = $servicioInicial?->hora_ingreso
            ? \Carbon\Carbon::parse($servicioInicial->hora_ingreso, config('app.timezone'))
            : null;
        $horaInicial = $horaInicialCarbon
            ? $horaInicialCarbon->format('g:i')
            : null;
        $periodoInicial = $horaInicialCarbon
            ? ((int) $horaInicialCarbon->format('G') < 12 ? 'a. m.' : 'p. m.')
            : null;
    @endphp

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                <div class="bg-gray-700 p-6 text-lg font-semibold text-white sm:rounded-t-lg">
                    <h4>{{ __('Formulario de Agendamiento de Cita') }}</h4>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ ($esCliente ?? false) ? route('mis-citas.store') : route('admin.citas.store') }}" class="space-y-6">
                        @csrf

                        @if ($esCliente ?? false)
                            <input type="hidden" id="cliente_id" name="cliente_id" value="{{ $clientes->first()->id }}">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Cliente: {{ $clientes->first()->nombre }} {{ $clientes->first()->apellido }}
                            </p>
                        @else
                            <div>
                                <x-input-label for="cliente_id" :value="__('Cliente')" />
                                <select id="cliente_id" name="cliente_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required>
                                    <option value="">{{ __('Seleccione un cliente') }}</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                            C.C {{ $cliente->documento }} - {{ $cliente->nombre }} {{ $cliente->apellido }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                            </div>
                        @endif

                        <div>
                            <x-input-label for="moto_id" :value="__('Moto')" />
                            <select name="moto_id" id="moto_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required {{ $sinMotos ? 'disabled' : '' }}>
                                @if ($esCliente ?? false)
                                    @if ($motos->isEmpty())
                                        <option value="">No tienes motos registradas. Pide al taller que registre tu moto</option>
                                    @else
                                        @if ($motos->count() > 1)
                                            <option value="">{{ __('Seleccione una moto') }}</option>
                                        @endif
                                        @foreach ($motos as $moto)
                                            <option value="{{ $moto->id }}" {{ $motos->count() === 1 || old('moto_id') == $moto->id ? 'selected' : '' }}>
                                                {{ $moto->placa }} - {{ $moto->marca }} {{ $moto->modelo }}
                                            </option>
                                        @endforeach
                                    @endif
                                @else
                                    <option value="">{{ __('Seleccione primero un cliente') }}</option>
                                @endif
                            </select>
                            @if ($sinMotos)
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">No tienes motos registradas. Pide al taller que registre tu moto</p>
                            @endif
                            <x-input-error :messages="$errors->get('moto_id')" class="mt-2" />
                        </div>

                        @unless ($esCliente ?? false)
                            <div>
                                <x-input-label for="mecanico_id" :value="__('Mecánico Encargado')" />
                                <select name="mecanico_id" id="mecanico_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="">{{ __('Sin asignar') }}</option>
                                    @foreach ($mecanicos as $mecanico)
                                        <option value="{{ $mecanico->id }}" {{ old('mecanico_id') == $mecanico->id ? 'selected' : '' }}>
                                            {{ $mecanico->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('mecanico_id')" class="mt-2" />
                            </div>
                        @endunless

                        <div>
                            <x-input-label for="servicio_id" :value="__('Servicio solicitado')" />
                            <select id="servicio_id" name="servicio_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required>
                                <option value="">{{ __('Seleccione un servicio') }}</option>
                                @foreach ($servicios as $servicio)
                                    <option value="{{ $servicio->id }}"
                                        data-dias="{{ json_encode($diasDisponiblesPorServicio[$servicio->id] ?? []) }}"
                                        data-hora="{{ $servicio->hora_ingreso ? substr($servicio->hora_ingreso, 0, 5) : '' }}"
                                        {{ (string) $servicioSeleccionado === (string) $servicio->id ? 'selected' : '' }}>
                                        {{ $servicio->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('servicio_id')" class="mt-2" />
                            @if ($servicios->isEmpty())
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">No hay servicios agendables disponibles.</p>
                            @endif
                        </div>

                        <p id="hora-ingreso" class="text-sm text-gray-600 dark:text-gray-300" aria-live="polite">
                            @if ($horaInicial)
                                Lleva tu moto a partir de las {{ $horaInicial }} {{ $periodoInicial }}
                            @endif
                        </p>

                        <div>
                            <x-input-label for="fecha" :value="__('Fecha')" />
                            <select id="fecha" name="fecha" data-old-date="{{ old('fecha') }}" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required>
                                <option value="">{{ __('Seleccione un día disponible') }}</option>
                                @foreach ($diasDisponiblesPorServicio[$servicioSeleccionado] ?? [] as $dia)
                                    <option value="{{ $dia['fecha'] }}" {{ old('fecha') === $dia['fecha'] ? 'selected' : '' }}>
                                        {{ $dia['etiqueta'] }} ({{ $dia['cupos'] }} {{ $dia['cupos'] === 1 ? 'cupo' : 'cupos' }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                            <p id="sin-dias-disponibles" class="mt-2 {{ $sinDias ? '' : 'hidden' }} text-sm text-red-600 dark:text-red-400">
                                No hay días disponibles para este servicio en el rango permitido.
                            </p>
                        </div>

                        <div>
                            <x-input-label for="observaciones" :value="__('Observaciones o síntomas (opcional)')" />
                            <textarea id="observaciones" name="observaciones" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('observaciones') }}</textarea>
                            <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
                        </div>

                        <div class="flex justify-start space-x-4 pt-4">
                            <x-primary-button id="submit-cita" class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition" :disabled="$sinMotos || $sinDias || $servicios->isEmpty()">
                                {{ __('Agendar Cita') }}
                            </x-primary-button>
                            <a href="{{ ($esCliente ?? false) ? route('mis-citas.index') : route('admin.citas.index') }}" class="inline-flex items-center rounded-md bg-gray-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-600">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @unless ($esCliente ?? false)
        <script>
            const clienteField = document.getElementById('cliente_id');
            const motoSelect = document.getElementById('moto_id');
            const loadMotos = function () {
                if (!clienteField || !motoSelect) return;
                const clienteId = clienteField.value;
                const oldMotoId = @json(old('moto_id'));

                if (!clienteId) {
                    motoSelect.innerHTML = '<option value="">Seleccione un cliente</option>';
                    motoSelect.disabled = false;
                    return;
                }

                fetch(`/api/clientes/${clienteId}/motos`)
                    .then(response => response.json())
                    .then(data => {
                        motoSelect.innerHTML = '';
                        motoSelect.disabled = false;

                        if (data.length === 0) {
                            motoSelect.innerHTML = '<option value="">No hay motos registradas para este cliente</option>';
                            motoSelect.disabled = true;
                            return;
                        }

                        motoSelect.innerHTML = '<option value="">Seleccione una moto</option>';
                        data.forEach(moto => {
                            const option = document.createElement('option');
                            option.value = moto.id;
                            option.textContent = `${moto.placa} - ${moto.marca} ${moto.modelo}`;
                            option.selected = oldMotoId == moto.id;
                            motoSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar motos:', error);
                        motoSelect.innerHTML = '<option value="">Error al cargar las motos</option>';
                    });
            };

            if (clienteField?.tagName === 'SELECT') {
                clienteField.addEventListener('change', loadMotos);
                if (clienteField.value) loadMotos();
            }
        </script>
    @endunless

    <script>
        const servicioSelect = document.getElementById('servicio_id');
        const fechaSelect = document.getElementById('fecha');
        const noHayDias = document.getElementById('sin-dias-disponibles');
        const horaIngreso = document.getElementById('hora-ingreso');
        const submitButton = document.getElementById('submit-cita');
        const motosDisponibles = {{ $sinMotos ? 'false' : 'true' }};

        const formatearHora = (hora) => {
            if (!hora) return '';
            const [hora24, minutos] = hora.split(':').map(Number);
            const hora12 = hora24 % 12 || 12;
            return `${hora12}:${String(minutos).padStart(2, '0')} ${hora24 < 12 ? 'a. m.' : 'p. m.'}`;
        };

        const actualizarFechas = () => {
            const option = servicioSelect.selectedOptions[0];
            const dias = option?.dataset.dias ? JSON.parse(option.dataset.dias) : [];
            const fechaAnterior = fechaSelect.dataset.oldDate;
            fechaSelect.replaceChildren(new Option('Seleccione un día disponible', ''));

            dias.forEach((dia) => {
                const label = `${dia.etiqueta} (${dia.cupos} ${dia.cupos === 1 ? 'cupo' : 'cupos'})`;
                const dayOption = new Option(label, dia.fecha);
                dayOption.selected = dia.fecha === fechaAnterior;
                fechaSelect.add(dayOption);
            });

            const sinDias = dias.length === 0;
            noHayDias.classList.toggle('hidden', !sinDias);
            horaIngreso.textContent = option?.dataset.hora
                ? `Lleva tu moto a partir de las ${formatearHora(option.dataset.hora)}`
                : '';
            submitButton.disabled = sinDias || !motosDisponibles || !option?.value;
            fechaSelect.dataset.oldDate = '';
        };

        servicioSelect.addEventListener('change', actualizarFechas);
        actualizarFechas();
    </script>
</x-app-layout>
