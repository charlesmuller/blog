#!/bin/bash

# Script para configurar VPS KingHost para deploy do Blog Filament
# Uso: bash setup-vps.sh

set -e

echo "🔧 Configurando VPS para deploy do Blog Filament..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Variáveis
DOMAIN="seu-dominio.com"
EMAIL="admin@seu-dominio.com"
PROJECT_PATH="/var/www/blog"
NGINX_CONFIG="/etc/nginx/sites-available/blog"
USER="www-data"

# Função para log colorido
log() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verificar se está rodando como root
if [[ $EUID -ne 0 ]]; then
   error "Este script deve ser executado como root (use sudo)"
   exit 1
fi

# Atualizar sistema
log "Atualizando sistema..."
apt update && apt upgrade -y

# Instalar dependências essenciais
log "Instalando dependências..."
apt install -y \
    curl \
    wget \
    unzip \
    git \
    nginx \
    certbot \
    python3-certbot-nginx \
    ufw \
    htop \
    tree \
    nc \
    software-properties-common \
    apt-transport-https \
    ca-certificates \
    gnupg \
    lsb-release

# Instalar Docker
log "Instalando Docker..."
if ! command -v docker &> /dev/null; then
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null
    apt update
    apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
    
    # Adicionar usuário ao grupo docker
    usermod -aG docker $USER
    log "Docker instalado com sucesso"
else
    log "Docker já está instalado"
fi

# Instalar Docker Compose (standalone)
log "Instalando Docker Compose..."
if ! command -v docker-compose &> /dev/null; then
    curl -L "https://github.com/docker/compose/releases/download/v2.23.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
    log "Docker Compose instalado com sucesso"
else
    log "Docker Compose já está instalado"
fi

# Configurar firewall
log "Configurando firewall..."
ufw default deny incoming
ufw default allow outgoing
ufw allow ssh
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable

# Criar diretório do projeto
log "Criando estrutura de diretórios..."
mkdir -p $PROJECT_PATH
mkdir -p /var/log/nginx
mkdir -p /etc/nginx/ssl

# Configurar NGINX como proxy reverso
log "Configurando NGINX..."
cat > $NGINX_CONFIG << 'EOL'
# Redirect HTTP to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name seu-dominio.com www.seu-dominio.com;
    
    # Allow Let's Encrypt challenges
    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
    
    # Redirect all other traffic to HTTPS
    location / {
        return 301 https://$server_name$request_uri;
    }
}

# HTTPS server
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name seu-dominio.com www.seu-dominio.com;

    # SSL Configuration (será configurado pelo Certbot)
    # ssl_certificate /etc/letsencrypt/live/seu-dominio.com/fullchain.pem;
    # ssl_certificate_key /etc/letsencrypt/live/seu-dominio.com/privkey.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline' 'unsafe-eval'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Proxy to Docker container
    location / {
        proxy_pass http://localhost:8000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
        
        # Timeouts
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;
        
        # Buffer sizes
        proxy_buffering on;
        proxy_buffer_size 128k;
        proxy_buffers 4 256k;
        proxy_busy_buffers_size 256k;
    }

    # Health check endpoint
    location /health {
        proxy_pass http://localhost:8000/health;
        access_log off;
    }

    # Static files optimization
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        proxy_pass http://localhost:8000;
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Logs
    access_log /var/log/nginx/blog.access.log;
    error_log /var/log/nginx/blog.error.log;
}
EOL

# Habilitar site
ln -sf $NGINX_CONFIG /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Testar configuração NGINX
nginx -t
systemctl reload nginx

# Configurar chaves SSH para GitHub (se não existir)
log "Configurando chaves SSH..."
if [ ! -f /root/.ssh/id_rsa ]; then
    ssh-keygen -t rsa -b 4096 -C "deploy@blog-filament" -f /root/.ssh/id_rsa -N ""
    log "Chave SSH criada. Adicione esta chave pública ao GitHub:"
    echo "=================================="
    cat /root/.ssh/id_rsa.pub
    echo "=================================="
else
    log "Chave SSH já existe"
fi

# Configurar Git
log "Configurando Git..."
git config --global user.name "Deploy Bot"
git config --global user.email "deploy@blog-filament.com"

# Criar script de deploy
log "Criando script de deploy..."
cat > /usr/local/bin/deploy-blog << 'EOL'
#!/bin/bash
set -e

cd /var/www/blog

echo "🚀 Iniciando deploy..."

# Backup do banco antes do deploy
echo "📦 Criando backup do banco..."
docker-compose exec -T db mysqldump -u root -p$DB_PASSWORD filament_blog > backup-$(date +%Y%m%d-%H%M%S).sql || echo "Backup failed, continuing..."

# Parar containers
echo "⏸️ Parando containers..."
docker-compose down

# Atualizar código
echo "📥 Atualizando código..."
git fetch --all
git reset --hard origin/main

# Verificar se .env existe
if [ ! -f .env ]; then
    echo "📝 Criando arquivo .env..."
    cp .env.example .env
    echo "⚠️ ATENÇÃO: Configure o arquivo .env manualmente!"
fi

# Instalar dependências
echo "📦 Instalando dependências..."
docker-compose run --rm app composer install --no-dev --optimize-autoloader --no-interaction
docker-compose run --rm app npm ci --only=production

# Build assets
echo "🏗️ Building assets..."
docker-compose run --rm app npm run build

# Iniciar containers
echo "🐳 Iniciando containers..."
docker-compose up -d --build

# Aguardar containers iniciarem
echo "⏳ Aguardando containers..."
sleep 30

# Executar migrations
echo "🗃️ Executando migrations..."
docker-compose exec -T app php artisan migrate --force

# Otimizar aplicação
echo "⚡ Otimizando aplicação..."
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache
docker-compose exec -T app php artisan filament:optimize

# Storage link
docker-compose exec -T app php artisan storage:link

echo "✅ Deploy concluído!"
EOL

chmod +x /usr/local/bin/deploy-blog

# Configurar cron para renovação SSL automática
log "Configurando renovação automática SSL..."
echo "0 12 * * * /usr/bin/certbot renew --quiet" | crontab -

# Criar diretório para certbot
mkdir -p /var/www/certbot

# Configurar log rotation
log "Configurando log rotation..."
cat > /etc/logrotate.d/blog << 'EOL'
/var/log/nginx/blog*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
    postrotate
        systemctl reload nginx
    endscript
}
EOL

# Otimizações do sistema
log "Aplicando otimizações do sistema..."

# Configurar swap se não existir
if [ ! -f /swapfile ]; then
    fallocate -l 2G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab
    log "Swap de 2GB criado"
fi

# Otimizações de rede
echo 'net.core.default_qdisc=fq' >> /etc/sysctl.conf
echo 'net.ipv4.tcp_congestion_control=bbr' >> /etc/sysctl.conf
sysctl -p

# Criar usuário para deploy (opcional)
if ! id "deploy" &>/dev/null; then
    useradd -m -s /bin/bash deploy
    usermod -aG docker deploy
    usermod -aG sudo deploy
    log "Usuário 'deploy' criado"
fi

# Status dos serviços
log "Verificando status dos serviços..."
systemctl status nginx --no-pager
systemctl status docker --no-pager

echo ""
log "✅ VPS configurado com sucesso!"
echo ""
warn "PRÓXIMOS PASSOS:"
echo "1. Configure o DNS do seu domínio para apontar para este IP"
echo "2. Adicione a chave SSH pública ao GitHub (mostrada acima)"
echo "3. Clone o repositório em $PROJECT_PATH"
echo "4. Configure o arquivo .env com suas configurações"
echo "5. Execute: certbot --nginx -d seu-dominio.com"
echo "6. Execute o primeiro deploy: /usr/local/bin/deploy-blog"
echo ""
warn "IMPORTANTE: Edite os arquivos de configuração com seu domínio real!"
echo "- $NGINX_CONFIG"
echo "- Configurações SSL após obter certificados"
echo ""

# Informações do sistema
log "Informações do sistema:"
echo "IP Público: $(curl -s ifconfig.me)"
echo "Docker: $(docker --version)"
echo "Docker Compose: $(docker-compose --version)"
echo "NGINX: $(nginx -v 2>&1)"
echo "Usuário Docker: $(groups $USER | grep docker && echo 'OK' || echo 'ERRO')" 