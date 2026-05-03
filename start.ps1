# Script de inicialização para PowerShell (Windows)

Write-Host "Iniciando ambiente de desenvolvimento MaisBase..." -ForegroundColor Green

# Verifica node_modules
if (-not (Test-Path "node_modules")) {
    Write-Host "Instalando dependências do Node..." -ForegroundColor Blue
    npm install
}

# Usando concurrently para rodar ambos os processos simultaneamente
# Isso é mais robusto no Windows do que tentar rodar processos em background no bash
npx concurrently "npm run dev" "php artisan serve --host 0.0.0.0:8000" --names "VITE,PHP" --prefix-colors "blue,green"
