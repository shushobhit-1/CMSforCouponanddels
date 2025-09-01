<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Coupons') }}</title>
    @vite(['resources/js/admin.js'])
    <style>
        .share-circle { width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 8px; color: #fff; }
        .share-circle.facebook { background:#1877F2; }
        .share-circle.twitter { background:#1DA1F2; }
        .share-circle.whatsapp { background:#25D366; }
        .share-circle.copy { background:#6c757d; }
    </style>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // No-op: SweetAlert is loaded via admin.js; logic is in page scripts
        });
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <script type="module">
        import Swal from 'sweetalert2';
        window.Swal = Swal;
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
  <div class="container">
    <a class="navbar-brand" href="/">{{ config('app.name', 'Coupons') }}</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/coupons">Coupons</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="py-4">
    @yield('content')
</main>
</body>
</html>

