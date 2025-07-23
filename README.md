# Blog Filament

Um blog moderno desenvolvido com Laravel e Filament, containerizado com Docker e com deploy automatizado via GitHub Actions.

## 🚀 Características

- **Laravel 11** - Framework PHP moderno
- **Filament v3.3** - Painel administrativo elegante
- **Docker** - Containerização completa
- **GitHub Actions** - CI/CD automatizado
- **MySQL** - Banco de dados
- **Redis** - Cache e sessões
- **NGINX** - Servidor web otimizado
- **SSL/HTTPS** - Certificados automáticos

## 📋 Pré-requisitos

### Desenvolvimento Local
- Docker & Docker Compose
- Git
- Navegador moderno

### Produção
- VPS com Ubuntu 20.04+
- Domínio (opcional)
- Banco MySQL externo

## 🛠️ Instalação Local

### 1. Clonar o repositório

```bash
git clone git@github.com:charlesmuller/blog.git
cd blog
```

### 2. Configurar ambiente

```bash
# Copiar arquivo de ambiente
cp .env.example .env

# Editar configurações (se necessário)
nano .env
```

### 3. Iniciar containers

```bash
# Build e start
docker-compose up -d --build

# Aguardar containers iniciarem
sleep 30

# Instalar dependências
docker-compose exec app composer install
docker-compose exec app npm install

# Gerar chave da aplicação
docker-compose exec app php artisan key:generate

# Executar migrations
docker-compose exec app php artisan migrate

# Build assets
docker-compose exec app npm run build

# Criar usuário admin
docker-compose exec app php artisan make:filament-user

# Otimizar
docker-compose exec app php artisan optimize
docker-compose exec app php artisan filament:optimize
```

### 4. Acessar aplicação

- **Blog público:** http://localhost:8000/blog
- **Painel admin:** http://localhost:8000/admin
- **MailHog:** http://localhost:8025

## 🐳 Docker

### Comandos Úteis

```bash
# Ver status dos containers
docker-compose ps

# Ver logs
docker-compose logs -f

# Executar comandos Laravel
docker-compose exec app php artisan [comando]

# Parar containers
docker-compose down

# Rebuild completo
docker-compose down -v
docker-compose up -d --build
```

### Estrutura dos Containers

```
app     -> PHP 8.3-FPM (aplicação Laravel)
nginx   -> NGINX (servidor web)
redis   -> Redis (cache/sessões)
mailhog -> MailHog (emails em desenvolvimento)
```

## 📁 Estrutura do Projeto

```
blog-filament/
├── .github/workflows/          # GitHub Actions
├── app/                        # Código Laravel
│   ├── Filament/              # Resources, Widgets
│   ├── Http/Controllers/      # Controllers
│   └── Models/                # Models Eloquent
├── docker/                    # Configurações Docker
│   ├── nginx/                 # Configs NGINX
│   └── php/                   # Configs PHP
├── docs/                      # Documentação
├── resources/                 # Views, assets
│   ├── views/blog/           # Templates blog público
│   └── views/layouts/        # Layouts
├── scripts/                   # Scripts automação
├── docker-compose.yml         # Orquestração local
├── docker-compose.prod.yml    # Orquestração produção
├── Dockerfile                 # Build desenvolvimento
└── Dockerfile.prod           # Build produção
```

## 🎨 Filament Admin

### Resources Disponíveis

- **Posts** - Gerenciamento de artigos
- **Categorias** - Organização de conteúdo
- **Tags** - Marcadores
- **Usuários** - Gestão de usuários

### Funcionalidades

- Editor rich text para posts
- Upload de imagens
- SEO otimizado
- Sistema de cache
- Relatórios e métricas

## 🌐 Deploy para Produção

### Configuração do VPS

1. **Executar script de setup:**

```bash
# No VPS
curl -fsSL https://raw.githubusercontent.com/charlesmuller/blog/main/scripts/setup-vps.sh | bash
```

2. **Configurar GitHub Secrets:**

No repositório GitHub (Settings > Secrets):

```env
VPS_HOST=SEU_IP_VPS
VPS_USER=root
VPS_SSH_PRIVATE_KEY=<chave-ssh-privada>
VPS_PROJECT_PATH=/var/www/blog
DB_HOST=<host-mysql>
DB_DATABASE=filament_blog
DB_USERNAME=<usuario>
DB_PASSWORD=<senha>
```

### Deploy Automático

O deploy acontece automaticamente no push para `main`:

```bash
git add .
git commit -m "feat: nova funcionalidade"
git push origin main
```

### Deploy Manual

Via GitHub Actions:
1. Actions → "Deploy to Production VPS"
2. "Run workflow" → Escolher branch
3. Executar

## 🔧 Configuração

### Variáveis de Ambiente Importantes

```env
# Aplicação
APP_NAME="Blog Filament"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com

# Banco (MySQL externo)
DB_CONNECTION=mysql
DB_HOST=seu-host
DB_DATABASE=filament_blog
DB_USERNAME=usuario
DB_PASSWORD=senha

# Cache
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email@gmail.com
MAIL_PASSWORD=senha-app
```

### Customização

- **Views:** `resources/views/blog/`
- **Estilos:** `resources/css/app.css`
- **JavaScript:** `resources/js/app.js`
- **Configs:** `config/`

## 📊 Monitoramento

### Logs

```bash
# Logs da aplicação
docker-compose exec app tail -f storage/logs/laravel.log

# Logs do NGINX
tail -f /var/log/nginx/blog.access.log

# Logs dos containers
docker-compose logs -f
```

### Health Check

```bash
# Local
curl http://localhost:8000/health

# Produção
curl https://seu-dominio.com/health
```

### GitHub Actions

Monitore deploys em: https://github.com/charlesmuller/blog/actions

## 🔐 Segurança

### Configurações Implementadas

- Headers de segurança (NGINX)
- Content Security Policy
- SSL/HTTPS automático
- Firewall configurado
- Containers não-root
- Secrets management

### Backup

```bash
# Backup automático configurado
# Executa diariamente às 2h
/usr/local/bin/backup-blog
```

## 🐛 Troubleshooting

### Problemas Comuns

**Container não inicia:**
```bash
docker-compose logs app
docker-compose exec app php artisan config:clear
```

**Erro 500:**
```bash
docker-compose exec app php artisan optimize:clear
chmod -R 775 storage/
```

**Assets não carregam:**
```bash
docker-compose exec app npm run build
docker-compose exec app php artisan storage:link
```

### Suporte

- **Documentação:** [`docs/deployment.md`](docs/deployment.md)
- **Estrutura:** [`docs/project-structure.md`](docs/project-structure.md)
- **Issues:** https://github.com/charlesmuller/blog/issues

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/nova-feature`)
3. Commit (`git commit -m 'feat: adicionar nova feature'`)
4. Push (`git push origin feature/nova-feature`)
5. Abra um Pull Request

### Padrões de Commit

```
feat: nova funcionalidade
fix: correção de bug
docs: atualização documentação
style: formatação código
refactor: refatoração
test: adição testes
```

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para detalhes.

## 👨‍💻 Autor

**Charles Muller**
- Email: charlesmuller@rede.ulbra.br
- GitHub: [@charlesmuller](https://github.com/charlesmuller)

## 🔗 Links Úteis

- **Produção:** https://seu-dominio.com
- **Admin:** https://seu-dominio.com/admin
- **Repository:** https://github.com/charlesmuller/blog
- **Actions:** https://github.com/charlesmuller/blog/actions

---

Desenvolvido com ❤️ usando Laravel e Filament
