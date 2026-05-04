#!/bin/bash

# Define cores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${GREEN}Iniciando ambiente de desenvolvimento MaisBase...${NC}"

# Verifica dependências do WhatsApp Bridge
if [ ! -d "whatsapp-service/node_modules" ]; then
    echo -e "${BLUE}Instalando dependências do WhatsApp Bridge...${NC}"
    (cd whatsapp-service && npm install)
fi

# Inicia o WhatsApp Bridge em segundo plano (em um subshell para não mudar o CWD)
echo -e "${BLUE}Iniciando WhatsApp Bridge (Baileys)...${NC}"
(cd whatsapp-service && node index.js) &
WA_PID=$!

# Inicia o Vite (compilação do Tailwind e JS) em segundo plano
echo -e "${BLUE}Iniciando Vite (Tailwind + Livewire assets)...${NC}"
npm run dev &
VITE_PID=$!

# Aguarda um segundo para o Vite iniciar
sleep 1

# Inicia o servidor embutido do Laravel (PHP Artisan Serve) no primeiro plano
echo -e "${GREEN}Iniciando servidor PHP (Laravel)...${NC}"
php artisan serve

# Quando o usuário parar o artisan (Ctrl+C), encerra os processos em segundo plano
echo -e "${BLUE}Encerrando processos auxiliares...${NC}"
kill $VITE_PID
kill $WA_PID
