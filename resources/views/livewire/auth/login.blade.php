<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        @php
            $demoUsers = collect(config('crm.demo_login_users', []))->take(5);
            $demoPassword = (string) config('crm.demo_login_password', 'password123');
        @endphp

        @if ($demoUsers->isNotEmpty())
            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white/70 dark:border-zinc-800 dark:bg-zinc-900/70">
                <div class="border-b border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 dark:border-zinc-800 dark:text-zinc-100">
                    Demo Users
                </div>
                <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-zinc-800">
                    <thead class="bg-slate-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-slate-600 dark:text-zinc-300">Email</th>
                            <th class="px-4 py-2 text-left font-medium text-slate-600 dark:text-zinc-300">Password</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-zinc-800">
                        @foreach ($demoUsers as $demoUser)
                            <tr class="odd:bg-white even:bg-slate-50/60 dark:odd:bg-zinc-950 dark:even:bg-zinc-900/70">
                                <td class="px-4 py-2 font-mono text-slate-800 dark:text-zinc-100">{{ $demoUser['email'] }}</td>
                                <td class="px-4 py-2 font-mono text-slate-800 dark:text-zinc-100">{{ $demoPassword }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts::auth>
