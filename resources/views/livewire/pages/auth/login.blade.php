<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        // Dapatkan user yang baru login
        $user = auth()->user();

        // Cek apakah user adalah pemilik UMKM yang belum di-approve
        if ($user->user_type === \App\Models\User::ROLE_UMKM_OWNER && !$user->is_approved) {
            // Logout user
            auth()->logout();
            Session::invalidate();
            Session::regenerateToken();

            // Set flash message
            session()->flash('error', 'Akun Anda masih menunggu persetujuan dari admin. Anda akan menerima notifikasi melalui email setelah akun disetujui.');
            session()->flash('pending_approval', true);
            session()->flash('user_email', $user->email);
            session()->flash('user_name', $user->name);

            return;
        }

        Session::regenerate();

        // Tentukan redirect berdasarkan user type
        $defaultRoute = $this->getDefaultRouteForUser($user);

        $this->redirectIntended(default: route($defaultRoute, absolute: false), navigate: true);
    }

    /**
     * Mendapatkan route default berdasarkan user type
     */
    private function getDefaultRouteForUser($user): string
    {
        if (!$user) {
            return 'home';
        }

        switch ($user->user_type) {
            case \App\Models\User::ROLE_ADMIN:
                return 'admin.dashboard';

            case \App\Models\User::ROLE_UMKM_OWNER:
                return 'umkm.dashboard';

            default:
                return 'home';
        }
    }
}; ?>

<div>
    <!-- Pending Approval Alert -->
    @if (session('pending_approval'))
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Menunggu Persetujuan Admin
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>{{ session('error') }}</p>
                    </div>
                    @if (session('user_name'))
                        <div class="mt-3 bg-white rounded-lg p-3 border border-yellow-200">
                            <p class="text-xs text-gray-600">
                                <span class="font-semibold">Nama:</span> {{ session('user_name') }}
                            </p>
                            <p class="text-xs text-gray-600">
                                <span class="font-semibold">Email:</span> {{ session('user_email') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                ⏱️ Proses verifikasi biasanya memakan waktu 1-3 hari kerja
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Error Messages -->
    @if (session('error') && !session('pending_approval'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email"
                required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full" type="password"
                name="password" required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        {{-- <div class="flex items-center justify-between mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox"
                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-primary-600 hover:text-primary-500 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div> --}}

        <div class="mt-6">
            <button type="submit" class="w-full bg-fix-400 rounded-full text-white font-bold p-2 justify-center">
                {{ __('Log in') }}
            </button>
        </div>

        <!-- Register Link -->
        {{-- <div class="mt-4 text-center">
            <span class="text-sm text-gray-600">Belum punya akun?</span>
            <a href="{{ route('register') }}" wire:navigate
                class="text-sm text-primary-600 hover:text-primary-500 font-medium ml-1">
                Daftar sekarang
            </a>
        </div> --}}
    </form>
</div>
