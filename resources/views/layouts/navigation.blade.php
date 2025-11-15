<nav x-data="{ open: false }" class="bg-[#A7C9C9] border-b-4 border-blue-400 shadow-xl">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-blue-400" />
                    </a>
                </div>

                @php $user = Auth::user(); @endphp

                <!-- Desktop Navigation -->
                <div class="hidden sm:flex sm:ms-10 space-x-6">
                    @if ($user->role === 'admin')

                        <x-nav-link :href="route('admin.citas.index')" :active="request()->routeIs('admin.citas.*')"
                            class="text-white hover:text-[#3b82f6]">
                            Citas
                        </x-nav-link>

                        <x-nav-link :href="route('inventario.index')" :active="request()->routeIs('inventario.*')"
                            class="text-white hover:text-[#3b82f6]">
                            Inventario
                        </x-nav-link>

                        <x-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.*')"
                            class="text-white hover:text-[#3b82f6]">
                            Productos
                        </x-nav-link>

                        <x-nav-link :href="route('admin.motos.index')" :active="request()->routeIs('admin.motos.*')"
                            class="text-white hover:text-[#3b82f6]">
                            Motos
                        </x-nav-link>

                        <x-nav-link :href="route('admin.clientes.index')" :active="request()->routeIs('admin.clientes.*')"
                            class="text-white hover:text-[#3b82f6]">
                            Clientes
                        </x-nav-link>

                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')"
                            class="text-white hover:text-[#3b82f6]">
                            Usuarios
                        </x-nav-link>

                    @elseif ($user->role === 'almacenista')

                        <x-nav-link :href="route('inventario.index')" :active="request()->routeIs('inventario.*')"
                            class="text-white hover:text-[#3b82f6]">
                            Inventario
                        </x-nav-link>

                        <x-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.*')"
                            class="text-white hover:text-[#3b82f6]">
                            Productos
                        </x-nav-link>

                    @elseif ($user->role === 'mecanico')

                        <x-nav-link :href="route('admin.citas.index')" :active="request()->routeIs('admin.citas.*')"
                            class="text-white hover:text-[#3b82f6]">
                            Citas
                        </x-nav-link>

                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm 
                            leading-4 font-medium rounded-full text-white hover:text-[#3b82f6] 
                            bg-gray-700 transition ease-in-out duration-150 shadow-md">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                class="text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Cerrar Sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Menu Button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-300 
                        hover:text-[#3b82f6] hover:bg-gray-700 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden bg-gray-700 border-t border-gray-600">
        <div class="pt-2 pb-3 space-y-1">

            @if ($user->role === 'admin')
                <x-responsive-nav-link :href="route('admin.citas.index')" class="text-white hover:bg-gray-600 hover:text-[#3b82f6]">
                    Citas
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('inventario.index')" class="text-white hover:bg-gray-600 hover:text-[#3b82f6]">
                    Inventario
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('productos.index')" class="text-white hover:bg-gray-600 hover:text-[#3b82f6]">
                    Productos
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.motos.index')" class="text-white hover:bg-gray-600 hover:text-[#3b82f6]">
                    Motos
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.clientes.index')" class="text-white hover:bg-gray-600 hover:text-[#3b82f6]">
                    Clientes
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.users.index')" class="text-white hover:bg-gray-600 hover:text-[#3b82f6]">
                    Usuarios
                </x-responsive-nav-link>

            @elseif ($user->role === 'almacenista')

                <x-responsive-nav-link :href="route('inventario.index')" class="text-white hover:bg-gray-600 hover:text-[#3b82f6]">
                    Inventario
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('productos.index')" class="text-white hover:bg-gray-600 hover:text-[#3b82f6]">
                    Productos
                </x-responsive-nav-link>

            @elseif ($user->role === 'mecanico')

                <x-responsive-nav-link :href="route('admin.citas.index')" class="text-white hover:bg-gray-600 hover:text-[#3b82f6]">
                    Citas
                </x-responsive-nav-link>

            @endif
        </div>

        <!-- Responsive User Footer -->
        <div class="pt-4 pb-1 border-t border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-300">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        class="text-red-400 hover:bg-red-900/20"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Cerrar Sesión
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
