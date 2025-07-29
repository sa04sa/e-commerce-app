<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Produits - E-commerce</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- CSS personnalisé -->
    @if(file_exists(public_path('css/app.css')))
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @endif
    @if(file_exists(public_path('css/animations.css')))
        <link href="{{ asset('css/animations.css') }}" rel="stylesheet">
    @endif
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-shopping-cart me-2"></i>
                E-commerce
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">
                            <i class="fas fa-home me-1"></i>Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/produits">
                            <i class="fas fa-box me-1"></i>Produits
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="/favoris">
                            <i class="fas fa-heart"></i>
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle wishlist-count" style="display: none;">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="/panier">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle cart-count" style="display: none;">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div id="search-app">
        <!-- React ProductSearch sera monté ici -->
        
        <!-- Fallback si React ne charge pas -->
        <div class="container py-5">
            <h1 class="mb-4">
                <i class="fas fa-search me-2"></i>
                Rechercher des produits
            </h1>
            
            <!-- Barre de recherche simple -->
            <form method="GET" action="/produits" class="mb-4">
                <div class="input-group input-group-lg">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Rechercher un produit..." 
                           value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <!-- Résultats -->
            @if($products->count() > 0)
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-img-top-wrapper" style="height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                                </div>
                                <div class="card-body">
                                    <small class="text-muted">{{ $product->category->name ?? 'Non catégorisé' }}</small>
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-text">{{ Str::limit($product->short_description ?? $product->description, 100) }}</p>
                                    <div class="mb-3">
                                        @if($product->sale_price && $product->sale_price < $product->price)
                                            <span class="h5 text-danger fw-bold">{{ number_format($product->sale_price, 2) }}€</span>
                                            <small class="text-muted text-decoration-line-through ms-2">{{ number_format($product->price, 2) }}€</small>
                                        @else
                                            <span class="h5 text-primary fw-bold">{{ number_format($product->price, 2) }}€</span>
                                        @endif
                                    </div>
                                    @if($product->stock > 0)
                                        <small class="text-success"><i class="fas fa-check-circle"></i> En stock</small>
                                    @else
                                        <small class="text-danger"><i class="fas fa-times-circle"></i> Rupture de stock</small>
                                    @endif
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-grid">
                                        <a href="/produit/{{ $product->slug }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Voir détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                {{ $products->links() }}
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted">Aucun produit trouvé</h4>
                    <p class="text-muted">Essayez d'autres mots-clés ou parcourez nos catégories.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Données pour React -->
    <script>
        window.ecommerceData = {
            categories: @json($categories),
            baseUrl: '{{ config("app.url") }}',
            assetUrl: '{{ asset("") }}'
        };
    </script>
    
    <!-- React App -->
    @if(file_exists(public_path('js/app.js')))
        <script src="{{ mix('js/app.js') }}"></script>
    @endif
</body>
</html>