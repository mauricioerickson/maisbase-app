<?php

// filepath: app/Traits/BelongsToTenant.php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Tenant;

/**
 * Trait para isolamento absoluto de dados por Tenant.
 * Injeta automaticamente o tenant_id e filtra todas as consultas.
 */
trait BelongsToTenant
{
    /**
     * Inicializa a trait no modelo Eloquent.
     */
    protected static function bootBelongsToTenant()
    {
        // Escopo Global: Garante que o utilizador só veja dados da sua própria escola
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (session()->has('tenant_id')) {
                $builder->where('tenant_id', session()->get('tenant_id'));
            }
        });

        // Evento Creating: Preenche automaticamente o tenant_id ao criar um novo registro
        static::creating(function ($model) {
            if (session()->has('tenant_id')) {
                $model->tenant_id = session()->get('tenant_id');
            }
        });
    }

    /**
     * Relacionamento com o modelo Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
