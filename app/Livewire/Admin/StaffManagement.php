<?php

// filepath: app/Livewire/Admin/StaffManagement.php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Mary\Traits\Toast;

/**
 * Gestão de usuários da Arena (Staff).
 * Admin, Professor, Financeiro.
 */
class StaffManagement extends Component
{
    use Toast;

    public $name;
    public $email;
    public $role = 'professor';
    public $password;

    public bool $showDrawer = false;

    /**
     * Lista usuários vinculados ao tenant_id atual.
     */
    public function render()
    {
        $users = User::all(); // O GlobalScope BelongsToTenant filtra automaticamente

        return view('livewire.admin.staff-management', [
            'users' => $users
        ])->layout('layouts.app');
    }

    /**
     * Salva um novo membro do staff.
     */
    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,professor,financeiro',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'password' => Hash::make($this->password),
            // tenant_id é injetado automaticamente pela Trait BelongsToTenant
        ]);

        $this->reset(['name', 'email', 'role', 'password', 'showDrawer']);
        $this->success('Membro do Staff adicionado com sucesso!');
    }

    /**
     * Remove um membro do staff.
     */
    public function delete($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            $this->error('Você não pode se auto-excluir!');
            return;
        }

        $user->delete();
        $this->success('Membro removido.');
    }
}
