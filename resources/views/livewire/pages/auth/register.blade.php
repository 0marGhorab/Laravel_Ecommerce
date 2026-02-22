<?php

use App\Models\PendingEmailVerification;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /** Step 2: verification */
    public int $step = 1;
    public string $code = '';
    public ?int $verificationSentAt = null;

    public function getResendSecondsRemainingProperty(): int
    {
        if (!$this->verificationSentAt) {
            return 0;
        }
        return max(0, 30 - (time() - $this->verificationSentAt));
    }

    /** When using MAIL_MAILER=log in local, we show the code on screen so you can verify without SMTP. */
    public function getDevVerificationCodeProperty(): ?string
    {
        if (!app()->environment('local') || config('mail.default') !== 'log') {
            return null;
        }
        return session('email_verification_dev_code');
    }

    /**
     * Register immediately (no email verification). Used when email_verification_on_register is false.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Please enter a password.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password and confirmation do not match.',
            'password.letters' => 'The password must contain at least one letter.',
            'password.numbers' => 'The password must contain at least one number.',
            'password.mixed' => 'The password must contain both uppercase and lowercase letters.',
            'password.symbols' => 'The password must contain at least one symbol.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));
        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Step 1: Validate and send verification email. Used when email_verification_on_register is true.
     */
    public function sendCode(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Please enter a password.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password and confirmation do not match.',
            'password.letters' => 'The password must contain at least one letter.',
            'password.numbers' => 'The password must contain at least one number.',
            'password.mixed' => 'The password must contain both uppercase and lowercase letters.',
            'password.symbols' => 'The password must contain at least one symbol.',
        ]);

        $code = (string) random_int(100000, 999999);

        PendingEmailVerification::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'password' => Hash::make($validated['password']),
                'code' => $code,
                'expires_at' => now()->addMinutes(15),
            ]
        );

        Mail::to($validated['email'])->send(new \App\Mail\EmailVerificationCodeMail($code));

        if (app()->environment('local') && config('mail.default') === 'log') {
            session()->put('email_verification_dev_code', $code);
        }
        $this->verificationSentAt = time();
        $this->step = 2;
        $this->code = '';
        $this->resetValidation();
    }

    /**
     * Step 2: Verify code and create account.
     */
    public function verify(): void
    {
        $this->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ], [
            'code.required' => 'Please enter the 6-digit code.',
            'code.size' => 'The code must be exactly 6 digits.',
            'code.regex' => 'The code must contain only numbers.',
        ]);

        $pending = PendingEmailVerification::where('email', $this->email)
            ->where('code', $this->code)
            ->first();

        if (!$pending) {
            $this->addError('code', 'The code is incorrect. Please check and try again, or request a new code.');
            return;
        }

        if ($pending->isExpired()) {
            $this->addError('code', 'This code has expired. Please request a new code or change your email.');
            return;
        }

        event(new Registered($user = User::create([
            'name' => $pending->name,
            'email' => $pending->email,
            'password' => $pending->password,
        ])));

        $pending->delete();
        session()->forget('email_verification_dev_code');
        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Resend verification code (with 30s cooldown).
     */
    public function resend(): void
    {
        if ($this->resendSecondsRemaining > 0) {
            $this->addError('resend', 'Please wait ' . $this->resendSecondsRemaining . ' seconds before requesting a new code.');
            return;
        }

        $pending = PendingEmailVerification::where('email', $this->email)->first();
        if (!$pending) {
            $this->addError('resend', 'Session expired. Please change your email and start again.');
            return;
        }

        $code = (string) random_int(100000, 999999);
        $pending->update([
            'code' => $code,
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($this->email)->send(new \App\Mail\EmailVerificationCodeMail($code));
        if (app()->environment('local') && config('mail.default') === 'log') {
            session()->put('email_verification_dev_code', $code);
        }
        $this->verificationSentAt = time();
        $this->resetValidation();
        $this->dispatch('code-sent');
    }

    /**
     * Go back to step 1 to change email.
     */
    public function changeEmail(): void
    {
        PendingEmailVerification::where('email', $this->email)->delete();
        $this->step = 1;
        $this->code = '';
        $this->verificationSentAt = null;
        $this->resetValidation();
    }
}; ?>

<div>
    @if(!config('app.email_verification_on_register'))
        {{-- No email verification: single-step registration --}}
        <form wire:submit="register">
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <p class="mt-1.5 text-sm text-gray-500">Must be at least 8 characters.</p>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                    {{ __('Already have an account? Login Now') }}
                </a>
                <x-primary-button type="submit">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    @elseif($step === 1)
        <form wire:submit="sendCode">
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <p class="mt-1.5 text-sm text-gray-500">Must be at least 8 characters.</p>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                    {{ __('Already have an account? Login Now') }}
                </a>
                <x-primary-button type="submit">
                    {{ __('Continue') }}
                </x-primary-button>
            </div>
        </form>
    @else
        {{-- Step 2: Verification --}}
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-6" x-data="{ seconds: {{ $this->resendSecondsRemaining }} }" x-init="setInterval(() => { if (seconds > 0) seconds-- }, 1000); $wire.on('code-sent', () => { seconds = 30 })">
            @if($this->devVerificationCode)
                <div class="mb-4 rounded-md bg-amber-50 border border-amber-200 p-3 text-sm text-amber-800">
                    <strong>Development mode:</strong> Mail is not sent. Use this code: <code class="font-mono font-bold">{{ $this->devVerificationCode }}</code>
                </div>
            @endif
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Verify your email</h2>
            <p class="text-sm text-gray-600 mb-4">We sent a 6-digit code to the email below. Enter it to create your account.</p>

            {{-- Readonly email so user can confirm --}}
            <div class="mb-4">
                <x-input-label for="verify_email_display" value="Email" />
                <input type="text" id="verify_email_display" value="{{ $email }}" readonly
                    class="block mt-1 w-full rounded-md border-gray-300 bg-gray-100 text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            </div>

            <form wire:submit="verify">
                <div>
                    <x-input-label for="code" value="Verification code" />
                    <x-text-input wire:model="code" id="code" class="block mt-1 w-full text-center text-lg tracking-[0.5em] font-mono" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="000000" autocomplete="one-time-code" />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>

                <x-primary-button type="submit" class="w-full mt-4">
                    {{ __('Verify and create account') }}
                </x-primary-button>
            </form>

            <div class="mt-6 pt-4 border-t border-gray-200 space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-sm text-gray-600">Didn't get the code or it expired?</span>
                    <button type="button" wire:click="resend" :disabled="seconds > 0"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="seconds > 0" x-text="'Resend code (' + seconds + 's)'"></span>
                        <span x-show="seconds <= 0">Resend code</span>
                    </button>
                </div>
                @error('resend')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <button type="button" wire:click="changeEmail" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    {{ __('Use a different email') }}
                </button>
            </div>
        </div>

        <p class="mt-4 text-center">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}" wire:navigate>
                {{ __('Already have an account? Login') }}
            </a>
        </p>
    @endif
</div>
