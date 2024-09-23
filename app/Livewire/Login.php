<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Login - Kulisha')]
class Login extends Component
{
    public $identifier; // For email, phone or username
    public $password;
    public $remember;

    protected $rules = [
        'identifier' => 'required',
        'password' => 'required|min:8',
    ];

    public function login()
    {
        $this->validate();

        // Verifying the ID
        $user = null;

        if (filter_var($this->identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $this->identifier)->first();

        } elseif (is_numeric($this->identifier)) { // Adjust the regex according to the phone format
            $user = User::where('phone', $this->identifier)->first();

        } else {
            $user = User::where('username', $this->identifier)->first();
        }

        if ($user && Auth::attempt(['id' => $user->id, 'password' => $this->password], $this->remember)) {
            // Authentication successful
            return redirect()->intended('/'); // Redirigez où vous le souhaitez
        }

        // Authentication failed
        throw ValidationException::withMessages([
            'identifier' => __('auth.failed'),
        ]);
    }   

    public function render()
    {
        return view('livewire.login');
    }
}
