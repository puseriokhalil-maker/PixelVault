@extends('layouts.auth')

@section('title', 'Create an account - PixelVault')

@section('content')
    <div class="grid min-h-[min(720px,calc(100vh-2rem))] w-full max-w-[1180px] overflow-hidden rounded-2xl bg-white shadow-xl shadow-slate-900/10 md:min-h-[min(620px,calc(100vh-4rem))] md:grid-cols-[0.9fr_1.1fr]">
        <section class="relative flex flex-col justify-center overflow-hidden bg-[#0e172b] px-7 py-10 text-white sm:px-10 md:px-12">
            <div
                class="pointer-events-none absolute inset-0 opacity-80"
                style="background-image: linear-gradient(to right, #1c2b49 1px, transparent 1px), linear-gradient(to bottom, #1c2b49 1px, transparent 1px); background-size: 34px 34px;"
                aria-hidden="true"
            ></div>

            <div class="relative mx-auto w-full max-w-lg">
                <a href="{{ route('home') }}" class="mb-6 inline-flex items-center gap-2 text-xs font-medium text-slate-300 transition hover:text-white">
                    <span class="h-5 w-5 rounded bg-[#d9d9dc]" aria-hidden="true"></span>
                    PixelVault
                </a>
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

                @include('components.auth-artwork-feature')
            </div>
        </section>

        <section class="flex items-center justify-center bg-slate-50 px-6 py-10 sm:px-10 md:px-12 lg:px-16">
            <div class="w-full max-w-xs">
                <div class="mb-2 text-[11px] font-semibold uppercase tracking-[0.12em] text-indigo-600">Your PixelVault</div>
                <h2 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-[26px]">Create your account</h2>
                <p class="mt-2 text-sm text-slate-500">Set up your personal space for digital art.</p>

                <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-medium text-slate-800">Name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            autocomplete="name"
                            required
                            maxlength="255"
                            placeholder="Your name"
                            aria-describedby="name-error"
                            class="h-11 w-full rounded-lg border border-slate-200 bg-white px-3.5 text-sm text-slate-900 shadow-sm shadow-slate-900/[0.02] outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                        >
                        @error('name')
                            <p id="name-error" class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-800">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                            maxlength="255"
                            placeholder="you@example.com"
                            aria-describedby="email-error"
                            class="h-11 w-full rounded-lg border border-slate-200 bg-white px-3.5 text-sm text-slate-900 shadow-sm shadow-slate-900/[0.02] outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                        >
                        @error('email')
                            <p id="email-error" class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-800">Password</label>
                        <div class="flex h-11 items-center gap-3 rounded-lg border border-slate-200 bg-white px-3.5 shadow-sm shadow-slate-900/[0.02] transition focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                placeholder="At least 8 characters"
                                aria-describedby="password-error"
                                class="min-w-0 flex-1 bg-transparent text-sm text-slate-900 outline-none placeholder:text-slate-400"
                            >
                            <button type="button" data-password-toggle="password" aria-label="Show password" class="shrink-0 text-slate-400 transition hover:text-slate-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p id="password-error" class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-800">Confirm password</label>
                        <div class="flex h-11 items-center gap-3 rounded-lg border border-slate-200 bg-white px-3.5 shadow-sm shadow-slate-900/[0.02] transition focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                placeholder="Enter your password again"
                                class="min-w-0 flex-1 bg-transparent text-sm text-slate-900 outline-none placeholder:text-slate-400"
                            >
                            <button type="button" data-password-toggle="password_confirmation" aria-label="Show password" class="shrink-0 text-slate-400 transition hover:text-slate-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Create account
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </form>

                <p class="mt-5 text-center text-sm text-slate-500">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 transition hover:text-indigo-800">Log in</a>
                </p>
            </div>
        </section>
    </div>
@endsection