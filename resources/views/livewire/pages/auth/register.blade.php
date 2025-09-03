<?php

use App\Models\User;
use App\Models\BadanPublik;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.guest')] class extends Component
{
    // Data Badan Publik
    public string $nama_badan_publik = '';
    public string $website = '';
    public string $telepon_badan_publik = '';
    public string $email_badan_publik = '';
    public string $alamat = '';

    // Data Responden
    public string $nama_responden = '';
    public string $telepon_responden = '';
    public string $jabatan = '';
    public string $email_responden = ''; // This will be the login email

    // Data PPID
    public string $nama_ppid = '';
    public string $telepon_ppid = '';
    public string $email_ppid = '';

    // Informasi Akun
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'nama_badan_publik' => ['required', 'string', 'max:255'],
            'website' => ['required', 'string', 'max:255'],
            'telepon_badan_publik' => ['required', 'string', 'max:20'],
            'email_badan_publik' => ['required', 'string', 'email', 'max:255'],
            'alamat' => ['required', 'string'],
            'nama_responden' => ['required', 'string', 'max:255'],
            'telepon_responden' => ['required', 'string', 'max:20'],
            'jabatan' => ['required', 'string', 'max:255'],
            'email_responden' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email'],
            'nama_ppid' => ['required', 'string', 'max:255'],
            'telepon_ppid' => ['required', 'string', 'max:20'],
            'email_ppid' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ]);

        // 1. Create the user account for login
        $user = User::create([
            'name' => $this->nama_responden,
            'email' => $this->email_responden,
            'password' => Hash::make($this->password),
            'role' => 'dinas', // PERBAIKAN: Secara eksplisit atur role pengguna baru
        ]);

        // 2. Save the additional data to the 'badan_publiks' table and link it to the user
        BadanPublik::create([
            'user_id' => $user->id, // This is the crucial link
            'nama_badan_publik' => $this->nama_badan_publik,
            'website' => $this->website,
            'telepon_badan_publik' => $this->telepon_badan_publik,
            'email_badan_publik' => $this->email_badan_publik,
            'alamat' => $this->alamat,
            'telepon_responden' => $this->telepon_responden,
            'jabatan' => $this->jabatan,
            'nama_ppid' => $this->nama_ppid,
            'telepon_ppid' => $this->telepon_ppid,
            'email_ppid' => $this->email_ppid,
        ]);

        event(new Registered($user));

        // Redirect to the success page
        $this->redirect(route('register.success'), navigate: true);
    }
}; ?>

<div>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-4xl">
            <div class="flex justify-end mb-4">
                 <a href="/" class="flex items-center space-x-2">
                    <img src="/images/logobna.png" alt="Logo E-Monev" class="h-10 w-auto">
                    <span class="text-xl font-bold text-gray-800">E-Monev</span>
                </a>
            </div>
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                 <h1 class="text-3xl font-bold text-gray-900 mb-6">Registrasi</h1>

                <form wire:submit="register" class="space-y-8">
                    <!-- Data Badan Publik -->
                    <fieldset class="space-y-6">
                        <legend class="text-lg font-semibold text-gray-900">Data Badan Publik</legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nama_badan_publik" class="block text-sm font-medium text-gray-700">Nama Badan Publik</label>
                                <input wire:model="nama_badan_publik" id="nama_badan_publik" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <x-input-error :messages="$errors->get('nama_badan_publik')" class="mt-2" />
                            </div>
                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                                <input wire:model="website" id="website" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <x-input-error :messages="$errors->get('website')" class="mt-2" />
                            </div>
                            <div>
                                <label for="telepon_badan_publik" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                <input wire:model="telepon_badan_publik" id="telepon_badan_publik" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <x-input-error :messages="$errors->get('telepon_badan_publik')" class="mt-2" />
                            </div>
                            <div>
                                <label for="email_badan_publik" class="block text-sm font-medium text-gray-700">Email</label>
                                <input wire:model="email_badan_publik" id="email_badan_publik" type="email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <x-input-error :messages="$errors->get('email_badan_publik')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                                <textarea wire:model="alamat" id="alamat" rows="3" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                            </div>
                        </div>
                    </fieldset>

                    <!-- Data Responden -->
                    <fieldset class="space-y-6">
                        <legend class="text-lg font-semibold text-gray-900">Data Responden</legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                             <div>
                                <label for="nama_responden" class="block text-sm font-medium text-gray-700">Nama Responden</label>
                                <input wire:model="nama_responden" id="nama_responden" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <x-input-error :messages="$errors->get('nama_responden')" class="mt-2" />
                            </div>
                             <div>
                                <label for="telepon_responden" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                <input wire:model="telepon_responden" id="telepon_responden" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <x-input-error :messages="$errors->get('telepon_responden')" class="mt-2" />
                            </div>
                             <div>
                                <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
                                <input wire:model="jabatan" id="jabatan" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
                            </div>
                             <div>
                                <label for="email_responden" class="block text-sm font-medium text-gray-700">Email Responden</label>
                                <input wire:model="email_responden" id="email_responden" type="email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <p class="mt-1 text-xs text-red-500">*email responden ini digunakan untuk login</p>
                                <x-input-error :messages="$errors->get('email_responden')" class="mt-2" />
                            </div>
                        </div>
                    </fieldset>

                     <!-- Data PPID -->
                    <fieldset class="space-y-6">
                        <legend class="text-lg font-semibold text-gray-900">Data PPID</legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                             <div>
                                <label for="nama_ppid" class="block text-sm font-medium text-gray-700">Nama PPID</label>
                                <input wire:model="nama_ppid" id="nama_ppid" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <x-input-error :messages="$errors->get('nama_ppid')" class="mt-2" />
                            </div>
                             <div>
                                <label for="telepon_ppid" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                <input wire:model="telepon_ppid" id="telepon_ppid" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <x-input-error :messages="$errors->get('telepon_ppid')" class="mt-2" />
                            </div>
                             <div class="md:col-span-2">
                                <label for="email_ppid" class="block text-sm font-medium text-gray-700">Email PPID</label>
                                <input wire:model="email_ppid" id="email_ppid" type="email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <x-input-error :messages="$errors->get('email_ppid')" class="mt-2" />
                            </div>
                        </div>
                    </fieldset>

                     <!-- Informasi Akun -->
                    <fieldset class="space-y-6">
                        <legend class="text-lg font-semibold text-gray-900">Informasi Akun</legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="relative">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input wire:model="password" id="password" type="password" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <button type="button" onclick="togglePasswordVisibility('password', 'eye-icon-1', 'eye-off-icon-1')" class="absolute inset-y-0 right-0 top-6 pr-3 flex items-center text-gray-400">
                                   <svg id="eye-icon-1" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                   <svg id="eye-off-icon-1" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 .847 0 1.67.127 2.455.364m0 11.452A9.96 9.96 0 0112 17c-4.478 0-8.268-2.943-9.542-7a10.034 10.034 0 013.454-4.545m1.546-1.546A10.008 10.008 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-.68 2.455m-1.455 1.455A10.05 10.05 0 0112 19c-1.654 0-3.21-.48-4.545-1.31M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </button>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div class="relative">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Ulangi Password</label>
                                <input wire:model="password_confirmation" id="password_confirmation" type="password" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eye-icon-2', 'eye-off-icon-2')" class="absolute inset-y-0 right-0 top-6 pr-3 flex items-center text-gray-400">
                                   <svg id="eye-icon-2" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                   <svg id="eye-off-icon-2" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 .847 0 1.67.127 2.455.364m0 11.452A9.96 9.96 0 0112 17c-4.478 0-8.268-2.943-9.542-7a10.034 10.034 0 013.454-4.545m1.546-1.546A10.008 10.008 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-.68 2.455m-1.455 1.455A10.05 10.05 0 0112 19c-1.654 0-3.21-.48-4.545-1.31M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </fieldset>

                    <div class="pt-5">
                        <div class="flex flex-col items-center">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline">
                                Registrasi
                            </button>
                            <p class="mt-4 text-sm text-gray-600">
                                Sudah memiliki akun?
                                <a href="{{ route('login') }}" wire:navigate class="font-medium text-blue-600 hover:text-blue-500">
                                    Masuk
                                </a>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
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
