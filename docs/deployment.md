# Guia de Deploy - Blog Filament

## Visão Geral

Este guia contém instruções completas para configurar e fazer deploy do Blog Filament no VPS KingHost usando Docker e GitHub Actions CI/CD.

## Pré-requisitos

- VPS com Ubuntu 20.04+ (KingHost)
- Acesso SSH como root: `ssh root@SEU_IP_VPS`
- Domínio configurado (opcional, mas recomendado)
- Conta GitHub com o repositório: `git@github.com:charlesmuller/blog.git`

## 1. Configuração do VPS

### 1.1 Executar Script de Setup

No seu VPS, execute o script de configuração:

```bash
# Conectar no VPS
ssh root@SEU_IP_VPS

# Baixar e executar script de setup
curl -fsSL https://raw.githubusercontent.com/charlesmuller/blog/main/scripts/setup-vps.sh | bash
```

### 1.2 Configuração Manual (se preferir)

Se preferir configurar manualmente:

```bash
# Atualizar sistema
apt update && apt upgrade -y

# Instalar Docker
curl -fsSL https://get.docker.com | sh
usermod -aG docker $USER

# Instalar Docker Compose
curl -L "https://github.com/docker/compose/releases/download/v2.23.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# Instalar NGINX
apt install -y nginx certbot python3-certbot-nginx

# Configurar firewall
ufw allow ssh
ufw allow http
ufw allow https
ufw --force enable
```

### 1.3 Configurar SSH para GitHub

```bash
# Gerar chave SSH (se não existir)
ssh-keygen -t rsa -b 4096 -C "deploy@blog-filament"

# Mostrar chave pública
cat ~/.ssh/id_rsa.pub
```

**Copie a chave pública e adicione nas Deploy Keys do GitHub:**
1. Acesse: https://github.com/charlesmuller/blog/settings/keys
2. Clique em "Add deploy key"
3. Cole a chave pública
4. Marque "Allow write access"

## 2. Configuração do Repositório GitHub

### 2.1 Secrets Necessários

Configure os seguintes secrets no GitHub (Settings > Secrets and variables > Actions):

```env
# VPS Configuration
VPS_HOST=SEU_IP_VPS
VPS_USER=root
VPS_SSH_PRIVATE_KEY=<sua-chave-ssh-privada-completa>
VPS_PROJECT_PATH=/var/www/blog

# Database Configuration (seu banco externo)
DB_HOST=<host-do-seu-mysql>
DB_DATABASE=filament_blog
DB_USERNAME=<usuario-banco>
DB_PASSWORD=<senha-banco>

# Optional: Slack notifications
SLACK_WEBHOOK_URL=<webhook-do-slack>
```

### 2.2 Como Obter a Chave SSH Privada

No seu VPS:

```bash
# Mostrar chave privada
cat ~/.ssh/id_rsa
```

Copie TODO o conteúdo (incluindo `-----BEGIN` e `-----END`) e cole no secret `VPS_SSH_PRIVATE_KEY`.

## 3. Primeiro Deploy

### 3.1 Clonar Repositório no VPS

```bash
# Conectar no VPS
ssh root@SEU_IP_VPS

# Criar diretório e clonar
mkdir -p /var/www/blog
cd /var/www/blog
git clone git@github.com:charlesmuller/blog.git .
```

### 3.2 Configurar .env

```bash
# Copiar template
cp .env.example .env

# Editar configurações
nano .env
```

Configure principalmente:

```env
APP_NAME="Blog Filament"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com

# Database (seu MySQL externo)
DB_CONNECTION=mysql
DB_HOST=seu-host-mysql
DB_PORT=3306
DB_DATABASE=filament_blog
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha

# Cache & Sessions
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app
MAIL_ENCRYPTION=tls
```

### 3.3 Gerar APP_KEY

```bash
# Gerar chave da aplicação
docker-compose run --rm app php artisan key:generate
```

### 3.4 Primeiro Build

```bash
# Build e start dos containers
docker-compose up -d --build

# Executar migrations
docker-compose exec app php artisan migrate

# Criar usuário admin
docker-compose exec app php artisan make:filament-user

# Otimizar aplicação
docker-compose exec app php artisan optimize
docker-compose exec app php artisan filament:optimize

# Link de storage
docker-compose exec app php artisan storage:link
```

## 4. Configuração do Domínio (Opcional)

### 4.1 Configurar DNS

Configure seu domínio para apontar para o IP do VPS:

```
A     @     SEU_IP_VPS
A     www   SEU_IP_VPS
```

### 4.2 Configurar SSL com Let's Encrypt

```bash
# Obter certificado SSL
certbot --nginx -d seu-dominio.com -d www.seu-dominio.com

# Testar renovação automática
certbot renew --dry-run
```

### 4.3 Atualizar Configuração NGINX

Edite `/etc/nginx/sites-available/blog` e substitua `seu-dominio.com` pelo seu domínio real.

## 5. GitHub Actions CI/CD

### 5.1 Workflow Automático

O deploy acontece automaticamente quando você faz push para a branch `main`:

```bash
# Local development
git add .
git commit -m "feat: adicionar nova funcionalidade"
git push origin main
```

### 5.2 Deploy Manual

Você pode executar deploy manual via GitHub Actions:

1. Acesse: https://github.com/charlesmuller/blog/actions
2. Selecione "Deploy to Production VPS"
3. Clique "Run workflow"
4. Escolha a branch e clique "Run workflow"

### 5.3 Monitoramento

Monitore os deploys em:
- GitHub Actions: https://github.com/charlesmuller/blog/actions
- Logs do VPS: `ssh root@SEU_IP_VPS && docker-compose logs -f`

## 6. Comandos Úteis

### 6.1 Comandos no VPS

```bash
# Conectar no VPS
ssh root@SEU_IP_VPS

# Ver status dos containers
cd /var/www/blog
docker-compose ps

# Ver logs
docker-compose logs -f

# Executar comandos Laravel
docker-compose exec app php artisan migrate
docker-compose exec app php artisan optimize:clear

# Restart dos containers
docker-compose restart

# Deploy manual
/usr/local/bin/deploy-blog
```

### 6.2 Comandos de Manutenção

```bash
# Backup do banco
docker-compose exec db mysqldump -u root -p filament_blog > backup-$(date +%Y%m%d).sql

# Limpar containers antigos
docker system prune -f

# Ver uso de disco
df -h
docker system df

# Ver logs do NGINX
tail -f /var/log/nginx/blog.access.log
tail -f /var/log/nginx/blog.error.log
```

## 7. Troubleshooting

### 7.1 Problemas Comuns

**Container não inicia:**
```bash
docker-compose logs app
docker-compose exec app php artisan config:clear
```

**Erro de permissão:**
```bash
docker-compose exec app chown -R sail:sail /var/www/storage
docker-compose exec app chmod -R 775 /var/www/storage
```

**Banco não conecta:**
```bash
# Verificar configuração .env
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo()
```

**Assets não carregam:**
```bash
docker-compose exec app npm run build
docker-compose exec app php artisan storage:link
```

### 7.2 Logs Importantes

```bash
# Logs da aplicação
docker-compose exec app tail -f storage/logs/laravel.log

# Logs do NGINX (no VPS)
tail -f /var/log/nginx/blog.error.log

# Logs do GitHub Actions
# Ver na interface do GitHub
```

### 7.3 Health Check

```bash
# Verificar saúde da aplicação
curl -f http://localhost:8000/health

# Ou via domínio
curl -f https://seu-dominio.com/health
```

## 8. Estrutura de Branches

### 8.1 Branch Strategy

```
main        -> Produção (auto-deploy)
develop     -> Desenvolvimento (testes)
feature/*   -> Features (pull requests)
hotfix/*    -> Correções urgentes
```

### 8.2 Workflow de Desenvolvimento

```bash
# Criar feature
git checkout -b feature/nova-funcionalidade
git push origin feature/nova-funcionalidade

# Criar Pull Request
# Aguardar aprovação e merge

# Deploy automático acontece no merge para main
```

## 9. Monitoramento e Logs

### 9.1 Dashboards Disponíveis

- **Aplicação:** https://seu-dominio.com/admin
- **MailHog (dev):** http://localhost:8025 (apenas local)
- **GitHub Actions:** https://github.com/charlesmuller/blog/actions

### 9.2 Alertas e Notificações

Configure webhook do Slack para receber notificações de deploy:

```bash
# No GitHub Secrets
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/...
```

## 10. Backup e Restore

### 10.1 Backup Automático

```bash
# Criar script de backup
cat > /usr/local/bin/backup-blog << 'EOF'
#!/bin/bash
cd /var/www/blog
docker-compose exec -T db mysqldump -u root -p$DB_PASSWORD filament_blog > /backup/blog-$(date +%Y%m%d-%H%M%S).sql
tar -czf /backup/blog-files-$(date +%Y%m%d-%H%M%S).tar.gz storage/ public/storage/
EOF

chmod +x /usr/local/bin/backup-blog

# Configurar cron
echo "0 2 * * * /usr/local/bin/backup-blog" | crontab -
```

### 10.2 Restore

```bash
# Restore do banco
docker-compose exec -T db mysql -u root -p$DB_PASSWORD filament_blog < backup.sql

# Restore dos arquivos
tar -xzf backup-files.tar.gz
```

## 11. Performance e Otimização

### 11.1 Otimizações Laravel

```bash
# Otimizar aplicação
docker-compose exec app php artisan optimize
docker-compose exec app php artisan filament:optimize

# Configurar OPcache (já configurado no Dockerfile.prod)
# Configurar Redis para cache (já configurado)
```

### 11.2 Otimizações NGINX

O arquivo `docker/nginx/prod.conf` já inclui:
- Gzip compression
- Asset caching
- Security headers
- Buffer optimization

### 11.3 Monitoramento de Performance

```bash
# Ver uso de recursos
docker stats

# Ver logs de performance
docker-compose exec app php artisan telescope:install  # Se usar Telescope
```

---

## Contatos e Suporte

**Desenvolvedor:** Charles Muller  
**Email:** charlesmuller@rede.ulbra.br  
**GitHub:** https://github.com/charlesmuller/blog  

**VPS:** KingHost - SEU_IP_VPS  
**Repositório:** git@github.com:charlesmuller/blog.git  

---

**Última atualização:** 18/01/2025  
**Versão:** 1.0 