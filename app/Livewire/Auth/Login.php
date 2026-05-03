<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email;
    public $password;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $user = Auth::user();
            
            // Identify Tenant and store in session
            if ($user->tenant_id) {
                session(['tenant_id' => $user->tenant_id]);
            }
            
            return redirect()->intended('/dashboard');
        }

        $this->addError('email', 'Credenciais inválidas.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.app');
    }
}
