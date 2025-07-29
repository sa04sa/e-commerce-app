import React from 'react';

function ProductCard({ product }) {
    const addToCart = async () => {
        try {
            const response = await fetch('/panier/ajouter', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    product_id: product.id, 
                    quantity: 1 
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Animation de succès
                showSuccessAnimation();
                updateCartCount(data.cart_count);
                
                // Notification
                if (window.showNotification) {
                    window.showNotification(data.message, 'success');
                }
            } else {
                if (window.showNotification) {
                    window.showNotification(data.message || 'Erreur lors de l\'ajout au panier', 'error');
                } else {
                    alert(data.message || 'Erreur lors de l\'ajout au panier');
                }
            }
        } catch (error) {
            console.error('Erreur:', error);
            if (window.showNotification) {
                window.showNotification('Erreur de connexion', 'error');
            } else {
                alert('Erreur de connexion');
            }
        }
    };

    const showSuccessAnimation = () => {
        // Créer une notification toast
        const toast = document.createElement('div');
        toast.className = 'alert alert-success alert-dismissible fade show position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            Produit ajouté au panier !
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        // Supprimer après 3 secondes
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 3000);
    };

    const updateCartCount = (count) => {
        const cartBadge = document.querySelector('.cart-count');
        if (cartBadge) {
            cartBadge.textContent = count;
            cartBadge.style.display = count > 0 ? 'inline' : 'none';
            
            // Animation du badge
            cartBadge.classList.add('updated');
            setTimeout(() => cartBadge.classList.remove('updated'), 400);
        }
    };

    const formatPrice = (price) => {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(price);
    };

    const getImageUrl = (product) => {
        if (product.images && product.images.length > 0) {
            return `${window.ecommerceData?.assetUrl || ''}storage/products/${product.images[0]}`;
        }
        return `${window.ecommerceData?.assetUrl || ''}images/no-product.png`;
    };

    const getCategoryIcon = (categorySlug) => {
        const icons = {
            'electronique': 'fas fa-laptop',
            'vetements': 'fas fa-tshirt', 
            'maison-decoration': 'fas fa-couch',
            'sports-fitness': 'fas fa-dumbbell',
            'beaute-bien-etre': 'fas fa-spa',
            'livres-culture': 'fas fa-book'
        };
        return icons[categorySlug] || 'fas fa-box';
    };

    const discountPercentage = product.sale_price && product.sale_price < product.price 
        ? Math.round(((product.price - product.sale_price) / product.price) * 100)
        : 0;

    return (
        <div className="col-md-6 col-lg-4 col-xl-3 mb-4">
            <div className="card h-100 shadow-sm product-card">
                {/* Image du produit */}
                <div className="card-img-top-wrapper position-relative" style={{ height: '200px', overflow: 'hidden' }}>
                    {/* Badges */}
                    {product.sale_price && product.sale_price < product.price && (
                        <div className="badge bg-danger position-absolute" style={{ top: '10px', left: '10px', zIndex: 1 }}>
                            -{discountPercentage}%
                        </div>
                    )}

                    {product.featured && (
                        <div className="badge bg-warning position-absolute" style={{ top: '10px', right: '10px', zIndex: 1 }}>
                            <i className="fas fa-star me-1"></i>Premium
                        </div>
                    )}

                    {/* Image ou icône */}
                    <div className="w-100 h-100 d-flex align-items-center justify-content-center" 
                         style={{ background: 'linear-gradient(45deg, #f8fafc, #e2e8f0)' }}>
                        <i className={`${getCategoryIcon(product.category?.slug)} text-primary`} 
                           style={{ fontSize: '4rem' }}></i>
                    </div>
                </div>

                <div className="card-body d-flex flex-column">
                    {/* Catégorie */}
                    <small className="text-muted mb-1">
                        {product.category ? product.category.name : 'Non catégorisé'}
                    </small>

                    {/* Nom du produit */}
                    <h5 className="card-title">
                        <a href={`/produit/${product.slug}`} className="text-decoration-none text-dark">
                            {product.name}
                        </a>
                    </h5>

                    {/* Description courte */}
                    <p className="card-text text-muted small flex-grow-1">
                        {product.short_description || 
                         (product.description && product.description.length > 100 
                             ? product.description.substring(0, 100) + '...'
                             : product.description)
                        }
                    </p>

                    {/* Prix */}
                    <div className="mb-3">
                        {product.sale_price && product.sale_price < product.price ? (
                            <div>
                                <span className="h5 text-danger fw-bold">
                                    {formatPrice(product.sale_price)}
                                </span>
                                <span className="text-muted text-decoration-line-through ms-2">
                                    {formatPrice(product.price)}
                                </span>
                            </div>
                        ) : (
                            <span className="h5 text-primary fw-bold">
                                {formatPrice(product.price)}
                            </span>
                        )}
                    </div>

                    {/* Stock */}
                    <div className="mb-3">
                        {product.stock > 0 ? (
                            <small className="text-success">
                                <i className="fas fa-check-circle me-1"></i> 
                                En stock ({product.stock})
                            </small>
                        ) : (
                            <small className="text-danger">
                                <i className="fas fa-times-circle me-1"></i> 
                                Rupture de stock
                            </small>
                        )}
                    </div>

                    {/* Boutons d'action */}
                    <div className="mt-auto">
                        <div className="d-grid gap-2">
                            {product.stock > 0 ? (
                                <button 
                                    className="btn btn-primary btn-add-cart"
                                    onClick={addToCart}
                                >
                                    <i className="fas fa-cart-plus me-2"></i>
                                    Ajouter au panier
                                </button>
                            ) : (
                                <button className="btn btn-secondary" disabled>
                                    <i className="fas fa-ban me-2"></i>
                                    Indisponible
                                </button>
                            )}
                        </div>
                        <div className="text-center mt-2">
                            <a href={`/produit/${product.slug}`} className="btn btn-outline-primary btn-sm">
                                <i className="fas fa-eye me-1"></i>
                                Voir détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default ProductCard;             