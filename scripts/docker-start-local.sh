#!/bin/bash

# Script de Inicialização Docker - Blog Filament
# Baseado nas instruções do .cursorrules

echo "🐳 Inicializando Blog Filament com Docker..."
echo "============================================="

# Entrar no diretório do projeto


# Verificar se Docker está rodando
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker não está rodando. Inicie o Docker e tente novamente."
    exit 1
fi

# Verificar se o .env existe
if [ ! -f .env ]; then
    echo "📝 Criando arquivo .env..."
    cp .env.example .env
    echo "⚠️  IMPORTANTE: Configure seu banco MySQL externo no arquivo .env:"
    echo "   DB_HOST=seu-host-mysql.kinghost.net"
    echo "   DB_DATABASE=seu_banco_blog"
    echo "   DB_USERNAME=seu_usuario"
    echo "   DB_PASSWORD=sua_senha"
    echo ""
    read -p "⏸️  Pressione ENTER depois de configurar o .env..."
fi

# Build e start containers
echo "🔨 Building containers..."
docker-compose build --no-cache

echo "🚀 Iniciando containers..."
docker-compose up -d

# Aguardar containers iniciarem
echo "⏳ Aguardando containers iniciarem..."
sleep 30

# Verificar se os containers estão rodando
if ! docker-compose ps | grep -q "Up"; then
    echo "❌ Erro ao iniciar containers"
    docker-compose logs
    exit 1
fi

echo "✅ Containers iniciados com sucesso!"

# Verificar se a aplicação já foi configurada
if ! docker-compose exec -T app php artisan migrate:status > /dev/null 2>&1; then
    echo "📊 Configurando aplicação pela primeira vez..."
    
    # Gerar chave da aplicação
    echo "🔑 Gerando chave da aplicação..."
    docker-compose exec app php artisan key:generate
    
    # Executar migrations
    echo "📊 Executando migrations..."
    docker-compose exec app php artisan migrate
    
    # Criar usuário admin
    echo "👤 Criando usuário admin..."
    docker-compose exec app php artisan make:filament-user
    
    # Importar posts do WordPress
    echo "📥 Importando posts do WordPress..."
    docker-compose exec app php artisan blog:import-wordpress
    
    echo "✅ Configuração inicial concluída!"
else
    echo "📋 Aplicação já configurada, iniciando normalmente..."
fi

# Otimizar cache
echo "⚡ Otimizando cache..."
docker-compose exec app php artisan optimize
docker-compose exec app php artisan filament:optimize

echo ""
echo "🎉 Blog Filament rodando com Docker!"
echo "===================================="
echo ""
echo "📋 URLs disponíveis:"
echo "   🌐 Blog: http://localhost:8000"
echo "   🛠️  Admin: http://localhost:8000/admin"
echo "   📧 MailHog: http://localhost:8025"
echo ""
echo "🔧 Comandos úteis:"
echo "   make help       # Ver todos os comandos disponíveis"
echo "   make shell      # Entrar no container"
echo "   make logs       # Ver logs em tempo real"
echo "   make down       # Parar containers"
echo ""
echo "🐳 Status dos containers:"
docker-compose ps

echo ""
echo "✅ Tudo pronto! Acesse o painel admin e comece a gerenciar seu blog!" 