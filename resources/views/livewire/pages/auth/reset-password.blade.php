<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public string $token;
    public string $email;
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    /**
     * Handle an incoming new password request.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            ['email' => $this->email, 'password' => $this->password, 'token' => $this->token],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', __($status));
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $this->addError('email', __($status));
    }
}; ?>

<div class="min-h-screen flex flex-col md:flex-row">
    <!-- Left Side: Form -->
    <div class="w-full md:w-1/2 flex flex-col justify-center items-center p-8 md:p-12 bg-white">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="flex items-center space-x-2 mb-12">
                <img src="/images/logobna.png" alt="Logo E-Monev" class="h-10 w-auto">
                <span class="text-xl font-bold text-gray-800">E-Monev</span>
            </div>

            <!-- Header -->
            <h1 class="text-3xl font-bold text-gray-900">Atur kata sandi</h1>
            <p class="mt-2 text-gray-600">Kata sandi Anda yang sebelumnya telah direset. Silakan atur kata sandi baru untuk akun Anda.</p>

            <!-- Form -->
            <form wire:submit="resetPassword" class="mt-8 space-y-6">
                <!-- Email (hidden, but needed for the process) -->
                <input wire:model="email" id="email" type="hidden">
                
                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Buat Password</label>
                    <div class="mt-1 relative">
                        <input wire:model="password" id="password" type="password" required autocomplete="new-password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Masukkan password baru anda">
                        <button type="button" onclick="togglePasswordVisibility('password', 'eye-icon-1', 'eye-off-icon-1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                        <svg id="eye-icon-1" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg id="eye-off-icon-1" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 .847 0 1.67.127 2.455.364m0 11.452A9.96 9.96 0 0112 17c-4.478 0-8.268-2.943-9.542-7a10.034 10.034 0 013.454-4.545m1.546-1.546A10.008 10.008 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-.68 2.455m-1.455 1.455A10.05 10.05 0 0112 19c-1.654 0-3.21-.48-4.545-1.31M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password Input -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Masukkan Kembali Password</label>
                    <div class="mt-1 relative">
                        <input wire:model="password_confirmation" id="password_confirmation" type="password" required autocomplete="new-password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Masukkan kembali password baru anda">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eye-icon-2', 'eye-off-icon-2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                        <svg id="eye-icon-2" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg id="eye-off-icon-2" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 .847 0 1.67.127 2.455.364m0 11.452A9.96 9.96 0 0112 17c-4.478 0-8.268-2.943-9.542-7a10.034 10.034 0 013.454-4.545m1.546-1.546A10.008 10.008 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-.68 2.455m-1.455 1.455A10.05 10.05 0 0112 19c-1.654 0-3.21-.48-4.545-1.31M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Atur kata sandi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Side: Image -->
    <div class="w-full md:w-1/2 hidden md:flex justify-center items-center p-12">
        <div class="w-full max-w-md">
            <img src="/images/forgot-password-illustration.png" alt="Set Password Illustration" class="w-full h-auto">
        </div>
    </div>

    @push('scripts')
    <script>
        function togglePasswordVisibility(inputId, eyeIconId, eyeOffIconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(eyeIconId);
            const eyeOffIcon = document.getElementById(eyeOffIconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
    @endpush
</div>
