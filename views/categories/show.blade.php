<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->name }} - E-commerce Premium</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .product-image-main {
            height: 500px;
            background: linear-gradient(45deg, #f8fafc, #e2e8f0);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .price-current {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2563eb;
        }
        
        .price-original {
            font-size: 1.5rem;
            text-decoration: line-through;
            color: #64748b;
        }
        
        .btn-add-cart {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 15px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        }
        
        .btn-add-cart:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(37, 99, 235, 0.4);
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .related-product-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .related-product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #1e293b, #334155);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-gem me-2 text-warning"></i>
                NDADR SHOP
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Accueil</a>
                <a class="nav-link" href="/produits">Produits</a>
                <a class="nav-link" href="/panier">Panier</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Accueil</a></li>
                <li class="breadcrumb-item"><a href="/produits" class="text-decoration-none">Produits</a></li>
                <li class="breadcrumb-item"><a href="/categorie/{{ $product->category->slug }}" class="text-decoration-none">{{ $product->category->name }}</a></li>
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>

        <!-- Produit principal -->
        <div class="row mb-5">
            <!-- Images -->
            <div class="col-lg-6 mb-4">
                <div class="product-image-main position-relative">
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill">
                                -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                            </span>
                        </div>
                    @endif
                    @if($product->featured)
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-warning fs-6 px-3 py-2 rounded-pill">
                                <i class="fas fa-star me-1"></i>Premium
                            </span>
                        </div>
                    @endif
                    
                    @switch($product->category->slug)
                        @case('electronique')
                            <i class="fas fa-laptop text-primary" style="font-size: 8rem;"></i>
                            @break
                        @case('vetements')
                            <i class="fas fa-tshirt text-info" style="font-size: 8rem;"></i>
                            @break
                        @case('maison-decoration')
                            <i class="fas fa-couch text-warning" style="font-size: 8rem;"></i>
                            @break
                        @case('sports-fitness')
                            <i class="fas fa-dumbbell text-success" style="font-size: 8rem;"></i>
                            @break
                        @case('beaute-bien-etre')
                            <i class="fas fa-spa text-danger" style="font-size: 8rem;"></i>
                            @break
                        @case('livres-culture')
                            <i class="fas fa-book text-secondary" style="font-size: 8rem;"></i>
                            @break
                        @default
                            <i class="fas fa-box text-muted" style="font-size: 8rem;"></i>
                    @endswitch
                </div>
            </div>

            <!-- Détails -->
            <div class="col-lg-6">
                <div class="product-details">
                    <!-- Catégorie et nom -->
                    <div class="mb-3">
                        <span class="badge bg-light text-primary fs-6 px-3 py-2 rounded-pill mb-3">
                            {{ $product->category->name }}
                        </span>
                        <h1 class="display-5 fw-bold mb-3">{{ $product->name }}</h1>
                        <p class="text-muted">SKU: {{ $product->sku }}</p>
                    </div>

                    <!-- Prix -->
                    <div class="price-section mb-4">
                        @if($product->sale_price && $product->sale_price < $product->price)
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="price-current">{{ number_format($product->sale_price, 0, ',', ' ') }}€</span>
                                <span class="price-original">{{ number_format($product->price, 0, ',', ' ') }}€</span>
                            </div>
                            <div class="alert alert-success d-inline-block">
                                <i class="fas fa-tag me-2"></i>
                                Vous économisez {{ number_format($product->price - $product->sale_price, 0, ',', ' ') }}€
                            </div>
                        @else
                            <span class="price-current">{{ number_format($product->price, 0, ',', ' ') }}€</span>
                        @endif
                    </div>

                    <!-- Description courte -->
                    @if($product->short_description)
                        <div class="mb-4">
                            <p class="lead">{{ $product->short_description }}</p>
                        </div>
                    @endif

                    <!-- Stock -->
                    <div class="stock-section mb-4">
                        @if($product->stock > 0)
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-3 fs-4"></i>
                                <div>
                                    <strong>En stock</strong>
                                    <div class="small">{{ $product->stock }} exemplaire{{ $product->stock > 1 ? 's' : '' }} disponible{{ $product->stock > 1 ? 's' : '' }}</div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-danger d-flex align-items-center">
                                <i class="fas fa-times-circle text-danger me-3 fs-4"></i>
                                <div>
                                    <strong>Rupture de stock</strong>
                                    <div class="small">Ce produit sera bientôt de retour</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    @if($product->stock > 0)
                        <div class="actions-section mb-4">
                            <div class="row g-3">
                                <div class="col-4">
                                    <label class="form-label fw-semibold">Quantité</label>
                                    <select class="form-select form-select-lg">
                                        @for($i = 1; $i <= min($product->stock, 10); $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-8 d-flex align-items-end">
                                    <button class="btn btn-add-cart text-white w-100">
                                        <i class="fas fa-cart-plus me-2"></i>
                                        Ajouter au panier
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Actions secondaires -->
                    <div class="secondary-actions mb-4">
                        <div class="d-flex gap-3">
                            <button class="btn btn-outline-danger btn-lg">
                                <i class="fas fa-heart me-2"></i>
                                Ajouter aux favoris
                            </button>
                            <button class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-share-alt me-2"></i>
                                Partager
                            </button>
                        </div>
                    </div>

                    <!-- Garanties -->
                    <div class="guarantees">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="feature-card">
                                    <i class="fas fa-shipping-fast text-primary fs-3 mb-2"></i>
                                    <h6 class="fw-bold">Livraison rapide</h6>
                                    <small class="text-muted">Livraison gratuite dès 50€</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-card">
                                    <i class="fas fa-undo text-success fs-3 mb-2"></i>
                                    <h6 class="fw-bold">Retour gratuit</h6>
                                    <small class="text-muted">30 jours pour changer d'avis</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-card">
                                    <i class="fas fa-shield-alt text-warning fs-3 mb-2"></i>
                                    <h6 class="fw-bold">Garantie</h6>
                                    <small class="text-muted">2 ans constructeur</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-card">
                                    <i class="fas fa-headset text-info fs-3 mb-2"></i>
                                    <h6 class="fw-bold">Support</h6>
                                    <small class="text-muted">Assistance 7j/7</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglets description -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <ul class="nav nav-tabs card-header-tabs" id="productTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-semibold" id="description-tab" data-bs-toggle="tab" 
                                        data-bs-target="#description" type="button" role="tab">
                                    <i class="fas fa-align-left me-2"></i>Description
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="specs-tab" data-bs-toggle="tab" 
                                        data-bs-target="#specs" type="button" role="tab">
                                    <i class="fas fa-list me-2"></i>Caractéristiques
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="reviews-tab" data-bs-toggle="tab" 
                                        data-bs-target="#reviews" type="button" role="tab">
                                    <i class="fas fa-star me-2"></i>Avis clients
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="delivery-tab" data-bs-toggle="tab" 
                                        data-bs-target="#delivery" type="button" role="tab">
                                    <i class="fas fa-truck me-2"></i>Livraison
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="productTabsContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel">
                                <div class="py-3">
                                    <h5 class="fw-bold mb-3">Description détaillée</h5>
                                    <div class="description-content">
                                        {!! nl2br(e($product->description)) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="specs" role="tabpanel">
                                <div class="py-3">
                                    <h5 class="fw-bold mb-3">Caractéristiques techniques</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Référence</strong></td>
                                                    <td>{{ $product->sku }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Catégorie</strong></td>
                                                    <td>{{ $product->category->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Stock disponible</strong></td>
                                                    <td>{{ $product->stock }} unité{{ $product->stock > 1 ? 's' : '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Date d'ajout</strong></td>
                                                    <td>{{ $product->created_at->format('d/m/Y') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <div class="py-3">
                                    <h5 class="fw-bold mb-3">Avis clients</h5>
                                    <div class="text-center py-5">
                                        <i class="fas fa-comments text-muted mb-3" style="font-size: 3rem;"></i>
                                        <h6 class="text-muted">Aucun avis pour le moment</h6>
                                        <p class="text-muted">Soyez le premier à donner votre avis sur ce produit !</p>
                                        <button class="btn btn-primary">
                                            <i class="fas fa-edit me-2"></i>Écrire un avis
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="delivery" role="tabpanel">
                                <div class="py-3">
                                    <h5 class="fw-bold mb-3">Informations de livraison</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="delivery-option p-3 border rounded mb-3">
                                                <h6 class="fw-bold text-primary">
                                                    <i class="fas fa-truck me-2"></i>Livraison Standard
                                                </h6>
                                                <p class="mb-1"><strong>5,99€</strong> - Livraison en 3-5 jours ouvrés</p>
                                                <small class="text-muted">Gratuite dès 50€ d'achat</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="delivery-option p-3 border rounded mb-3">
                                                <h6 class="fw-bold text-warning">
                                                    <i class="fas fa-shipping-fast me-2"></i>Livraison Express
                                                </h6>
                                                <p class="mb-1"><strong>9,99€</strong> - Livraison en 24-48h</p>
                                                <small class="text-muted">Commande avant 14h</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produits similaires -->
        @if($relatedProducts->count() > 0)
        <div class="row">
            <div class="col-12">
                <h3 class="fw-bold mb-4">
                    <i class="fas fa-thumbs-up text-primary me-2"></i>
                    Produits similaires
                </h3>
                <div class="row">
                    @foreach($relatedProducts as $related)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card related-product-card h-100">
                            <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 200px; background: linear-gradient(45deg, #f8fafc, #e2e8f0);">
                                @switch($related->category->slug)
                                    @case('electronique')
                                        <i class="fas fa-laptop text-primary" style="font-size: 3rem;"></i>
                                        @break
                                    @case('vetements')
                                        <i class="fas fa-tshirt text-info" style="font-size: 3rem;"></i>
                                        @break
                                    @case('maison-decoration')
                                        <i class="fas fa-couch text-warning" style="font-size: 3rem;"></i>
                                        @break
                                    @case('sports-fitness')
                                        <i class="fas fa-dumbbell text-success" style="font-size: 3rem;"></i>
                                        @break
                                    @case('beaute-bien-etre')
                                        <i class="fas fa-spa text-danger" style="font-size: 3rem;"></i>
                                        @break
                                    @case('livres-culture')
                                        <i class="fas fa-book text-secondary" style="font-size: 3rem;"></i>
                                        @break
                                    @default
                                        <i class="fas fa-box text-muted" style="font-size: 3rem;"></i>
                                @endswitch
                            </div>
                            <div class="card-body">
                                <h6 class="card-title fw-bold">{{ $related->name }}</h6>
                                <p class="card-text small text-muted">{{ Str::limit($related->short_description, 60) }}</p>
                                <div class="price-section">
                                    @if($related->sale_price && $related->sale_price < $related->price)
                                        <span class="fw-bold text-primary">{{ number_format($related->sale_price, 0, ',', ' ') }}€</span>
                                        <small class="text-muted text-decoration-line-through ms-1">{{ number_format($related->price, 0, ',', ' ') }}€</small>
                                    @else
                                        <span class="fw-bold text-primary">{{ number_format($related->price, 0, ',', ' ') }}€</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="/produit/{{ $related->slug }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-eye me-1"></i>Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animation bouton ajout panier
        document.addEventListener('click', function(e) {
            if (e.target.matches('.btn-add-cart') || e.target.closest('.btn-add-cart')) {
                const btn = e.target.closest('.btn-add-cart');
                const originalText = btn.innerHTML;
                
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Ajout en cours...';
                btn.disabled = true;
                
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-check me-2"></i>Ajouté au panier !';
                    btn.classList.remove('btn-add-cart');
                    btn.classList.add('btn-success');
                    
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-add-cart');
                        btn.disabled = false;
                    }, 3000);
                }, 1500);
            }
        });
    </script>
</body>
</html>