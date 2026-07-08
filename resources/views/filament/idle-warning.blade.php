@auth
    @php
        // Aviso a los 2 min de inactividad real (sin click/teclado/scroll), countdown de 2 min más antes del cierre.
        $idleBeforeWarningMs = 2 * 60 * 1000;
        $countdownSeconds = 120;
    @endphp

    <div
        x-data="{
            idleTimer: null,
            countdownTimer: null,
            showModal: false,
            secondsLeft: {{ $countdownSeconds }},
            idleBeforeWarningMs: {{ $idleBeforeWarningMs }},

            init() {
                this.resetIdleTimer();
                ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(evento => {
                    document.addEventListener(evento, () => this.resetIdleTimer());
                });
            },

            resetIdleTimer() {
                if (this.showModal) return;

                clearTimeout(this.idleTimer);
                this.idleTimer = setTimeout(() => this.mostrarAviso(), this.idleBeforeWarningMs);
            },

            mostrarAviso() {
                this.showModal = true;
                this.secondsLeft = {{ $countdownSeconds }};

                this.countdownTimer = setInterval(() => {
                    this.secondsLeft--;
                    if (this.secondsLeft <= 0) {
                        clearInterval(this.countdownTimer);
                        this.salir();
                    }
                }, 1000);
            },

            seguirTrabajando() {
                clearInterval(this.countdownTimer);
                this.showModal = false;

                fetch(window.location.href, { method: 'GET', credentials: 'same-origin' });

                this.resetIdleTimer();
            },

            salir() {
                document.getElementById('idle-logout-form').submit();
            },
        }"
        x-cloak
    >
        <form id="idle-logout-form" method="POST" action="{{ filament()->getLogoutUrl() }}" class="hidden">
            @csrf
        </form>

        <template x-teleport="body">
            <div
                x-show="showModal"
                x-transition
                class="fi-modal fixed inset-0 z-[99999] flex items-center justify-center bg-gray-950/50 p-4"
                style="display: none;"
            >
                <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900">
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">
                        ¿Sigues trabajando?
                    </h2>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        La sesión se va a cerrar por inactividad en
                        <span class="font-semibold text-gray-950 dark:text-white" x-text="secondsLeft"></span>
                        segundos.
                    </p>
                    <div class="mt-4 flex justify-end gap-2">
                        <button
                            type="button"
                            x-on:click="salir()"
                            class="fi-btn fi-btn-color-gray rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/5"
                        >
                            Salir
                        </button>
                        <button
                            type="button"
                            x-on:click="seguirTrabajando()"
                            class="fi-btn fi-btn-color-primary rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-500"
                        >
                            Seguir trabajando
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endauth
