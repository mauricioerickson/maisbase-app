# Relatorio de Stress e Auditoria UX/UI - MaisBase

Data: 2026-05-03  
Ambiente: `local`, MySQL `maisbase_db`, Laravel 12, Livewire 4

## Escopo Executado

- Seeder de lancamento criada e executada: `StressAuditSeeder`.
- Tenant A `Arena Interior`: 80 atletas, 4 categorias, 20 horarios semanais.
- Tenant B `Elite Soccer Academy`: 155 atletas, 6 categorias, 35 horarios semanais.
- Testes automatizados adicionados em `tests/Feature/StressAuditTest.php`.
- Stress real de webhook executado via servidor local temporario em `127.0.0.1:8001`.

## Resultado dos Testes

| Area | Resultado |
| --- | --- |
| Multi-tenancy | PASS: com `session('tenant_id')`, Tenant A ve 80 atletas e Tenant B ve 155; nao houve vazamento entre dashboards/model queries autenticadas. |
| Webhook financeiro | PASS parcial: 100 POSTs paralelos retornaram sucesso; 100 invoices foram marcadas como `paid`. |
| Deadlocks | Nenhum deadlock encontrado nos logs durante o disparo. |
| Performance | 155 atletas com `guardian` e `schedules.category`: 68.43 ms no banco local. Dashboard financeiro: 8.98 ms. |
| AI tone check | Funcional, mas fallback tem estrutura repetitiva demais para WhatsApp em escala. |
| SEO/GEO | H1 local existe em `/solucoes/bauru` e `/solucoes/rio-preto`; meta description GEO esta ausente. |

## Critical Bugs

1. `Tenant` usava `SoftDeletes`, mas a tabela nao tinha `deleted_at`.
   - Impacto: consultas normais de `Tenant` quebravam em bancos novos/limpos.
   - Acao: migration incremental `2026_05_03_173000_add_deleted_at_to_tenants_table.php` adicionada; migration original tambem alinhada para novos ambientes.

2. `WebhookController` usava `App\Models\WebhookLog`, mas o model nao existia.
   - Impacto: webhook falhava antes de marcar pagamento.
   - Acao: model `WebhookLog` criado.

3. `BelongsToTenant` aplicava `where('tenant_id')` sem qualificar a tabela.
   - Impacto: queries com joins, como `Athlete::with('schedules.category')`, falhavam no MySQL com `Column 'tenant_id' ... is ambiguous`.
   - Acao: scope ajustado para `qualifyColumn('tenant_id')`.

4. Risco restante: isolamento depende de `session('tenant_id')`.
   - Impacto: jobs, comandos CLI, webhooks e consultas fora do middleware podem operar sem tenant scope.
   - Recomendacao: introduzir um `TenantContext` explicito para HTTP, queue e CLI; em jobs, passar `tenant_id` no payload e setar contexto antes das queries.

5. Risco restante: webhook atual marca pagamento somente por `external_id`.
   - Impacto: nao valida assinatura do gateway, evento recebido, valor, tenant ou idempotencia.
   - Recomendacao: validar assinatura, aceitar apenas eventos pagos, criar indice unico para `external_id` ou composto por gateway, e tornar update idempotente (`where status != paid`).

## Performance Bottlenecks

- `AthleteManagement` carrega todos os atletas com `get()`. Com 155 atletas ainda ficou aceitavel, mas com 500+ vai degradar a UX e memoria do Livewire.
- `Schedule::getOccupancyAttribute()` executa `count()` por horario quando chamado em lista, abrindo risco de N+1.
- `AttendanceSession` chama `$athlete->isCompliant()` dentro da view; cada atleta pode consultar invoice pendente e atestado, criando N+1 em uma chamada de 20 alunos.
- Falta indice composto em invoices para os filtros principais:
  - `(tenant_id, status, due_date)`
  - `(tenant_id, status, paid_at)`
  - `external_id` idealmente unico por gateway.

## UI Improvements

- Chamada em campo: os botoes de presenca tem area de toque boa (`48px`), mas contraste do estado ausente (`text-slate-300` em branco) e `opacity-70` ficam fracos sob sol forte.
- Para marcar 20 alunos em menos de 1 minuto, adicionar acoes de lote: "Marcar todos presentes", contador fixo e confirmacao unica.
- Evitar toast a cada toggle; em campo isso vira ruído visual e pode atrasar o professor.
- Bottom nav aponta para `/chamada` e `/financeiro`, mas as rotas reais sao `/campo/chamada` e `/financeiro/fluxo-caixa`.
- Material Design 3: manter cards menos aninhados, elevar contraste dos estados, e usar estado selecionado mais explicito que apenas borda lateral.

## AI Recommendations

- Prompt atual pede variacao, mas fallback tem sempre a mesma estrutura: "Ola..., tudo bem? Passando para lembrar...".
- Incluir explicitamente variaveis sem depender de `subject`: `{nome_atleta}`, `{vencimento}`, `{nome_escola}`, `{valor}` e `{pix_key}`.
- Criar 8 a 12 templates locais rotativos para fallback e limitar emoji a no maximo 1.
- Adicionar regra anti-cobranca agressiva: reconhecer rotina corrida dos pais, explicar proximo passo e evitar termos como "suspensao" no primeiro lembrete.
- Para Bauru, Aracatuba e Sao Jose do Rio Preto, variar regionalmente a abertura sem parecer localizacao artificial.

## SEO/GEO

- `/solucoes/bauru` e `/solucoes/rio-preto` renderizam cidade no H1.
- Ausentes: `meta name="description"`, canonical, Open Graph local e copy com termos de cauda longa como "software para escola de futebol em Bauru".
- Ajustar `Rio Preto` para `Sao Jose do Rio Preto` quando o slug for `rio-preto`, se essa for a premissa comercial da EMS Dev.

## Comandos de Verificacao

```bash
php artisan migrate --force
php artisan db:seed --class=StressAuditSeeder --force
php artisan test --filter=StressAuditTest
```

Resultado da suite: 4 testes, 218 assertions, todos passando.
