<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Nuevo Usuario') }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Contenedor principal de la tarjeta del formulario --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                
                {{-- Encabezado de la Tarjeta --}}
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                    <h4>{{ __('Formulario de Registro de Usuario') }}</h4>
                </div>

                {{-- Cuerpo del Formulario --}}
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Nombre')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Correo')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="role" :value="__('Rol')" />

                            {{-- Select con estilos de dark mode para consistencia --}}
                            <select id="role" name="role" 
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                required>
                                <option value="">Seleccionar rol</option>
                                @foreach (\App\Models\User::ROLE_LABELS as $role => $label)
                                    <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div id="cliente-field" class="{{ old('role') === \App\Models\User::ROLE_CLIENT ? '' : 'hidden' }}">
                            <x-input-label for="cliente_id" :value="__('Ficha de cliente')" />
                            <select id="cliente_id" name="cliente_id"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                <option value="">Seleccionar ficha</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->documento }} - {{ $cliente->nombre }} {{ $cliente->apellido }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Estado')" />
                            {{-- Select con estilos de dark mode para consistencia --}}
                            <select id="status" name="status" 
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                required>
                                <option value="" disabled selected>Seleccionar Estado</option>
                                <option value="activo" {{ old('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Contraseña')" />
                            <div class="mt-1 flex">
                                <x-text-input id="password" class="block w-full rounded-r-none"
                                    type="password"
                                    name="password"
                                    required autocomplete="new-password" />
                                <button type="button" data-password-toggle="password"
                                    aria-label="Mostrar contraseña" aria-pressed="false"
                                    class="rounded-r-md border border-l-0 border-gray-300 bg-gray-100 px-3 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                    Mostrar
                                </button>
                            </div>
                            <div id="password-requirements" class="mt-3 space-y-2 text-sm" aria-live="polite">
                                <p class="font-medium text-gray-700 dark:text-gray-200">Requisitos de contraseña</p>
                                <ul class="space-y-1">
                                    <li data-password-rule="length" class="text-gray-500"><span aria-hidden="true">✗</span> Al menos 8 caracteres</li>
                                    <li data-password-rule="uppercase" class="text-gray-500"><span aria-hidden="true">✗</span> Una letra mayúscula</li>
                                    <li data-password-rule="lowercase" class="text-gray-500"><span aria-hidden="true">✗</span> Una letra minúscula</li>
                                    <li data-password-rule="number" class="text-gray-500"><span aria-hidden="true">✗</span> Un número</li>
                                    <li data-password-rule="special" class="text-gray-500"><span aria-hidden="true">✗</span> Un carácter especial (@$!%*?&)</li>
                                </ul>
                                <p>Seguridad de la contraseña: <span id="password-strength" class="font-semibold text-red-600">Débil</span></p>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confrimar Contraseña')" />
                            <div class="mt-1 flex">
                                <x-text-input id="password_confirmation" class="block w-full rounded-r-none"
                                    type="password"
                                    name="password_confirmation" required autocomplete="new-password" />
                                <button type="button" data-password-toggle="password_confirmation"
                                    aria-label="Mostrar confirmación de contraseña" aria-pressed="false"
                                    class="rounded-r-md border border-l-0 border-gray-300 bg-gray-100 px-3 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                    Mostrar
                                </button>
                            </div>
                            <p id="password-match" class="mt-2 text-sm text-gray-500" aria-live="polite">Escribe ambas contraseñas para comprobar si coinciden.</p>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        {{-- Botones de acción con estilos detallados y alineados a la izquierda --}}
                        <div class="flex justify-start space-x-4 pt-4">
                            <x-primary-button class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Guardar Usuario') }}
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
    <script>
        document.getElementById('role').addEventListener('change', function () {
            document.getElementById('cliente-field').classList.toggle('hidden', this.value !== 'cliente');
        });

        (() => {
            const password = document.getElementById('password');
            const confirmation = document.getElementById('password_confirmation');
            const strength = document.getElementById('password-strength');
            const matchMessage = document.getElementById('password-match');

            const updatePasswordFeedback = () => {
                const value = password.value;
                const checks = {
                    length: [...value].length >= 8,
                    uppercase: /[A-Z]/.test(value),
                    lowercase: /[a-z]/.test(value),
                    number: /\d/.test(value),
                    special: /[@$!%*?&]/.test(value),
                };

                Object.entries(checks).forEach(([rule, passed]) => {
                    const item = document.querySelector(`[data-password-rule="${rule}"]`);
                    item.classList.toggle('text-green-600', passed);
                    item.classList.toggle('text-red-600', value.length > 0 && !passed);
                    item.classList.toggle('text-gray-500', !value);
                    item.querySelector('span').textContent = passed ? '✓' : '✗';
                });

                const score = Object.values(checks).filter(Boolean).length;
                strength.textContent = score >= 5 ? 'Fuerte' : score >= 3 ? 'Media' : 'Débil';
                strength.classList.toggle('text-green-600', score >= 5);
                strength.classList.toggle('text-yellow-600', score >= 3 && score < 5);
                strength.classList.toggle('text-red-600', score < 3);

                if (!confirmation.value) {
                    matchMessage.textContent = 'Escribe ambas contraseñas para comprobar si coinciden.';
                    matchMessage.className = 'mt-2 text-sm text-gray-500';
                } else if (value === confirmation.value) {
                    matchMessage.textContent = '✓ Las contraseñas coinciden.';
                    matchMessage.className = 'mt-2 text-sm text-green-600';
                } else {
                    matchMessage.textContent = '✗ Las contraseñas no coinciden.';
                    matchMessage.className = 'mt-2 text-sm text-red-600';
                }
            };

            password.addEventListener('input', updatePasswordFeedback);
            confirmation.addEventListener('input', updatePasswordFeedback);
            document.querySelectorAll('[data-password-toggle]').forEach((button) => {
                button.addEventListener('click', () => {
                    const input = document.getElementById(button.dataset.passwordToggle);
                    const visible = input.type === 'password';
                    input.type = visible ? 'text' : 'password';
                    button.textContent = visible ? 'Ocultar' : 'Mostrar';
                    button.setAttribute('aria-pressed', String(visible));
                    button.setAttribute('aria-label', `${visible ? 'Ocultar' : 'Mostrar'} ${input.id === 'password' ? 'contraseña' : 'confirmación de contraseña'}`);
                });
            });

            updatePasswordFeedback();
        })();
    </script>
</x-app-layout>