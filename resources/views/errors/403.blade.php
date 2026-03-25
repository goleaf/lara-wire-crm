<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        @include('partials.head')
    </head>
    <body class="h-full bg-slate-950 text-slate-100">
        <main class="flex min-h-screen items-center justify-center px-4 py-10">
            <section class="w-full max-w-xl rounded-3xl border border-white/10 bg-slate-900/70 p-8 shadow-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-300">403</p>
                <h1 class="mt-3 text-3xl font-semibold">Access Denied</h1>
                <p class="mt-3 text-sm text-slate-300">You do not have permission to view this page.</p>
                <div class="mt-6">
                    <a href="{{ Route::has('dashboard') ? route('dashboard') : route('home') }}" class="crm-btn crm-btn-primary">
                        Back to Dashboard
                    </a>
                </div>
            </section>
        </main>
    </body>
</html>
