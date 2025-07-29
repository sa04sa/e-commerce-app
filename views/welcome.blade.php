<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-commerce Premium - Laravel + React')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS personnalisé -->
    @if(file_exists(public_path('css/app.css')))
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @endif
    @if(file_exists(public_path('css/animations.css')))
        <link href="{{ asset('css/animations.css') }}" rel="stylesheet">
    @endif

    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --accent-color: #f59e0b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            background-color: #ffffff;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 70vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="1000,100 1000,0 0,100"/></svg>') no-repeat;
            background-size: cover;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .stat-item {
            padding: 2rem;
            border-radius: 16px;
            background: white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }

        .stat-icon.text-primary { background: rgba(37, 99, 235, 0.1); }
        .stat-icon.text-success { background: rgba(16, 185, 129, 0.1); }
        .stat-icon.text-warning { background: rgba(245, 158, 11, 0.1); }
        .stat-icon.text-info { background: rgba(6, 182, 212, 0.1); }

        .product-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .product-image {
            height: 240px;
            background: linear-gradient(45deg, #f8fafc, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .product-card:hover .product-image::before {
            transform: translateX(100%);
        }

        .badge-sale {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 6px 12px;
            border-radius: 25px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }

        .badge-featured {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 6px 12px;
            border-radius: 25px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
        }

        .price-current {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .price-original {
            text-decoration: line-through;
            color: var(--secondary-color);
            font-size: 1rem;
        }

        .btn-add-cart {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
        }

        .category-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            height: 100%;
        }

        .category-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .category-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .services-section {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            position: relative;
        }

        .services-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.05)"><polygon points="0,0 1000,100 0,100"/></svg>') no-repeat;
            background-size: cover;
        }

        .service-item {
            text-align: center;
            padding: 2rem;
            border-radius: 16px;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
        }

        .service-item:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-5px);
        }

        .section-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--dark-color), var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .animate-fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @media (max-width: 768px) {
            .hero-section {
                min-height: 60vh;
                text-align: center;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .stat-item {
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation améliorée -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #1e293b, #334155);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <i class="fas fa-gem me-2" style="color: #f59e0b;"></i>
              NDADR SHOP
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="/">
                            <i class="fas fa-home me-1"></i>Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="/produits">
                            <i class="fas fa-shopping-bag me-1"></i>Produits
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-tags me-1"></i>Catégories
                        </a>
                        <ul class="dropdown-menu border-0 shadow-lg">
                            @foreach($categories as $category)
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center" href="/categorie/{{ $category->slug }}">
                                    <span>{{ $category->name }}</span>
                                    <span class="badge bg-primary rounded-pill">{{ $category->products_count }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="/favoris">
                            <i class="fas fa-heart fs-5"></i>
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle wishlist-count rounded-pill" style="display: none;">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="/panier">
                            <i class="fas fa-shopping-cart fs-5"></i>
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle cart-count rounded-pill" style="display: none;">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section améliorée -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content animate-fade-in">
                        <h1 class="display-3 fw-bold mb-4 text-white">
                            Découvrez l'Excellence
                            <span class="d-block" style="color: #f59e0b;">Premium</span>
                        </h1>
                        <p class="lead mb-4 text-white-50">
                            Une sélection exclusive de produits haut de gamme pour transformer votre quotidien. 
                            Qualité, innovation et style réunis dans notre collection premium.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="/produits" class="btn btn-light btn-lg px-4 fw-semibold">
                                <i class="fas fa-rocket me-2"></i>
                                Explorer maintenant
                            </a>
                            <a href="#categories" class="btn btn-outline-light btn-lg px-4 fw-semibold">
                                <i class="fas fa-arrow-down me-2"></i>
                                Voir les catégories
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image animate-fade-in pulse-animation">
                        <i class="fas fa-shopping-bag text-white" style="font-size: 8rem; opacity: 0.9;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section améliorée -->
    <div class="stats-section py-5" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="stat-item animate-fade-in">
                        <div class="stat-icon text-primary">
                            <i class="fas fa-box"></i>
                        </div>
                        <h3 class="fw-bold mb-2">{{ $latestProducts->count() }}+</h3>
                        <p class="text-muted mb-0">Produits Premium</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-item animate-fade-in" style="animation-delay: 0.1s;">
                        <div class="stat-icon text-success">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h3 class="fw-bold mb-2">{{ $categories->count() }}</h3>
                        <p class="text-muted mb-0">Catégories Exclusives</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-item animate-fade-in" style="animation-delay: 0.2s;">
                        <div class="stat-icon text-warning">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <h3 class="fw-bold mb-2">24h</h3>
                        <p class="text-muted mb-0">Livraison Express</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-item animate-fade-in" style="animation-delay: 0.3s;">
                        <div class="stat-icon text-info">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="fw-bold mb-2">4.9/5</h3>
                        <p class="text-muted mb-0">Satisfaction Client</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section améliorée -->
    <div class="categories-section py-5" id="categories">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">Nos Univers</h2>
                    <p class="lead text-muted">Explorez nos collections soigneusement sélectionnées</p>
                </div>
            </div>
            <div class="row">
                @foreach($categories as $index => $category)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="category-card animate-fade-in" style="animation-delay: {{ $index * 0.1 }}s;">
                        <div class="card-body text-center p-4">
                            <div class="category-icon">
                                @switch($category->slug)
                                    @case('electronique')
                                        <i class="fas fa-laptop"></i>
                                        @break
                                    @case('vetements')
                                        <i class="fas fa-tshirt"></i>
                                        @break
                                    @case('maison-decoration')
                                        <i class="fas fa-home"></i>
                                        @break
                                    @case('sports-fitness')
                                        <i class="fas fa-dumbbell"></i>
                                        @break
                                    @case('beaute-bien-etre')
                                        <i class="fas fa-spa"></i>
                                        @break
                                    @case('livres-culture')
                                        <i class="fas fa-book"></i>
                                        @break
                                    @default
                                        <i class="fas fa-tag"></i>
                                @endswitch
                            </div>
                            <h4 class="fw-bold mb-3">{{ $category->name }}</h4>
                            <p class="text-muted mb-4">{{ $category->description }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                    {{ $category->products_count }} produit{{ $category->products_count > 1 ? 's' : '' }}
                                </span>
                                <a href="/categorie/{{ $category->slug }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                    Explorer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Featured Products améliorée -->
    @if($featuredProducts->count() > 0)
    <div class="featured-section py-5" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">
                        <i class="fas fa-star text-warning me-3"></i>
                        Sélection Premium
                    </h2>
                    <p class="lead text-muted">Nos coups de cœur sélectionnés avec passion</p>
                </div>
            </div>
            <div class="row">
                @foreach($featuredProducts as $index => $product)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card product-card animate-fade-in" style="animation-delay: {{ $index * 0.1 }}s;">
                        <div class="product-image position-relative">
                            @if($product->sale_price && $product->sale_price < $product->price)
                                <div class="badge-sale">
                                    -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                </div>
                            @endif
                            @if($product->featured)
                                <div class="badge-featured">
                                    <i class="fas fa-star me-1"></i>Premium
                                </div>
                            @endif
                            <i class="fas fa-mobile-alt text-primary" style="font-size: 4rem;"></i>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-2">
                                <span class="badge bg-light text-primary rounded-pill">{{ $product->category->name }}</span>
                            </div>
                            <h5 class="card-title fw-bold mb-3">{{ $product->name }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($product->short_description, 80) }}</p>
                            <div class="price-section mb-3">
                                @if($product->sale_price && $product->sale_price < $product->price)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="price-current">{{ number_format($product->sale_price, 0, ',', ' ') }}€</span>
                                        <span class="price-original">{{ number_format($product->price, 0, ',', ' ') }}€</span>
                                    </div>
                                @else
                                    <span class="price-current">{{ number_format($product->price, 0, ',', ' ') }}€</span>
                                @endif
                            </div>
                            <div class="stock-info mb-3">
                                @if($product->stock > 0)
                                    <small class="text-success fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i>En stock ({{ $product->stock }})
                                    </small>
                                @else
                                    <small class="text-danger fw-semibold">
                                        <i class="fas fa-times-circle me-1"></i>Rupture de stock
                                    </small>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-4 pt-0">
                            <div class="d-grid gap-2">
                                @if($product->stock > 0)
                                    <button class="btn btn-add-cart text-white fw-semibold">
                                        <i class="fas fa-cart-plus me-2"></i>Ajouter au panier
                                    </button>
                                @else
                                    <button class="btn btn-secondary" disabled>
                                        <i class="fas fa-ban me-2"></i>Indisponible
                                    </button>
                                @endif
                                <a href="/produit/{{ $product->slug }}" class="btn btn-outline-primary fw-semibold">
                                    <i class="fas fa-eye me-1"></i>Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Latest Products -->
    <div class="latest-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">
                        <i class="fas fa-clock text-primary me-3"></i>
                        Dernières Nouveautés
                    </h2>
                    <p class="lead text-muted">Découvrez nos derniers arrivages premium</p>
                </div>
            </div>
            <div class="row">
                @foreach($latestProducts->take(8) as $index => $product)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card product-card animate-fade-in" style="animation-delay: {{ $index * 0.1 }}s;">
                        <div class="product-image position-relative">
                            @if($product->sale_price && $product->sale_price < $product->price)
                                <div class="badge-sale">
                                    -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                </div>
                            @endif
                            @switch($product->category->slug)
                                @case('electronique')
                                    <i class="fas fa-laptop text-primary" style="font-size: 4rem;"></i>
                                    @break
                                @case('vetements')
                                    <i class="fas fa-tshirt text-info" style="font-size: 4rem;"></i>
                                    @break
                                @case('maison-decoration')
                                    <i class="fas fa-couch text-warning" style="font-size: 4rem;"></i>
                                    @break
                                @case('sports-fitness')
                                    <i class="fas fa-dumbbell text-success" style="font-size: 4rem;"></i>
                                    @break
                                @case('beaute-bien-etre')
                                    <i class="fas fa-spa text-danger" style="font-size: 4rem;"></i>
                                    @break
                                @case('livres-culture')
                                    <i class="fas fa-book text-secondary" style="font-size: 4rem;"></i>
                                    @break
                                @default
                                    <i class="fas fa-box text-muted" style="font-size: 4rem;"></i>
                            @endswitch
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-2">
                                <span class="badge bg-light text-primary rounded-pill">{{ $product->category->name }}</span>
                            </div>
                            <h5 class="card-title fw-bold mb-3">{{ $product->name }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($product->short_description, 80) }}</p>
                            <div class="price-section mb-3">
                                @if($product->sale_price && $product->sale_price < $product->price)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="price-current">{{ number_format($product->sale_price, 0, ',', ' ') }}€</span>
                                        <span class="price-original">{{ number_format($product->price, 0, ',', ' ') }}€</span>
                                    </div>
                                @else
                                    <span class="price-current">{{ number_format($product->price, 0, ',', ' ') }}€</span>
                                @endif
                            </div>
                            <div class="stock-info mb-3">
                                @if($product->stock > 0)
                                    <small class="text-success fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i>En stock
                                    </small>
                                @else
                                    <small class="text-danger fw-semibold">
                                        <i class="fas fa-times-circle me-1"></i>Rupture de stock
                                    </small>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-4 pt-0">
                            <div class="d-grid gap-2">
                                @if($product->stock > 0)
                                    <button class="btn btn-add-cart text-white fw-semibold">
                                        <i class="fas fa-cart-plus me-2"></i>Ajouter au panier
                                    </button>
                                @else
                                    <button class="btn btn-secondary" disabled>
                                        <i class="fas fa-ban me-2"></i>Indisponible
                                    </button>
                                @endif
                                <a href="/produit/{{ $product->slug }}" class="btn btn-outline-primary fw-semibold">
                                    <i class="fas fa-eye me-1"></i>Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-5">
                <a href="/produits" class="btn btn-primary btn-lg px-5 fw-semibold">
                    <i class="fas fa-eye me-2"></i>
                    Voir tous les produits
                </a>
            </div>
        </div>
    </div>

    <!-- Services Section améliorée -->
    <div class="services-section py-5 text-white">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold text-white mb-4">Nos Services Premium</h2>
                    <p class="lead text-white-50">Un service d'exception pour une expérience unique</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="service-item animate-fade-in">
                        <i class="fas fa-shipping-fast fa-3x text-warning mb-3"></i>
                        <h5 class="fw-bold text-white">Livraison Express</h5>
                        <p class="text-white-50 mb-0">Livraison gratuite dès 50€ partout en France</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="service-item animate-fade-in" style="animation-delay: 0.1s;">
                        <i class="fas fa-undo fa-3x text-success mb-3"></i>
                        <h5 class="fw-bold text-white">Retour Facile</h5>
                        <p class="text-white-50 mb-0">30 jours pour changer d'avis, retour gratuit</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="service-item animate-fade-in" style="animation-delay: 0.2s;">
                        <i class="fas fa-shield-alt fa-3x text-info mb-3"></i>
                        <h5 class="fw-bold text-white">Paiement Sécurisé</h5>
                        <p class="text-white-50 mb-0">Vos données protégées par cryptage SSL</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="service-item animate-fade-in" style="animation-delay: 0.3s;">
                        <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                        <h5 class="fw-bold text-white">Support Expert</h5>
                        <p class="text-white-50 mb-0">Une équipe dédiée à votre service 7j/7</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter Section -->
    <div class="newsletter-section py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h3 class="fw-bold text-white mb-4">Restez informé de nos nouveautés</h3>
                    <p class="text-white-50 mb-4">Inscrivez-vous à notre newsletter et recevez en exclusivité nos offres spéciales</p>
                    <form class="row g-3 justify-content-center">
                        <div class="col-md-6">
                            <input type="email" class="form-control form-control-lg" placeholder="Votre adresse email" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-warning btn-lg w-100 fw-semibold">
                                <i class="fas fa-paper-plane me-2"></i>S'inscrire
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer amélioré -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-4">
                        <i class="fas fa-gem me-2 text-warning"></i>
                        E-commerce Premium
                    </h5>
                    <p class="text-white-50">Votre destination pour des produits d'exception. Qualité, innovation et service premium depuis 2024.</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Navigation</h6>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white-50 text-decoration-none">Accueil</a></li>
                        <li><a href="/produits" class="text-white-50 text-decoration-none">Produits</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">À propos</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Catégories</h6>
                    <ul class="list-unstyled">
                        @foreach($categories->take(4) as $category)
                        <li><a href="/categorie/{{ $category->slug }}" class="text-white-50 text-decoration-none">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Services</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">Livraison</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Retours</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Garantie</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Support</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Légal</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">CGV</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Confidentialité</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Cookies</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Mentions légales</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-white-50">&copy; {{ date('Y') }} E-commerce Premium. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-white-50">Développé avec ❤️ par Laravel + React</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Données pour React -->
    <script>
        window.ecommerceData = {
            featuredProducts: @json($featuredProducts),
            latestProducts: @json($latestProducts), 
            categories: @json($categories),
            baseUrl: '{{ config("app.url") }}',
            assetUrl: '{{ asset("") }}'
        };
    </script>
    
    <!-- React App -->
    @if(file_exists(public_path('js/app.js')))
        <script src="{{ mix('js/app.js') }}"></script>
    @endif

    <!-- Animation au scroll -->
    <script>
        // Animation au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer tous les éléments avec animation
        document.querySelectorAll('.animate-fade-in').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.8s ease-out';
            observer.observe(el);
        });

        // Animation des boutons d'ajout au panier
        document.addEventListener('click', function(e) {
            if (e.target.matches('.btn-add-cart') || e.target.closest('.btn-add-cart')) {
                const btn = e.target.closest('.btn-add-cart');
                const originalText = btn.innerHTML;
                
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Ajout...';
                btn.disabled = true;
                
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-check me-2"></i>Ajouté !';
                    btn.classList.remove('btn-add-cart');
                    btn.classList.add('btn-success');
                    
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-add-cart');
                        btn.disabled = false;
                    }, 2000);
                }, 1000);
            }
        });
    </script>
</body>
</html>