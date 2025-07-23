.PHONY: help setup up down fresh shell logs build

help: ## Mostrar ajuda
	@echo "🐳 Comandos Docker disponíveis para Filament Blog:"
	@echo "=================================================="
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'

setup: ## Setup inicial completo com Docker
	@echo "🚀 Configurando projeto Filament com Docker..."
	@cp .env.example .env
	@echo "⚠️  Configure seu banco MySQL externo no .env antes de continuar!"
	@echo "Pressione ENTER depois de configurar..."
	@read input
	docker-compose build --no-cache
	docker-compose up -d
	@echo "⏳ Aguardando containers..."
	@sleep 30
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate
	docker-compose exec app php artisan make:filament-user
	docker-compose exec app php artisan blog:import-wordpress
	@echo "✅ Setup concluído! Acesse: http://localhost:8000/admin"

up: ## Iniciar containers
	@echo "🚀 Iniciando containers..."
	docker-compose up -d

down: ## Parar containers
	@echo "⏸️ Parando containers..."
	docker-compose down

fresh: ## Reset completo do ambiente
	@echo "🔄 Reset completo do ambiente..."
	docker-compose down -v
	docker system prune -f
	make setup

shell: ## Entrar no container app
	@echo "🐚 Abrindo shell no container..."
	docker-compose exec app bash

logs: ## Ver logs em tempo real
	@echo "📋 Exibindo logs..."
	docker-compose logs -f

logs-app: ## Ver logs apenas do app
	docker-compose logs -f app

logs-nginx: ## Ver logs apenas do nginx
	docker-compose logs -f nginx

build: ## Rebuild containers
	@echo "🔨 Rebuilding containers..."
	docker-compose build --no-cache
	docker-compose up -d

restart: ## Reiniciar containers
	@echo "🔄 Reiniciando containers..."
	docker-compose restart

migrate: ## Executar migrations
	docker-compose exec app php artisan migrate

migrate-fresh: ## Reset e executar migrations
	docker-compose exec app php artisan migrate:fresh --seed

import: ## Importar posts do WordPress
	docker-compose exec app php artisan blog:import-wordpress

cache: ## Limpar e otimizar cache
	docker-compose exec app php artisan optimize:clear
	docker-compose exec app php artisan optimize
	docker-compose exec app php artisan filament:optimize

admin: ## Criar usuário admin
	docker-compose exec app php artisan make:filament-user

test: ## Executar testes
	docker-compose exec app php artisan test

npm-dev: ## Executar npm run dev
	docker-compose exec app npm run dev

npm-build: ## Executar npm run build
	docker-compose exec app npm run build

ps: ## Verificar status dos containers
	docker-compose ps

health: ## Verificar saúde dos serviços
	@echo "🏥 Verificando saúde dos serviços..."
	@curl -f http://localhost:8000 > /dev/null 2>&1 && echo "✅ App OK" || echo "❌ App FAIL"
	@docker-compose exec redis redis-cli ping | grep -q PONG && echo "✅ Redis OK" || echo "❌ Redis FAIL" 