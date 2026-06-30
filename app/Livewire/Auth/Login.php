<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';
    
    #[Validate('required|string')]
    public string $password = '';
    
    public bool $remember = false;

    public function login()
    {
        // Validierung
        $this->validate();
        
        // Login-Versuch
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return redirect()->intended('/dashboard');
        }
        
        // Fehler bei falschen Credentials
        $this->addError('email', 'Die angegebenen Anmeldedaten sind ungültig.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}