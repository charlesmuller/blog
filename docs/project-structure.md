# Estrutura do Projeto - Blog Filament

## Visão Geral

Este projeto consiste em um blog desenvolvido com Laravel + Filament, containerizado com Docker e pronto para deploy via GitHub Actions CI/CD.

## Estrutura de Diretórios

### Raiz do Workspace: `/home/charles.muller@king.local/projetos/blog-fillament/`

```
blog-fillament/                          # Workspace raiz
├── blog-filament/                        # 🚨 PROJETO LARAVEL PRINCIPAL
│   ├── app/                             # Código da aplicação Laravel
│   ├── config/                          # Configurações Laravel
│   ├── database/                        # Migrations, seeders, factories
│   ├── docker/                          # Configurações Docker do projeto
│   ├── docs/                           # Documentação do projeto (este arquivo)
│   ├── public/                         # Assets públicos, index.php
│   ├── resources/                      # Views, CSS, JS, Blade templates
│   ├── routes/                         # Definições de rotas
│   ├── storage/                        # Storage, logs, cache
│   ├── tests/                          # Testes unitários e de feature
│   ├── vendor/                         # Dependências Composer
│   ├── .env                           # Configurações ambiente (não versionado)
│   ├── .gitignore                     # Git ignore otimizado
│   ├── artisan                        # CLI do Laravel
│   ├── composer.json                  # Dependências PHP
│   ├── docker-compose.yml             # Orquestração containers
│   ├── Dockerfile                     # Build da imagem PHP
│   ├── Makefile                       # Comandos automatizados
│   ├── package.json                   # Dependências Node.js
│   ├── phpunit.xml                    # Configuração testes
│   └── vite.config.js                 # Build assets frontend
├── docker/                            # Docker configs gerais (workspace)
│   └── php/                          # Configs PHP globais
├── setup-database.sh                 # Script setup banco
├── docker-start.sh                   # Script inicialização Docker
└── setup-blog.sh                     # Script setup inicial blog
```

## Identificação de Problemas na Estrutura

### ✅ Problemas Corrigidos:

1. **~~Duplicação de diretórios Docker~~** ✅ RESOLVIDO
   - Mantida apenas estrutura no projeto Laravel

2. **~~Documentação espalhada~~** ✅ RESOLVIDO
   - Tudo unificado em `/blog-filament/docs/`

3. **~~Scripts de setup na raiz~~** ✅ RESOLVIDO
   - Movidos para `/blog-filament/scripts/`

4. **~~Arquivo .cursorrules fora do projeto~~** ✅ RESOLVIDO
   - Movido para `/blog-filament/docs/.cursorrules`

## Estrutura Correta Recomendada

### Organização Ideal:

```
blog-fillament/                          # Workspace/Repository raiz
├── .github/                             # GitHub Actions workflows
│   └── workflows/
│       ├── deploy.yml                   # Deploy para produção
│       └── test.yml                     # Testes automatizados
├── app/                                 # Código da aplicação Laravel
├── config/                              # Configurações Laravel
├── database/                            # Migrations, seeders, factories
├── docker/                              # Configurações Docker
│   ├── nginx/
│   │   └── default.conf                # Config NGINX
│   └── php/
│       ├── entrypoint.sh               # Script inicialização
│       └── php.ini                     # Config PHP customizada
├── docs/                               # Documentação
│   ├── .cursorrules                    # Regras do Cursor
│   ├── deployment.md                   # Guia de deploy
│   ├── filament-patterns.md           # Padrões Filament
│   ├── configuracao-banco.md          # Config banco de dados
│   ├── project-structure.md           # Este arquivo
│   └── posts_blog.json               # Dados exemplo (não versionado)
├── public/                             # Assets públicos
├── resources/                          # Views, assets
├── routes/                             # Rotas
├── scripts/                            # Scripts automação
│   ├── deploy.sh                      # Script deploy
│   ├── setup.sh                       # Setup inicial
│   └── health-check.sh                # Verificação saúde
├── storage/                            # Storage, logs
├── tests/                              # Testes
├── .cursorrules                        # Regras Cursor (raiz do projeto)
├── .env.example                        # Template configurações
├── .gitignore                          # Git ignore
├── artisan                             # CLI Laravel
├── composer.json                       # Dependências PHP
├── docker-compose.yml                  # Orquestração containers
├── Dockerfile                          # Build imagem
├── Makefile                            # Comandos automatizados
├── package.json                        # Dependências Node
└── README.md                           # Documentação principal
```

## Aplicação Laravel - Detalhes

### Estrutura da Aplicação (`/blog-filament/`)

#### `/app/` - Código da Aplicação
```
app/
├── Console/
│   └── Commands/
│       └── ImportPosts.php             # Comando import WordPress
├── Filament/
│   ├── Resources/                      # Resources Filament
│   │   ├── CategoryResource/
│   │   ├── PostResource/
│   │   └── TagResource/
│   └── Widgets/                        # Widgets dashboard
├── Http/
│   └── Controllers/
│       └── BlogController.php          # Controller blog público
├── Models/                             # Models Eloquent
│   ├── Category.php
│   ├── Post.php
│   ├── Tag.php
│   └── User.php
└── Providers/
    ├── Filament/
    │   └── AdminPanelProvider.php      # Config painel admin
    └── LivewireAssetServiceProvider.php # Assets Livewire
```

#### `/database/` - Banco de Dados
```
database/
├── factories/                          # Factories para testes
├── migrations/                         # Migrações do banco
│   ├── 2014_10_12_000000_create_users_table.php
│   ├── 2025_01_18_create_categories_table.php
│   ├── 2025_01_18_create_tags_table.php
│   └── 2025_01_18_create_posts_table.php
└── seeders/                            # Seeders
    └── DatabaseSeeder.php
```

#### `/resources/` - Frontend e Views
```
resources/
├── css/
│   └── app.css                         # Estilos customizados
├── js/
│   └── app.js                          # JavaScript customizado
└── views/
    ├── blog/                           # Views blog público
    │   ├── index.blade.php             # Lista posts
    │   ├── show.blade.php              # Post individual
    │   ├── category.blade.php          # Posts por categoria
    │   └── tag.blade.php               # Posts por tag
    ├── layouts/
    │   └── blog.blade.php              # Layout principal blog
    └── sitemap/
        └── index.blade.php             # XML sitemap
```

#### `/routes/` - Rotas
```
routes/
├── web.php                             # Rotas web principais
├── api.php                             # Rotas API (se necessário)
└── console.php                         # Comandos Artisan customizados
```

## Configurações Docker

### Docker Compose Services
```yaml
services:
  app:                                  # PHP-FPM 8.3 Alpine
  nginx:                               # NGINX proxy/web server
  redis:                               # Cache/sessions
  mailhog:                             # Mail testing (dev)
```

### Volumes e Networks
```yaml
volumes:
  redis_data:                          # Persistência Redis
networks:
  filament_network:                    # Rede interna containers
```

## Stack Tecnológica

### Backend
- **Laravel 11** - Framework PHP
- **Filament v3.3** - Admin panel
- **PHP 8.3** - Linguagem
- **MySQL 8.0** - Banco de dados
- **Redis** - Cache/sessões

### Frontend
- **Livewire v3** - Componentes dinâmicos
- **Tailwind CSS** - Estilização
- **Alpine.js** - JavaScript reativo
- **Vite** - Build assets

### Infrastructure
- **Docker** - Containerização
- **NGINX** - Web server/proxy
- **GitHub Actions** - CI/CD
- **VPS KingHost** - Hospedagem

## Pontos de Atenção para Deploy

### Arquivos Críticos
1. **`.env.example`** - Template configurações
2. **`docker-compose.yml`** - Para produção precisa ajustes
3. **`Dockerfile`** - Build otimizado para produção
4. **`.github/workflows/`** - Pipelines CI/CD

### Configurações Ambiente
- **Desenvolvimento:** Docker local com volumes
- **Produção:** VPS com volumes persistentes
- **Database:** MySQL externo (já configurado)

### Assets e Storage
- **Local:** Volume Docker
- **Produção:** Symlink storage → public/storage
- **CDN:** Configurar S3 para assets estáticos (futuro)

## Próximos Passos

1. **Reorganizar estrutura** (mover .cursorrules para raiz)
2. **Criar .env.example** no local correto
3. **Configurar GitHub Actions** workflows
4. **Otimizar Docker** para produção
5. **Setup VPS** com NGINX proxy reverso
6. **Configurar domínio** e SSL

## Comandos Importantes

### Docker Local
```bash
# Entrar no diretório correto
cd blog-filament/

# Build e start
make up
# ou
docker-compose up -d --build

# Comandos Laravel
docker-compose exec app php artisan migrate
docker-compose exec app php artisan optimize
```

### Deploy Produção
```bash
# Via GitHub Actions (automático)
git push origin main

# Manual (se necessário)
ssh root@191.252.214.90
cd /var/www/blog
git pull origin main
./scripts/deploy.sh
```

---

**Última atualização:** 18/01/2025
**Autor:** Charles Muller
**Versão:** 1.0 