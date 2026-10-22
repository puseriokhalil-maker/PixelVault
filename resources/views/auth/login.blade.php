@extends('layouts.auth')

@section('title', 'Log in - PixelVault')

@section('content')
    <div class="grid min-h-[min(720px,calc(100vh-2rem))] w-full max-w-[1180px] overflow-hidden rounded-2xl bg-white shadow-xl shadow-slate-900/10 md:min-h-[min(620px,calc(100vh-4rem))] md:grid-cols-[0.9fr_1.1fr]">
        <section class="relative flex flex-col justify-center overflow-hidden bg-[#0e172b] px-7 py-10 text-white sm:px-10 md:px-12">
            <div
                class="pointer-events-none absolute inset-0 opacity-80"
                style="background-image: linear-gradient(to right, #1c2b49 1px, transparent 1px), linear-gradient(to bottom, #1c2b49 1px, transparent 1px); background-size: 34px 34px;"
                aria-hidden="true"
            ></div>

            <div class="relative mx-auto w-full max-w-lg">
                <div class="mb-4 inline-flex items-center rounded-full border border-indigo-300/30 bg-indigo-950/40 px-3 py-1.5 text-[10px] font-medium tracking-wide text-indigo-100">
                    <span aria-hidden="true" class="mr-2 text-sm">✧</span>
                    CURATED FOR DIGITAL CREATORS
                </div>
                <h1 class="max-w-md text-[34px] font-semibold leading-tight tracking-tight sm:text-4xl">
                    Art belongs everywhere.
                </h1>
                <p class="mt-3 max-w-md text-sm leading-6 text-slate-300">
                    Discover independent artists. Collect original digital pieces. Find what moves you.
                </p>

                <figure class="mt-7">
                    <div class="relative aspect-[2.4] overflow-hidden rounded-xl bg-[#160b37] shadow-lg shadow-black/20">
                        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_rgba(50,160,255,0.35),_transparent_55%),radial-gradient(ellipse_at_70%_35%,_rgba(194,43,255,0.5),_transparent_48%),linear-gradient(135deg,_#0b0631,_#24094b_55%,_#070d2b)]"></div>
                        <svg class="absolute inset-0 h-full w-full" viewBox="0 0 720 400" fill="none" role="img" aria-label="Abstract neon blue and purple digital artwork">
                            <defs>
                                <filter id="neon-glow" x="-50%" y="-50%" width="200%" height="200%">
                                    <feGaussianBlur stdDeviation="5" result="blur"></feGaussianBlur>
                                    <feMerge>
                                        <feMergeNode in="blur"></feMergeNode>
                                        <feMergeNode in="SourceGraphic"></feMergeNode>
                                    </feMerge>
                                </filter>
                            </defs>
                            <g filter="url(#neon-glow)" stroke-linecap="round">
                                <path d="M64 215C175 55 224 350 355 182S550 32 660 205" stroke="#39D7FF" stroke-width="4" opacity=".95"></path>
                                <path d="M66 192C190 348 245 43 372 205S548 356 654 177" stroke="#7388FF" stroke-width="3" opacity=".9"></path>
                                <path d="M98 285C215 95 295 306 365 140S516 113 622 283" stroke="#D15BFF" stroke-width="3" opacity=".9"></path>
                                <path d="M92 120C220 274 291 87 377 258S511 298 625 104" stroke="#48BFFF" stroke-width="2.5" opacity=".85"></path>
                                <path d="M145 332C221 211 262 196 347 220S490 140 575 67" stroke="#52F0FF" stroke-width="2" opacity=".8"></path>
                                <path d="M137 61C219 163 277 254 353 184S492 127 589 332" stroke="#B05BFF" stroke-width="2" opacity=".75"></path>
                            </g>
                            <g fill="#B8F7FF">
                                <circle cx="355" cy="182" r="4"></circle>
                                <circle cx="372" cy="205" r="3"></circle>
                                <circle cx="347" cy="220" r="2.5"></circle>
                            </g>
                        </svg>
                        <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-[#100a30]/80 to-transparent"></div>
                    </div>
                    <figcaption class="mt-3 flex items-center justify-between gap-4">
                        <div>
                            <div class="text-sm font-medium">Neon Solitude</div>
                            <div class="mt-0.5 text-xs text-slate-400">by Maya Chen</div>
                        </div>
                        <span class="text-xs text-slate-300">2D · Digital artwork</span>
                    </figcaption>
                </figure>
            </div>
        </section>

        <section class="flex items-center justify-center bg-slate-50 px-6 py-12 sm:px-10 md:px-12 lg:px-16">
            <div class="w-full max-w-xs">
                <div class="mb-2 text-[11px] font-semibold uppercase tracking-[0.12em] text-indigo-600">Your PixelVault</div>
                <h2 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-[26px]">Welcome back</h2>
                <p class="mt-2 text-sm text-slate-500">Log in to your account to keep exploring.</p>

                <form method="POST" action="{{ route('login.store') }}" class="mt-7 space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-800">Email address</label>
                        <div class="flex h-11 items-center gap-3 rounded-lg border border-slate-200 bg-white px-3.5 shadow-sm shadow-slate-900/[0.02] transition focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100">
                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.7"></rect>
                                <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                                placeholder="you@example.com"
                                aria-describedby="email-error"
                                class="min-w-0 flex-1 bg-transparent text-sm text-slate-900 outline-none placeholder:text-slate-400"
                            >
                        </div>
                        @error('email')
                            <p id="email-error" class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-800">Password</label>
                        <div class="flex h-11 items-center gap-3 rounded-lg border border-slate-200 bg-white px-3.5 shadow-sm shadow-slate-900/[0.02] transition focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100">
                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="5" y="10" width="14" height="11" rx="2" stroke="currentColor" stroke-width="1.7"></rect>
                                <path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"></path>
                            </svg>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                placeholder="Enter your password"
                                aria-describedby="password-error"
                                class="min-w-0 flex-1 bg-transparent text-sm text-slate-900 outline-none placeholder:text-slate-400"
                            >
                            <button
                                type="button"
                                data-password-toggle
                                aria-label="Show password"
                                class="shrink-0 text-slate-400 transition hover:text-slate-600"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"></path>
                                    <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"></circle>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p id="password-error" class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between gap-3 text-xs">
                        <label for="remember" class="inline-flex items-center gap-2 text-slate-500">
                            <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember')) class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            Remember me
                        </label>
                        <a href="#forgot-password" class="font-medium text-indigo-600 transition hover:text-indigo-800">Forgot password?</a>
                    </div>

                    <button type="submit" class="flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Log in
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-500">
                    New to PixelVault?
                    <a href="#create-account" class="font-medium text-indigo-600 transition hover:text-indigo-800">Create an account</a>
                </p>
            </div>
        </section>
    </div>

    <script>
        document.querySelector('[data-password-toggle]')?.addEventListener('click', (event) => {
            const button = event.currentTarget;
            const password = document.querySelector('#password');
            const isVisible = password.type === 'text';

            password.type = isVisible ? 'password' : 'text';
            button.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
        });
    </script>
@endsection
