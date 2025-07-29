<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mon Panier - E-commerce Premium</title>
    
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
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc;
        }
        .cart-item-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .cart-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        .quantity-input {
            width: 80px;
            text-align: center;
            border-radius: 10px;
        }
        .btn-quantity {
            border-radius: 10px;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .summary-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
        }
        .coupon-input {
            border-radius: 15px;
        }
        .btn-checkout {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 15px;
            padding: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #1e293b, #334155);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-gem me-2 text-warning"></i>
                E-commerce Premium
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
                        <a class="nav-link" href="/produits">
                            <i class="fas fa-box me-1"></i>Produits
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/panier">
                            <i class="fas fa-shopping-cart me-1"></i>Panier
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
    <div id="cart-app">
        <!-- React ShoppingCart sera monté ici -->
        
        <!-- Fallback si React ne charge pas (optionnel) -->
        <noscript>
            <div class="container py-5">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    JavaScript est requis pour afficher le panier. Veuillez activer JavaScript dans votre navigateur.
                </div>
            </div>
        </noscript>
        
        <!-- Loading initial -->
        <div id="cart-loading" class="container py-5">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-3">Chargement de votre panier...</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Données pour React -->
    <script>
        window.ecommerceData = {
            cartItems: @json($cartItems ?? []),
            baseUrl: '{{ config("app.url") }}',
            assetUrl: '{{ asset("") }}',
            csrfToken: '{{ csrf_token() }}'
        };

        // Fonction pour cacher le loading une fois React chargé
        function hideCartLoading() {
            const loadingElement = document.getElementById('cart-loading');
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
        }

        // Fonctions globales pour les notifications
        window.showNotification = function(message, type = 'info', duration = 5000) {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(toast);

            // Auto-supprimer
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, duration);
        };

        // Mettre à jour le compteur du panier dans la navigation
        window.updateCartBadge = function(count) {
            const cartBadge = document.querySelector('.cart-count');
            if (cartBadge) {
                cartBadge.textContent = count || 0;
                cartBadge.style.display = count > 0 ? 'inline' : 'none';
                
                // Animation
                cartBadge.classList.add('updated');
                setTimeout(() => cartBadge.classList.remove('updated'), 400);
            }
        };

        // Charger le compteur initial
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/v1/cart/count')
                .then(response => response.json())
                .then(data => {
                    if (data.count !== undefined) {
                        updateCartBadge(data.count);
                    }
                })
                .catch(error => console.warn('Erreur lors du chargement du compteur:', error));
        });
    </script>
    
    <!-- React App -->
    @if(file_exists(public_path('js/app.js')))
        <script src="{{ mix('js/app.js') }}"></script>
        <script>
            // Cacher le loading une fois que React est chargé
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(hideCartLoading, 1000);
            });
        </script>
    @else
        <!-- Fallback si les assets React ne sont pas compilés -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('cart-loading').innerHTML = `
                    <div class="container py-5">
                        <div class="alert alert-info text-center">
                            <h4><i class="fas fa-tools me-2"></i>Application en cours de développement</h4>
                            <p>Les assets React ne sont pas encore compilés. Exécutez <code>npm run dev</code> pour compiler les composants.</p>
                            <a href="/" class="btn btn-primary mt-3">
                                <i class="fas fa-home me-2"></i>Retour à l'accueil
                            </a>
                        </div>
                    </div>
                `;
            });
        </script>
    @endif
</body>
</html>
                                    <div class="col-md-2">
                                        <div class="text-center">
                                            <div class="fw-bold">{{ number_format($item->price, 2) }}€</div>
                                            @if($item->product->sale_price && $item->product->sale_price < $item->product->price)
                                                <small class="text-muted text-decoration-line-through">
                                                    {{ number_format($item->product->price, 2) }}€
                                                </small>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Quantité -->
                                    <div class="col-md-2">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <button class="btn btn-outline-secondary btn-quantity me-2" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="form-control quantity-input" 
                                                   value="{{ $item->quantity }}" 
                                                   min="1" max="{{ $item->product->stock }}"
                                                   onchange="updateQuantity({{ $item->id }}, this.value)">
                                            <button class="btn btn-outline-secondary btn-quantity ms-2" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted d-block text-center mt-1">Stock: {{ $item->product->stock }}</small>
                                    </div>

                                    <!-- Total et actions -->
                                    <div class="col-md-2">
                                        <div class="text-center">
                                            <div class="fw-bold text-primary fs-5 mb-2 item-total">
                                                {{ number_format($item->quantity * $item->price, 2) }}€
                                            </div>
                                            <button class="btn btn-outline-danger btn-sm" onclick="removeItem({{ $item->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Résumé de commande -->
                    <div class="col-lg-4">
                        <div class="summary-card card">
                            <div class="card-header bg-primary text-white text-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-receipt me-2"></i>
                                    Résumé de commande
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <!-- Code promo -->
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-tag me-2 text-warning"></i>
                                        Code promo
                                    </h6>
                                    <div class="coupon-section">
                                        <div class="input-group">
                                            <input type="text" class="form-control coupon-input" id="couponCode" 
                                                   placeholder="Entrez votre code">
                                            <button class="btn btn-outline-primary" onclick="applyCoupon()">
                                                Appliquer
                                            </button>
                                        </div>
                                        <div id="coupon-message" class="mt-2"></div>
                                    </div>
                                </div>

                                <!-- Détails prix -->
                                <div class="price-details">
                                    @php
                                        $subtotal = $cartItems->sum(function($item) {
                                            return $item->quantity * $item->price;
                                        });
                                        $shipping = $subtotal >= 50 ? 0 : 5.99;
                                    @endphp
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Sous-total ({{ $cartItems->sum('quantity') }} article{{ $cartItems->sum('quantity') > 1 ? 's' : '' }})</span>
                                        <span id="subtotal">{{ number_format($subtotal, 2) }}€</span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2" id="discount-row" style="display: none !important;">
                                        <span class="text-success">
                                            <i class="fas fa-tag me-1"></i>
                                            Réduction
                                        </span>
                                        <span class="text-success" id="discount-amount">-0€</span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Livraison</span>
                                        <span class="{{ $shipping == 0 ? 'text-success' : '' }}">
                                            {{ $shipping == 0 ? 'GRATUITE' : number_format($shipping, 2) . '€' }}
                                        </span>
                                    </div>
                                    
                                    @if($subtotal < 50)
                                    <div class="alert alert-info small p-2 mb-3">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Livraison gratuite dès 50€ (encore {{ number_format(50 - $subtotal, 2) }}€)
                                        <div class="progress mt-1" style="height: 4px;">
                                            <div class="progress-bar bg-success" style="width: {{ ($subtotal / 50) * 100 }}%"></div>
                                        </div>
                                    </div>
                                    @endif

                                    <hr>
                                    
                                    <div class="d-flex justify-content-between fw-bold fs-5 text-primary">
                                        <span>Total</span>
                                        <span id="total">{{ number_format($subtotal + $shipping, 2) }}€</span>
                                    </div>
                                </div>

                                <!-- Boutons d'action -->
                                <div class="mt-4">
                                    <div class="d-grid gap-2">
                                        <a href="/commande" class="btn btn-checkout text-white">
                                            <i class="fas fa-credit-card me-2"></i>
                                            Passer commande
                                        </a>
                                        <a href="/produits" class="btn btn-outline-primary">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Continuer mes achats
                                        </a>
                                    </div>
                                </div>

                                <!-- Sécurité -->
                                <div class="text-center mt-4 pt-3 border-top">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Paiement 100% sécurisé SSL
                                    </small>
                                    <div class="mt-2">
                                        <i class="fab fa-cc-visa text-muted me-2"></i>
                                        <i class="fab fa-cc-mastercard text-muted me-2"></i>
                                        <i class="fab fa-cc-paypal text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Garanties -->
                        <div class="card mt-4" style="border: none; border-radius: 15px;">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Nos garanties
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <i class="fas fa-shipping-fast text-primary mb-2"></i>
                                        <div class="small">Livraison rapide</div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <i class="fas fa-undo text-success mb-2"></i>
                                        <div class="small">Retour 30j</div>
                                    </div>
                                    <div class="col-6">
                                        <i class="fas fa-headset text-info mb-2"></i>
                                        <div class="small">Support 7j/7</div>
                                    </div>
                                    <div class="col-6">
                                        <i class="fas fa-lock text-warning mb-2"></i>
                                        <div class="small">Paiement sécurisé</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Panier vide -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-shopping-cart text-muted" style="font-size: 6rem; opacity: 0.3;"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Votre panier est vide</h3>
                    <p class="text-muted mb-4 lead">
                        Découvrez nos produits exclusifs et ajoutez vos articles préférés à votre panier.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="/produits" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Découvrir nos produits
                        </a>
                        <a href="/" class="btn btn-outline-primary btn-lg px-4">
                            <i class="fas fa-home me-2"></i>
                            Retour à l'accueil
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Données pour React -->
    <script>
        window.ecommerceData = {
            cartItems: @json($cartItems),
            baseUrl: '{{ config("app.url") }}',
            assetUrl: '{{ asset("") }}'
        };
    </script>

    <!-- Fonctions JavaScript du panier -->
    <script>
        // Token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Mettre à jour la quantité
        async function updateQuantity(itemId, newQuantity) {
            if (newQuantity < 1) {
                removeItem(itemId);
                return;
            }

            try {
                const response = await fetch(`/panier/modifier/${itemId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ quantity: newQuantity })
                });

                const result = await response.json();

                if (result.success) {
                    // Mettre à jour l'affichage
                    updateCartDisplay();
                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Erreur de connexion', 'error');
            }
        }

        // Supprimer un article
        async function removeItem(itemId) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
                return;
            }

            try {
                const response = await fetch(`/panier/supprimer/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    // Supprimer l'élément du DOM
                    document.querySelector(`[data-item-id="${itemId}"]`).remove();
                    updateCartDisplay();
                    showNotification(result.message, 'success');
                    
                    // Recharger la page si le panier est vide
                    if (result.cart_count === 0) {
                        setTimeout(() => location.reload(), 1000);
                    }
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Erreur de connexion', 'error');
            }
        }

        // Vider le panier
        async function clearCart() {
            if (!confirm('Êtes-vous sûr de vouloir vider complètement votre panier ?')) {
                return;
            }

            try {
                const response = await fetch('/api/v1/cart/clear', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Erreur de connexion', 'error');
            }
        }

        // Appliquer un code promo
        async function applyCoupon() {
            const code = document.getElementById('couponCode').value.trim();
            
            if (!code) {
                showNotification('Veuillez saisir un code promo', 'error');
                return;
            }

            try {
                const response = await fetch('/api/v1/coupons/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ code })
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    document.getElementById('couponCode').value = '';
                    updateCartDisplay();
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Erreur de connexion', 'error');
            }
        }

        // Mettre à jour l'affichage du panier
        async function updateCartDisplay() {
            try {
                const response = await fetch('/api/v1/cart');
                const data = await response.json();

                if (data.success) {
                    // Mettre à jour les totaux
                    document.getElementById('subtotal').textContent = data.summary.subtotal.toFixed(2) + '€';
                    document.getElementById('total').textContent = data.summary.total.toFixed(2) + '€';

                    // Mettre à jour le compteur du panier dans la nav
                    const cartBadge = document.querySelector('.cart-count');
                    if (cartBadge) {
                        cartBadge.textContent = data.summary.items_count;
                        cartBadge.style.display = data.summary.items_count > 0 ? 'inline' : 'none';
                    }
                }
            } catch (error) {
                console.error('Erreur lors de la mise à jour:', error);
            }
        }

        // Afficher une notification
        function showNotification(message, type = 'info') {
            // Créer le toast
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(toast);

            // Supprimer après 5 secondes
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 5000);
        }

        // Codes promo suggérés
        const suggestedCoupons = [
            { code: 'BIENVENUE10', description: '10% de réduction dès 50€' },
            { code: 'REDUCTION20', description: '20% de réduction dès 100€' },
            { code: 'LIVRAISON', description: 'Livraison gratuite' }
        ];

        // Afficher les codes promo suggérés
        document.addEventListener('DOMContentLoaded', function() {
            const couponSection = document.querySelector('.coupon-section');
            if (couponSection) {
                const suggestionsHtml = `
                    <div class="mt-2">
                        <small class="text-muted">Codes disponibles :</small>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            ${suggestedCoupons.map(coupon => 
                                `<button class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('couponCode').value='${coupon.code}'" title="${coupon.description}">
                                    ${coupon.code}
                                </button>`
                            ).join('')}
                        </div>
                    </div>
                `;
                couponSection.insertAdjacentHTML('beforeend', suggestionsHtml);
            }
        });
    </script>

    <!-- React App (si disponible) -->
    @if(file_exists(public_path('js/app.js')))
        <script src="{{ mix('js/app.js') }}"></script>
    @endif
</body>
</html>