# Documentação do Projeto

Este diretório contém toda a documentação centralizada do Blog Filament.

## 📁 Estrutura

```
docs/
├── .cursorrules                # Regras e contexto para Cursor IDE
├── README.md                   # Este arquivo
├── deployment.md              # Guia completo de deploy no VPS
├── project-structure.md       # Análise da estrutura do projeto
├── filament-patterns.md       # Padrões específicos do Filament
├── configuracao-banco.md      # Configuração do banco de dados
└── posts_blog.json           # Dados exemplo WordPress (não versionado)
```

## 📝 Decisões de Versionamento

### ✅ **Arquivos Versionados** (importantes para o projeto)

- **`.cursorrules`** - Regras essenciais para o Cursor IDE funcionar corretamente
- **`deployment.md`** - Guia crítico para deploy em produção
- **`project-structure.md`** - Documentação da arquitetura
- **`filament-patterns.md`** - Padrões e convenções do projeto
- **`configuracao-banco.md`** - Instruções de configuração

### ❌ **Arquivos NÃO Versionados** (dados temporários/exemplo)

- **`posts_blog.json`** - Dados de exemplo do WordPress (612KB)
  - Adicionado ao `.gitignore`
  - Apenas para importação inicial
  - Não é código do projeto

## 🔧 Como Usar

### Para Desenvolvedores

1. **Leia primeiro:** [`project-structure.md`](project-structure.md)
2. **Para deploy:** [`deployment.md`](deployment.md)
3. **Padrões Filament:** [`filament-patterns.md`](filament-patterns.md)

### Para o Cursor IDE

O arquivo `.cursorrules` é automaticamente detectado pelo Cursor e fornece:

- Contexto sobre o projeto Laravel/Filament
- Padrões de código a seguir
- Configurações Docker
- Boas práticas específicas

### Para Novos Desenvolvedores

```bash
# 1. Ler documentação
cd docs/
cat project-structure.md

# 2. Configurar ambiente
cd ..
cp .env.example .env

# 3. Iniciar projeto
docker-compose up -d --build
```

## 📋 Manutenção

### Atualizações da Documentação

- Manter sincronizado com mudanças no código
- Atualizar versões de dependências
- Revisar instruções de deploy após mudanças

### Limpeza Periódica

- Verificar se links estão funcionando
- Remover documentação obsoleta
- Atualizar capturas de tela (se houver)

## 🔗 Referências Externas

- [Documentação Laravel](https://laravel.com/docs)
- [Documentação Filament](https://filamentphp.com/docs)
- [Docker Documentation](https://docs.docker.com/)
- [GitHub Actions](https://docs.github.com/en/actions)

---

**Mantido por:** Equipe de desenvolvimento  
**Última revisão:** 23/01/2025 