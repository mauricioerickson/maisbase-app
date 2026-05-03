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

    public $staffId;
    public $name;
    public $email;
    public $role = 'professor';
    public $password;

    public bool $showDrawer = false;
    public bool $showDeleteModal = false;
    public $idToDelete;

    /**
     * Reseta campos para novo cadastro.
     */
    public function create()
    {
        $this->reset(['name', 'email', 'role', 'password', 'staffId']);
        $this->showDrawer = true;
    }

    /**
     * Lista usuários vinculados ao tenant_id atual.
     */
    public function render()
    {
        $users = User::all();

        return view('livewire.admin.staff-management', [
            'users' => $users
        ])->layout('layouts.app');
    }

    /**
     * Carrega dados para edição.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->staffId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = ''; // Limpa senha na edição
        
        $this->showDrawer = true;
    }

    /**
     * Salva ou atualiza um membro do staff.
     */
    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->staffId,
            'role' => 'required|in:admin,professor,financeiro',
            'password' => $this->staffId ? 'nullable|min:8' : 'required|min:8',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(
            ['id' => $this->staffId],
            $data
        );

        $this->reset(['name', 'email', 'role', 'password', 'showDrawer', 'staffId']);
        $this->success($this->staffId ? 'Membro atualizado!' : 'Membro adicionado!');
    }

    public function confirmDelete($id)
    {
        $this->idToDelete = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Remove um membro do staff.
     */
    public function delete()
    {
        $user = User::findOrFail($this->idToDelete);
        
        if ($user->id === auth()->id()) {
            $this->error('Você não pode se auto-excluir!');
            $this->showDeleteModal = false;
            return;
        }

        $user->delete();
        $this->showDeleteModal = false;
        $this->success('Membro removido.');
    }
}
