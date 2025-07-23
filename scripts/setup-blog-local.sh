#!/bin/bash

# Script de Setup Completo - Blog Filament
# Baseado nas instruções do .cursorrules

echo "🚀 Configurando Blog Filament com MySQL Externo..."
echo "=================================================="

# Entrar no diretório do projeto


# Verificar se o .env existe
if [ ! -f .env ]; then
    echo "📝 Criando arquivo .env..."
    cp .env.example .env
fi

echo "⚠️  IMPORTANTE: Configure seu banco MySQL externo no arquivo .env:"
echo "   DB_CONNECTION=mysql"
echo "   DB_HOST=seu-host-mysql.kinghost.net"  
echo "   DB_PORT=3306"
echo "   DB_DATABASE=seu_banco_blog"
echo "   DB_USERNAME=seu_usuario"
echo "   DB_PASSWORD=sua_senha"
echo ""
echo "   FILAMENT_FILESYSTEM_DISK=public"
echo ""
read -p "⏸️  Pressione ENTER depois de configurar o .env..."

# Gerar chave da aplicação
echo "🔑 Gerando chave da aplicação..."
php artisan key:generate

# Executar migrations
echo "📊 Executando migrations..."
php artisan migrate

# Criar usuário admin
echo "👤 Criando usuário admin do Filament..."
php artisan make:filament-user

# Criar link de storage
echo "🔗 Criando link de storage..."
php artisan storage:link

# Otimizar Filament
echo "⚡ Otimizando Filament..."
php artisan filament:optimize

# Importar posts do WordPress
echo "📥 Importando posts do WordPress..."
php artisan blog:import-wordpress

# Cache da aplicação
echo "🚀 Otimizando cache da aplicação..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "🎉 Setup concluído com sucesso!"
echo "================================"
echo ""
echo "📋 Próximos passos:"
echo "   1. Acesse o painel admin: http://localhost:8000/admin"
echo "   2. Faça login com o usuário criado"
echo "   3. Gerencie seus posts importados"
echo ""
echo "🔧 Para desenvolvimento:"
echo "   php artisan serve  # Para rodar o servidor local"
echo ""
echo "🐳 Para Docker (após configurar):"
echo "   docker-compose up -d"
echo "" 