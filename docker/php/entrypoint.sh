#!/bin/sh

set -e

echo "🚀 Iniciando container Filament..."

# Função para aguardar serviços
wait_for_service() {
    local host=$1
    local port=$2
    local service_name=$3
    
    echo "⏳ Aguardando $service_name ($host:$port)..."
    while ! nc -z "$host" "$port" 2>/dev/null; do
        sleep 1
    done
    echo "✅ $service_name conectado!"
}

# Aguardar serviços essenciais
if [ ! -z "$REDIS_HOST" ]; then
    wait_for_service "$REDIS_HOST" "${REDIS_PORT:-6379}" "Redis"
fi

if [ ! -z "$DB_HOST" ] && [ "$DB_HOST" != "localhost" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    wait_for_service "$DB_HOST" "${DB_PORT:-3306}" "MySQL"
fi

# Verificar e criar diretórios se necessário (sem alterar permissões)
echo "📁 Verificando estrutura de diretórios..."
[ ! -d "storage/app/public" ] && mkdir -p storage/app/public
[ ! -d "storage/framework/cache" ] && mkdir -p storage/framework/cache
[ ! -d "storage/framework/sessions" ] && mkdir -p storage/framework/sessions
[ ! -d "storage/framework/views" ] && mkdir -p storage/framework/views
[ ! -d "storage/logs" ] && mkdir -p storage/logs
[ ! -d "bootstrap/cache" ] && mkdir -p bootstrap/cache

# Verificar se o storage link existe (sem tentar criar se já existe)
echo "🔗 Verificando storage link..."
if [ ! -L "public/storage" ] && [ ! -e "public/storage" ]; then
    echo "📁 Criando storage link..."
    php artisan storage:link 2>/dev/null || echo "⚠️ Storage link já existe ou falhou"
else
    echo "✅ Storage link OK"
fi

# Otimizar apenas se especificado
if [ "$OPTIMIZE_CACHE" = "true" ]; then
    echo "⚡ Otimizando cache..."
    php artisan config:cache 2>/dev/null || echo "⚠️ Config cache falhou"
    php artisan route:cache 2>/dev/null || echo "⚠️ Route cache falhou"
    php artisan view:cache 2>/dev/null || echo "⚠️ View cache falhou"
fi

# Executar migrations se solicitado
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "🗃️ Executando migrations..."
    php artisan migrate --force 2>/dev/null || echo "⚠️ Migrations falharam"
fi

# Executar seeders se solicitado
if [ "$RUN_SEEDERS" = "true" ]; then
    echo "🌱 Executando seeders..."
    php artisan db:seed --force 2>/dev/null || echo "⚠️ Seeders falharam"
fi

# Otimizar Filament se disponível
if [ "$OPTIMIZE_FILAMENT" = "true" ]; then
    echo "⚡ Otimizando Filament..."
    php artisan filament:optimize 2>/dev/null || echo "⚠️ Filament optimize não disponível"
fi

echo "✅ Container inicializado com sucesso!"
echo "🌟 Pronto para receber conexões na porta 9000"

# Executar comando passado como parâmetro
exec "$@" 