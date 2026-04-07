# Lebillet Template

Template reutilizável para sites com integração à API Lebillet (eventos, checkout e carrinho).

## 🚀 Quick Start

### 1. Clone e configure

```bash
cp config/site.php config/site.local.php
# Edite config/site.local.php com suas credenciais
```

### 2. Inicie o servidor

```bash
cd public
php -S localhost:8000
```

### 3. Acesse

Abra http://localhost:8000 no navegador.

---

## 📁 Estrutura

```
yanns-starter-template/
├── config/
│   └── site.php          # Configurações do site
├── app/
│   ├── Config/
│   │   └── Config.php     # Gerenciador de config
│   ├── Services/
│   │   ├── LebilletApiService.php  # API client
│   │   └── CartService.php           # Carrinho
│   ├── Helpers/
│   │   ├── Component.php   # Renderizador de componentes
│   │   └── Theme.php       # Gerenciador de temas
│   └── Views/
│       └── Components/     # Componentes base
├── public/
│   ├── index.php           # Entry point
│   ├── assets/
│   │   ├── css/            # CSS base e componentes
│   │   └── js/             # JavaScript base
│   └── themes/
│       └── default/        # Tema padrão
│           ├── theme.css
│           └── theme.js
```

---

## ⚙️ Configuração

Edite `config/site.php`:

```php
return [
    'site_id' => 'meu-site',
    'site_name' => 'Nome do Site',

    'api' => [
        'auth_token' => 'Basic SEU_TOKEN_AQUI',
        'checkout_url' => 'https://checkout.lebillet.eu/',
    ],

    'theme' => [
        'name' => 'default', // ou outro tema
    ],
];
```

---

## 🎨 Criando um Novo Tema

### Opção 1: Tema Simples (CSS/JS apenas)

1. Crie a pasta: `public/themes/meu-tema/`
2. Crie `theme.css` e `theme.js`
3. Altere `config/site.php`: `'theme.name' => 'meu-tema'`

### Opção 2: Tema com Componentes Customizados

1. Crie a pasta do tema: `public/themes/meu-tema/`
2. Crie `components/Header.php` para sobrescrever o padrão
3. O sistema usa automaticamente o componente do tema se existir

```
themes/meu-tema/
├── theme.css
├── theme.js
└── components/
    ├── Header.php      # Sobrescreve o padrão
    └── Footer.php      # Sobrescreve o padrão
```

---

## 🛒 Carrinho

O carrinho é gerenciado via JavaScript usando `localStorage`.

### Adicionar item ao carrinho:

```javascript
CartService.addItem({
    id: 'produto-123',
    name: 'Camiseta',
    price: 29.90,
    qty: 1,
    size: 'M',
    image: '/img/produto.jpg'
});
```

### Abrir modal do carrinho:

```javascript
CartService.openModal();
```

---

## 📅 Eventos

### Componente EventsSection

Renderiza lista de eventos da API:

```php
<?= Component::render('EventsSection', [
    'events' => $events,
    'title' => 'PRÓXIMOS SHOWS',
    'showViewAll' => true,
    'viewAllUrl' => '/dates',
]) ?>
```

### Checkout de Evento

O componente EventsSection já inclui o botão de checkout que abre o modal automaticamente.

Para abrir o checkout manualmente:

```javascript
CheckoutModal.open('Nome do Evento', 'event-id-123');
```

---

## 🔌 API Lebillet

### Buscar eventos (PHP):

```php
use App\Services\LebilletApiService;
use App\Config\Config;

$api = new LebilletApiService(Config::get('api'));
$events = $api->getEvents(6); // Limita a 6 eventos
```

### Verificar disponibilidade:

```php
if ($api->isAvailable()) {
    // API online
}
```

---

## 🧩 Criando Componentes

### 1. Componente Padrão (usado por todos os temas)

Crie em `app/Views/Components/MeuComponente.php`:

```php
<?php
$title = $title ?? 'Título Padrão';
?>
<div class="meu-componente">
    <h2><?= htmlspecialchars($title) ?></h2>
</div>
```

### 2. Sobrescrever em um tema

Crie em `public/themes/meu-tema/components/MeuComponente.php`:

```php
<?php
// Versão customizada do componente
$title = $title ?? 'Meu Título Customizado';
?>
<div class="meu-componente custom-theme">
    <h2 style="color: red;"><?= htmlspecialchars($title) ?></h2>
</div>
```

### Usar o componente:

```php
<?= Component::render('MeuComponente', ['title' => 'Olá Mundo']) ?>
```

---

## 🚢 Deploy

1. Copie todos os arquivos para o servidor
2. Configure o document root para a pasta `public/`
3. Certifique-se de que o PHP 8.0+ está instalado
4. Não requer banco de dados ou Composer

### Exemplo Apache (.htaccess):

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

### Exemplo Nginx:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

---

## 📄 Licença

Uso interno - Baseado no projeto YANNS.
