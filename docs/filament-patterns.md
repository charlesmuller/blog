# Padrões Filament do Projeto

## Estrutura do Projeto

### Models Principais
- User (usuários do sistema)
- Post (posts do blog)
- Category (categorias)
- Tag (tags)

### Resources Implementados
- UserResource
- PostResource  
- CategoryResource
- TagResource

## Convenções Específicas do Projeto

### Nomenclatura
- Resources: `{Model}Resource.php`
- Pages: `{Action}{Model}.php`
- Widgets: `{Purpose}Widget.php`

### Relacionamentos
- Post belongsTo Category
- Post belongsToMany Tag
- User hasMany Post

### Configurações Padrão
```php
// Configuração padrão de tabelas
->defaultSort('created_at', 'desc')
->defaultPaginationPageOption(25)
```

### Componentes Customizados
- Rich Editor para conteúdo de posts
- File Upload para imagens
- Select com busca para relacionamentos

## Exemplos Específicos do Blog

### Post Resource
```php
TextInput::make('title')->required()->maxLength(255),
RichEditor::make('content')->required(),
Select::make('category_id')
    ->relationship('category', 'name')
    ->required(),
```

### Filtros Comuns
```php
SelectFilter::make('category')
    ->relationship('category', 'name'),
Filter::make('published')
    ->query(fn ($query) => $query->whereNotNull('published_at')),
``` 