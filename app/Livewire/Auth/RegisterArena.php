<?php

// filepath: app/Livewire/Auth/RegisterArena.php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

/**
 * Componente Livewire para Onboarding de novas Arenas.
 * Gerencia a criação do Tenant e do Usuário Administrador.
 */
class RegisterArena extends Component
{
    // Dados da Arena
    public $arena_name;
    public $slug;
    public $whatsapp;
    
    // Dados do Gestor
    public $manager_name;
    public $email;
    public $password;
    public $password_confirmation;

    /**
     * Regras de validação para o formulário de Onboarding.
     */
    protected $rules = [
        'arena_name' => 'required|min:3',
        'slug' => 'required|alpha_dash|unique:tenants,slug',
        'whatsapp' => 'required|min:10',
        'manager_name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
    ];

    /**
     * Atualiza o slug automaticamente ao digitar o nome da arena.
     */
    public function updatedArenaName($value)
    {
        $this->slug = Str::slug($value);
    }

    /**
     * Processa o registro da nova arena e do administrador.
     */
    public function register()
    {
        $this->validate();

        // 1. Criar o Tenant (Escola/Arena)
        $tenant = Tenant::create([
            'uuid' => Str::uuid(),
            'name' => $this->arena_name,
            'slug' => $this->slug,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'nudge_tone' => 'amigavel',
            'active' => true,
        ]);

        // 2. Criar o Usuário Administrador vinculado ao tenant_id
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $this->manager_name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'admin',
        ]);

        // 3. Logar o usuário e definir o tenant na sessão
        Auth::login($user);
        session(['tenant_id' => $tenant->id]);

        // Redirecionar para o Dashboard Staff
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register-arena')->layout('layouts.app');
    }
}
