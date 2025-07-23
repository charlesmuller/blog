#!/bin/bash

# Script de Configuração do Banco de Dados
# Execute APÓS configurar o .env com suas credenciais

echo "🗄️ Configurando Banco de Dados do Blog Filament..."
echo "================================================="



echo "🧹 Limpando cache de configuração..."
php artisan config:clear

echo "🔍 Verificando configurações do banco..."
CURRENT_DB=$(php artisan tinker --execute="echo config('database.connections.mysql.database');" 2>/dev/null | tail -1)
CURRENT_HOST=$(php artisan tinker --execute="echo config('database.connections.mysql.host');" 2>/dev/null | tail -1)
echo "   Host: $CURRENT_HOST"
echo "   Database: $CURRENT_DB"

echo "🔗 Testando conexão com o banco de dados..."
CONNECTION_TEST=$(php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'OK'; } catch(Exception \$e) { echo 'ERRO: ' . \$e->getMessage(); }" 2>/dev/null | tail -1)

if [[ $CONNECTION_TEST == "OK" ]]; then
    echo "   ✅ Conexão estabelecida com sucesso!"
else
    echo "   ❌ Erro na conexão: $CONNECTION_TEST"
    echo ""
    echo "🔧 Verifique suas credenciais no .env:"
    echo "   DB_HOST=seu-host-mysql.kinghost.net"
    echo "   DB_DATABASE=seu_banco_real"
    echo "   DB_USERNAME=seu_usuario_real"
    echo "   DB_PASSWORD=sua_senha_real"
    echo ""
    echo "💡 Dica: Se você criou um banco específico 'blog-filament',"
    echo "   altere DB_DATABASE=blog-filament no .env"
    exit 1
fi

echo "📋 Verificando se as tabelas já existem..."
TABLES_EXIST=$(php artisan migrate:status > /dev/null 2>&1; echo $?)
if [[ $TABLES_EXIST -eq 0 ]]; then
    echo "   ℹ️ Tabelas já existem no banco"
    echo "   🔄 Executando migrations pendentes..."
    php artisan migrate
else
    echo "   📊 Primeira execução - criando todas as tabelas..."
    php artisan migrate
fi

echo "👤 Verificando se já existe usuário admin..."
USER_EXISTS=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1)
if [[ $USER_EXISTS -gt 0 ]]; then
    echo "   ✅ Usuários já existem no sistema ($USER_EXISTS usuários)"
    echo "   ⏭️ Pulando criação de usuário admin"
else
    echo "   📝 Criando primeiro usuário admin..."
    echo "   (Você será solicitado a inserir dados do usuário)"
    php artisan make:filament-user
fi

echo "🔗 Criando link de storage..."
php artisan storage:link

echo "📥 Verificando se posts já foram importados..."
POST_COUNT=$(php artisan tinker --execute="echo App\Models\Post::count();" 2>/dev/null | tail -1)
if [[ $POST_COUNT -gt 0 ]]; then
    echo "   ✅ Posts já importados ($POST_COUNT posts no sistema)"
    echo "   ⏭️ Pulando importação"
else
    echo "   📥 Importando posts do WordPress..."
    if php artisan blog:import-wordpress; then
        echo "   ✅ Posts importados com sucesso!"
    else
        echo "   ⚠️ Erro na importação - verifique o arquivo docs/posts_blog.json"
    fi
fi

echo "⚡ Otimizando aplicação..."
php artisan optimize
php artisan filament:optimize

echo ""
echo "🎉 Configuração concluída com sucesso!"
echo "======================================"
echo ""
echo "📊 Status do sistema:"
FINAL_USERS=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1)
FINAL_POSTS=$(php artisan tinker --execute="echo App\Models\Post::count();" 2>/dev/null | tail -1)
FINAL_CATEGORIES=$(php artisan tinker --execute="echo App\Models\Category::count();" 2>/dev/null | tail -1)
FINAL_TAGS=$(php artisan tinker --execute="echo App\Models\Tag::count();" 2>/dev/null | tail -1)

echo "   👥 Usuários: $FINAL_USERS"
echo "   📝 Posts: $FINAL_POSTS"
echo "   📂 Categorias: $FINAL_CATEGORIES"
echo "   🏷️ Tags: $FINAL_TAGS"
echo ""
echo "🌐 Acesse o blog:"
echo "   📖 Blog público: http://localhost:8000/blog"
echo "   ⚙️ Painel admin: http://localhost:8000/admin"
echo ""
echo "🐳 Para desenvolvimento com Docker:"
echo "   make up  (ou ./docker-start.sh)"
echo "" 