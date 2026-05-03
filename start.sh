#!/bin/bash

# Define cores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${GREEN}Iniciando ambiente de desenvolvimento MaisBase...${NC}"

# Verifica node_modules
if [ ! -d "node_modules" ]; then
    echo -e "${BLUE}Instalando dependências...${NC}"
    npm install
fi

# Inicia o Vite (compilação do Tailwind e JS) em segundo plano
echo -e "${BLUE}Iniciando Vite (Tailwind + Livewire assets)...${NC}"
npm run dev &
VITE_PID=$!

# Aguarda um segundo para o Vite iniciar
sleep 1

# Inicia o servidor embutido do Laravel (PHP Artisan Serve) no primeiro plano
echo -e "${GREEN}Iniciando servidor PHP (Laravel)...${NC}"
php artisan serve

# Quando o usuário parar o artisan (Ctrl+C), este código será executado para matar o processo do Vite também
echo -e "${BLUE}Encerrando servidor Vite...${NC}"
kill $VITE_PID
