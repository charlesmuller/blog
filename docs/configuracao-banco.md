# 🗄️ Configuração do Banco MySQL - KingHost

## 📋 Passo a Passo

### 1. **Configure o arquivo `.env`**

Edite o arquivo `blog-filament/.env` e substitua essas linhas:

```env
# Configuração MySQL Externo - KingHost
DB_CONNECTION=mysql
DB_HOST=seu-host-mysql.kinghost.net
DB_PORT=3306
DB_DATABASE=seu_banco_de_dados
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 2. **Encontre suas credenciais na KingHost**

No painel da KingHost, procure por:
- **Host MySQL**: Geralmente algo como `mysql123.kinghost.net`
- **Nome do Banco**: O nome que você criou ou foi fornecido
- **Usuário**: Seu usuário MySQL
- **Senha**: Sua senha MySQL

### 3. **Execute o script de configuração**

Após configurar o `.env`:

```bash
./setup-database.sh
```

## 📊 Tabelas que serão criadas:

- **users** - Usuários do sistema (admin)
- **categories** - Categorias dos posts
- **tags** - Tags dos posts  
- **posts** - Posts do blog
- **post_tag** - Relacionamento posts-tags
- **migrations** - Controle de migrations
- **cache** - Cache da aplicação
- **jobs** - Filas (se necessário)
- **sessions** - Sessões de usuário

## 🔧 Comandos manuais (caso prefira):

Se preferir executar manualmente:

```bash
cd blog-filament

# Limpar cache
php artisan config:clear

# Testar conexão
php artisan migrate:status

# Criar tabelas
php artisan migrate

# Criar usuário admin
php artisan make:filament-user

# Importar posts
php artisan blog:import-wordpress

# Otimizar
php artisan optimize
```

## ❗ Problemas Comuns:

### Erro de conexão:
- Verifique host, usuário e senha
- Confirme se o banco existe na KingHost
- Teste conexão com cliente MySQL

### Erro de permissões:
- Verifique se o usuário tem permissões no banco
- Confirme se o IP está liberado (se necessário)

### Erro de encoding:
- Adicione no `.env`: `DB_CHARSET=utf8mb4`

## 📞 Suporte:

Se tiver problemas, verifique:
1. Credenciais corretas no `.env`
2. Banco de dados existe na KingHost
3. Usuário tem permissões adequadas
4. Host/porta estão corretos 