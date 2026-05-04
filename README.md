# MaisBase - Gestão de Elite para Escolas de Futebol

**MaisBase** é um sistema SaaS multi-tenant desenvolvido para transformar a gestão de escolas de futebol e arenas esportivas. Focado em produtividade, redução de inadimplência e reengajamento de atletas através de Inteligência Artificial e automação via WhatsApp.

---

## 🚀 Tecnologias Principais

O projeto utiliza a **TALL Stack** moderna:
- **Laravel 12**: Core do sistema.
- **Livewire 4**: Interface reativa sem sair do PHP.
- **Tailwind CSS & MaryUI**: Design system premium baseado em Material Design 3.
- **Baileys (Node.js)**: Microserviço para ponte nativa com WhatsApp.
- **Google Gemini 1.5 Flash**: Motor de IA para geração de comunicações (Nudges).

---

## 🏗️ Estrutura do Projeto

```text
maisbase-app/
├── app/
│   ├── Http/Middleware/      # CheckRole (RBAC), IdentifyTenant (Multi-tenancy)
│   ├── Jobs/                 # Processamento de Nudges (Financeiro/Retenção)
│   ├── Livewire/             # Componentes da interface reativa
│   ├── Models/               # Atletas, Invoices, Tenants, Guardians, etc.
│   └── Services/
│       ├── AI/               # NudgeGenerator (Gemini Integration)
│       └── WhatsApp/         # WhatsAppService (Bridge Integration)
├── database/migrations/      # Estrutura de banco multi-tenant
├── resources/views/          # Blade templates & layouts
├── whatsapp-service/         # [NOVO] Microserviço Node.js (Baileys Bridge)
│   ├── index.js              # API Express para controle de sessões
│   └── auth_info_*/          # Sessões de autenticação do WhatsApp (Ignorado no Git)
├── routes/web.php            # Definição de rotas com proteção de roles
├── start.sh                  # Script de inicialização (Linux/WSL)
└── start.ps1                 # Script de inicialização (Windows PowerShell)
```

---

## 🛠️ Funcionalidades de Destaque

### 1. IA Nudge System
Sistema que detecta automaticamente atletas em risco de evasão ou faturas pendentes e gera mensagens personalizadas via IA para os responsáveis, variando o tom e o conteúdo para evitar bloqueios no WhatsApp.

### 2. WhatsApp Multi-tenant (Baileys)
Cada arena conecta seu próprio WhatsApp escaneando um QR Code gerado pelo sistema. A comunicação é feita de forma nativa e direta, sem depender de APIs pagas de terceiros.

### 3. Gestão Financeira com Forecast
Dashboard financeiro completo com fluxo de caixa e previsão de receita (MRR) baseada nos planos ativos dos atletas.

### 4. Controle de Acesso (RBAC)
Sistema de permissões granular que separa as funções de:
- **Admin**: Acesso total.
- **Financeiro**: Acesso a cobranças e relatórios.
- **Treinador/Equipe**: Acesso apenas a chamadas e dados dos atletas.

---

## 🏁 Como Iniciar

O projeto inclui scripts que facilitam a inicialização de todos os serviços simultaneamente (PHP Server, Vite e WhatsApp Bridge).

### No Windows (PowerShell):
```powershell
./start.ps1
```

### No Linux/WSL:
```bash
chmod +x start.sh
./start.sh
```

---

## ⚙️ Configuração
Certifique-se de configurar as seguintes chaves no seu `.env`:
- `GEMINI_API_KEY`: Para geração de textos com IA.
- `WHATSAPP_BRIDGE_URL`: URL da ponte Node.js (padrão `http://localhost:3000`).

---
Desenvolvido com foco em alta performance e design premium.
