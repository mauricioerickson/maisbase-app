# Script de inicialização para PowerShell (Windows)

Write-Host "Iniciando ambiente de desenvolvimento MaisBase..." -ForegroundColor Green

# Verifica node_modules
if (-not (Test-Path "node_modules")) {
    Write-Host "Instalando dependências do Node..." -ForegroundColor Blue
    npm install
}

# Verifica dependências do WhatsApp Bridge
if (-not (Test-Path "whatsapp-service/node_modules")) {
    Write-Host "Instalando dependências do WhatsApp Bridge..." -ForegroundColor Blue
    Set-Location whatsapp-service
    npm install
    Set-Location ..
}

# Usando concurrently para rodar todos os processos simultaneamente
npx concurrently "npm run dev" "php artisan serve" "cd whatsapp-service && node index.js" --names "VITE,PHP,WABA" --prefix-colors "blue,green,magenta"
