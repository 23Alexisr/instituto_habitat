<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Inter', 'Segoe UI', sans-serif; }

.ipeh-wrap {
    min-height: 100vh;
    background: #F3F4F6;
    display: flex; align-items: center; justify-content: center;
    position: relative; overflow: hidden; padding: 20px;
}
.ipeh-wrap::before {
    content: ''; position: absolute;
    top: 32px; left: 32px; width: 140px; height: 140px;
    background-image: radial-gradient(circle, rgba(141,198,63,0.45) 1.5px, transparent 1.5px);
    background-size: 18px 18px; pointer-events: none;
}
.ipeh-wrap::after {
    content: ''; position: absolute;
    bottom: 32px; right: 32px; width: 140px; height: 140px;
    background-image: radial-gradient(circle, rgba(141,198,63,0.45) 1.5px, transparent 1.5px);
    background-size: 18px 18px; pointer-events: none;
}

.ipeh-card {
    display: flex; width: 900px; height: 560px;
    border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.12);
    background: #fff; animation: card-in .7s cubic-bezier(.22,1,.36,1) both;
}
@keyframes card-in {
    from { opacity:0; transform:translateY(28px) scale(.97); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}

/* ── IZQUIERDO ─────────────────────────────────────────── */
.ipeh-left {
    width: 50%;
    background: linear-gradient(160deg,#2d6b08 0%,#4f9a16 35%,#6aaa1f 65%,#8DC63F 100%);
    border-radius: 20px 0 0 20px;
    display: flex; flex-direction: column;
    align-items: center; justify-content: space-between;
    padding: 36px 32px 28px;
    transform: translateY(-10px);
    height: calc(100% + 20px);
    position: relative; overflow: hidden;
    box-shadow: 6px 0 24px rgba(0,0,0,.08);
}
.ipeh-left::before {
    content:''; position:absolute;
    width:280px; height:280px; border-radius:50%;
    background:rgba(255,255,255,.06);
    top:-60px; right:-60px; pointer-events:none;
}
.ipeh-left::after {
    content:''; position:absolute;
    width:200px; height:200px; border-radius:50%;
    background:rgba(255,255,255,.06);
    bottom:-40px; left:-40px; pointer-events:none;
}
.ipeh-left-logo {
    background:rgba(255,255,255,.18); backdrop-filter:blur(8px);
    border-radius:14px; padding:14px 22px;
    display:flex; align-items:center; justify-content:center;
}
.ipeh-left-logo img { width:180px; height:auto; display:block;
    filter:brightness(1.1) drop-shadow(0 2px 6px rgba(0,0,0,.25)); }

.ipeh-left-mid { text-align:center; color:rgba(255,255,255,.9); }
.ipeh-left-mid p { font-size:.8rem; letter-spacing:.08em;
    text-transform:uppercase; color:rgba(255,255,255,.65); margin-bottom:6px; }
.ipeh-left-mid h3 { font-size:1.2rem; font-weight:700; line-height:1.35; }

.ipeh-left-footer { width:100%; }
.ipeh-left-overlay {
    background:rgba(0,0,0,.22); border-radius:12px;
    padding:16px 20px; text-align:center; margin-bottom:16px;
}
.ipeh-left-overlay p:first-child {
    font-size:.72rem; letter-spacing:.12em;
    text-transform:uppercase; color:rgba(255,255,255,.6); margin-bottom:4px;
}
.ipeh-left-overlay p:last-child { font-size:.95rem; font-weight:600; color:#fff; }
.ipeh-nav-dots { display:flex; justify-content:center; gap:8px; }
.ipeh-nav-dot { width:8px; height:8px; border-radius:50%;
    background:rgba(255,255,255,.35); border:none; cursor:pointer; padding:0; transition:all .3s; }
.ipeh-nav-dot.active { background:#fff; width:22px; border-radius:4px; }

/* ── DERECHO ─────────────────────────────────────────── */
.ipeh-right {
    width: 50%; padding: 44px 40px;
    display: flex; flex-direction: column;
    background: #fff; border-radius: 0 20px 20px 0;
}

.ipeh-title { font-size:1.45rem; font-weight:700; color:#1f2937; margin-bottom:4px; }
.ipeh-subtitle { font-size:.875rem; color:#6b7280; margin-bottom:22px; }

.ipeh-tabs { display:flex; border-bottom:2px solid #e5e7eb; margin-bottom:22px; }
.ipeh-tab { padding:8px 18px; font-size:.875rem; font-weight:500;
    color:#9ca3af; background:none; border:none; cursor:pointer;
    border-bottom:2px solid transparent; margin-bottom:-2px; transition:all .2s; }
.ipeh-tab.active { color:#8DC63F; border-bottom-color:#8DC63F; }

.ipeh-field {
    display:flex; align-items:center;
    border-bottom:1.5px solid #e5e7eb; padding:10px 0; margin-bottom:18px;
    transition:border-color .2s;
}
.ipeh-field:focus-within { border-bottom-color:#8DC63F; }
.ipeh-field-icon { width:20px; margin-right:12px; color:#9ca3af; flex-shrink:0; }
.ipeh-field-icon svg { width:18px; height:18px; }
.ipeh-input { flex:1; border:none; outline:none;
    font-size:.9rem; color:#1f2937; background:transparent; }
.ipeh-input::placeholder { color:#9ca3af; }
.ipeh-forgot { font-size:.78rem; color:#8DC63F; text-decoration:none;
    white-space:nowrap; margin-left:8px; font-weight:500; }
.ipeh-forgot:hover { text-decoration:underline; }

.ipeh-error-msg { font-size:.78rem; color:#ef4444; margin:-12px 0 12px; }

.ipeh-btn {
    width:100%; padding:13px;
    background:linear-gradient(135deg,#6aaa1f,#8DC63F);
    color:#fff; font-size:.95rem; font-weight:600;
    border:none; border-radius:10px; cursor:pointer;
    letter-spacing:.02em; transition:opacity .2s, transform .15s; margin-top:4px;
    box-shadow:0 4px 14px rgba(141,198,63,.4);
    display:flex; align-items:center; justify-content:center; gap:8px;
}
.ipeh-btn:hover:not(:disabled) { opacity:.92; transform:translateY(-1px); }
.ipeh-btn:active:not(:disabled) { transform:translateY(0); }
.ipeh-btn:disabled { opacity:.7; cursor:not-allowed; }

.ipeh-spinner-sm {
    width:16px; height:16px; border-radius:50%;
    border:2px solid rgba(255,255,255,.4);
    border-top-color:#fff;
    animation:spin .6s linear infinite;
}
@keyframes spin { to { transform:rotate(360deg); } }

.ipeh-footer { text-align:center; font-size:.8rem; color:#6b7280; margin-top:auto; padding-top:16px; }
.ipeh-footer a { color:#8DC63F; font-weight:600; text-decoration:none; }
.ipeh-footer a:hover { text-decoration:underline; }
</style>

<div class="ipeh-wrap">
    <div class="ipeh-card">

        {{-- ── PANEL IZQUIERDO ──────────────────────────────── --}}
        <div class="ipeh-left">
            <div class="ipeh-left-logo">
                <img src="{{ asset('images/logo.svg') }}" alt="IPEH" />
            </div>

            <div class="ipeh-left-mid">
                <p>Sistema de Certificación</p>
                <h3>Gestiona y certifica<br>el aprendizaje</h3>
            </div>

            <div class="ipeh-left-footer">
                <div class="ipeh-left-overlay">
                    <p>Bienvenido a la comunidad</p>
                    <p>Ingresa para explorar el sistema</p>
                </div>
                <div class="ipeh-nav-dots">
                    <button type="button" class="ipeh-nav-dot active"></button>
                    <button type="button" class="ipeh-nav-dot"></button>
                    <button type="button" class="ipeh-nav-dot"></button>
                </div>
            </div>
        </div>

        {{-- -- PANEL DERECHO - formulario -------------------- --}}
        <div class="ipeh-right" x-data="{ tab: 'email' }">
            <p class="ipeh-title">¡Inicia sesión!</p>
            <p class="ipeh-subtitle">Accede a tu cuenta para continuar</p>

            <div class="ipeh-tabs">
                <button type="button" class="ipeh-tab" :class="{ active: tab === 'email' }" @click="tab = 'email'">Correo</button>
                <button type="button" class="ipeh-tab" :class="{ active: tab === 'phone' }" @click="tab = 'phone'">Celular</button>
            </div>

            <form wire:submit="authenticate">

                <div x-show="tab === 'email'">
                    <div class="ipeh-field">
                        <span class="ipeh-field-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </span>
                        <input type="email" wire:model="data.email" placeholder="Correo electrónico"
                               class="ipeh-input" autocomplete="email" />
                    </div>
                    @error('data.email')
                        <p class="ipeh-error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="tab === 'phone'" x-cloak>
                    <div class="ipeh-field">
                        <span class="ipeh-field-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                        </span>
                        <input type="tel" placeholder="Número de celular" class="ipeh-input" />
                    </div>
                </div>

                <div class="ipeh-field">
                    <span class="ipeh-field-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                    </span>
                    <input type="password" wire:model="data.password" placeholder="Contraseña"
                           class="ipeh-input" autocomplete="current-password" />
                    @if (filament()->hasPasswordReset())
                        <a href="{{ filament()->getRequestPasswordResetUrl() }}" class="ipeh-forgot">¿Olvidaste?</a>
                    @endif
                </div>
                @error('data.password')
                    <p class="ipeh-error-msg">{{ $message }}</p>
                @enderror

                <button type="submit" class="ipeh-btn"
                        wire:loading.attr="disabled"
                        wire:target="authenticate">
                    <span wire:loading.remove wire:target="authenticate">Continuar</span>
                    <span wire:loading wire:target="authenticate" style="display:none">
                        <span class="ipeh-spinner-sm"></span>
                        Verificando...
                    </span>
                </button>
            </form>

            <p class="ipeh-footer">
                ¿Problemas para ingresar?
                @if (filament()->hasPasswordReset())
                    <a href="{{ filament()->getRequestPasswordResetUrl() }}">Recupera tu contraseña</a>
                @else
                    Contacta al administrador
                @endif
            </p>
        </div>

    </div>
</div>
