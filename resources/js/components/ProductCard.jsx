import React from 'react';

function ProductCard({ product }) {
    const addToCart = () => {
        // Fonction pour ajouter au panier
        fetch('/panier/ajouter', {
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
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produit ajouté au panier !');
                // Mettre à jour le compteur du panier
                updateCartCount();
            } else {
                alert('Erreur lors de l\'ajout au panier');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur de connexion');
        });
    };

    const updateCartCount = () => {
        // Mettre à jour le badge du panier dans la navigation
        const cartBadge = document.querySelector('.cart-count');
        if (cartBadge) {
            const currentCount = parseInt(cartBadge.textContent) || 0;
            cartBadge.textContent = currentCount + 1;
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
            return `${window.ecommerceData.assetUrl}storage/products/${product.images[0]}`;
        }
        return `${window.ecommerceData.assetUrl}images/no-product.png`;
    };

    return (
        <div className="col-md-6 col-lg-4 col-xl-3 mb-4">
            <div className="card h-100 shadow-sm product-card">
                {/* Badge promotion */}
                {product.is_on_sale && (
                    <div className="badge bg-danger position-absolute" style={{ top: '10px', left: '10px', zIndex: 1 }}>
                        -{product.discount_percentage}%
                    </div>
                )}

                {/* Badge vedette */}
                {product.featured && (
                    <div className="badge bg-warning position-absolute" style={{ top: '10px', right: '10px', zIndex: 1 }}>
                        ⭐ Vedette
                    </div>
                )}

                {/* Image du produit */}
                <div className="card-img-top-wrapper" style={{ height: '200px', overflow: 'hidden' }}>
                    <img 
                        src={getImageUrl(product)} 
                        className="card-img-top w-100 h-100" 
                        alt={product.name}
                        style={{ objectFit: 'cover' }}
                        onError={(e) => {
                            e.target.src = `${window.ecommerceData.assetUrl}images/no-product.png`;
                        }}
                    />
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
                        {product.short_description || product.description.substring(0, 100) + '...'}
                    </p>

                    {/* Prix */}
                    <div className="mb-3">
                        {product.sale_price ? (
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
                                <i className="fas fa-check-circle"></i> En stock ({product.stock})
                            </small>
                        ) : (
                            <small className="text-danger">
                                <i className="fas fa-times-circle"></i> Rupture de stock
                            </small>
                        )}
                    </div>

                    {/* Boutons d'action */}
                    <div className="mt-auto">
                        <div className="d-grid gap-2">
                            {product.stock > 0 ? (
                                <button 
                                    className="btn btn-primary"
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