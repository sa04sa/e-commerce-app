import React, { useState, useEffect } from 'react';
import { showSuccess, showError } from './NotificationSystem';

function Wishlist() {
    const [wishlistItems, setWishlistItems] = useState([]);
    const [isLoading, setIsLoading] = useState(true);
    const [viewMode, setViewMode] = useState('grid'); // grid ou list

    useEffect(() => {
        fetchWishlist();
    }, []);

    const fetchWishlist = async () => {
        try {
            setIsLoading(true);
            const response = await fetch('/api/wishlist');
            const data = await response.json();
            
            if (response.ok) {
                setWishlistItems(data.items || []);
            }
        } catch (error) {
            console.error('Erreur lors du chargement de la wishlist:', error);
            showError('Erreur lors du chargement de vos favoris');
        } finally {
            setIsLoading(false);
        }
    };

    const removeFromWishlist = async (productId) => {
        try {
            const response = await fetch(`/api/wishlist/remove/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                setWishlistItems(prev => prev.filter(item => item.product.id !== productId));
                showSuccess('Produit retiré de vos favoris');
                updateWishlistCount(-1);
            } else {
                showError('Erreur lors de la suppression');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showError('Erreur de connexion');
        }
    };

    const addToCart = async (product) => {
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

            const result = await response.json();

            if (result.success) {
                showSuccess(`${product.name} ajouté au panier !`);
                updateCartCount(1);
            } else {
                showError('Erreur lors de l\'ajout au panier');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showError('Erreur de connexion');
        }
    };

    const shareWishlist = async () => {
        try {
            const shareData = {
                title: 'Ma liste de souhaits',
                text: 'Découvrez ma sélection de produits favoris !',
                url: window.location.href
            };

            if (navigator.share) {
                await navigator.share(shareData);
            } else {
                // Fallback: copier l'URL
                await navigator.clipboard.writeText(window.location.href);
                showSuccess('Lien copié dans le presse-papiers !');
            }
        } catch (error) {
            console.error('Erreur lors du partage:', error);
        }
    };

    const updateWishlistCount = (delta) => {
        const wishlistBadge = document.querySelector('.wishlist-count');
        if (wishlistBadge) {
            const currentCount = parseInt(wishlistBadge.textContent) || 0;
            const newCount = Math.max(0, currentCount + delta);
            wishlistBadge.textContent = newCount;
            wishlistBadge.style.display = newCount > 0 ? 'inline' : 'none';
        }
    };

    const updateCartCount = (delta) => {
        const cartBadge = document.querySelector('.cart-count');
        if (cartBadge) {
            const currentCount = parseInt(cartBadge.textContent) || 0;
            cartBadge.textContent = currentCount + delta;
        }
    };

    const formatPrice = (price) => {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(price);
    };

    const getImageUrl = (imageName) => {
        if (imageName) {
            return `${window.ecommerceData?.assetUrl || ''}storage/products/${imageName}`;
        }
        return `${window.ecommerceData?.assetUrl || ''}images/no-product.png`;
    };

    if (isLoading) {
        return (
            <div className="container py-5">
                <div className="text-center">
                    <div className="spinner-border text-primary" role="status">
                        <span className="visually-hidden">Chargement...</span>
                    </div>
                    <p className="mt-3">Chargement de vos favoris...</p>
                </div>
            </div>
        );
    }

    if (wishlistItems.length === 0) {
        return (
            <div className="container py-5">
                <div className="text-center">
                    <i className="fas fa-heart text-muted mb-4" style={{ fontSize: '4rem' }}></i>
                    <h3 className="mb-3">Votre liste de souhaits est vide</h3>
                    <p className="text-muted mb-4">
                        Parcourez nos produits et ajoutez vos coups de cœur à votre liste de souhaits.
                    </p>
                    <a href="/produits" className="btn btn-primary btn-lg">
                        <i className="fas fa-search me-2"></i>
                        Découvrir nos produits
                    </a>
                </div>
            </div>
        );
    }

    return (
        <div className="container py-5">
            {/* Header */}
            <div className="row mb-4">
                <div className="col-md-8">
                    <h1 className="mb-2">
                        <i className="fas fa-heart text-danger me-3"></i>
                        Mes Favoris
                    </h1>
                    <p className="text-muted">
                        {wishlistItems.length} produit{wishlistItems.length > 1 ? 's' : ''} dans votre liste de souhaits
                    </p>
                </div>
                <div className="col-md-4 text-md-end">
                    <div className="btn-group me-3" role="group">
                        <button
                            type="button"
                            className={`btn ${viewMode === 'grid' ? 'btn-primary' : 'btn-outline-primary'}`}
                            onClick={() => setViewMode('grid')}
                        >
                            <i className="fas fa-th"></i>
                        </button>
                        <button
                            type="button"
                            className={`btn ${viewMode === 'list' ? 'btn-primary' : 'btn-outline-primary'}`}
                            onClick={() => setViewMode('list')}
                        >
                            <i className="fas fa-list"></i>
                        </button>
                    </div>
                    <button
                        className="btn btn-outline-secondary"
                        onClick={shareWishlist}
                    >
                        <i className="fas fa-share-alt me-2"></i>
                        Partager
                    </button>
                </div>
            </div>

            {/* Vue Grille */}
            {viewMode === 'grid' && (
                <div className="row">
                    {wishlistItems.map(item => (
                        <div key={item.id} className="col-md-6 col-lg-4 col-xl-3 mb-4">
                            <div className="card h-100 shadow-sm wishlist-card">
                                {/* Image */}
                                <div className="position-relative">
                                    <img
                                        src={getImageUrl(item.product.images?.[0])}
                                        alt={item.product.name}
                                        className="card-img-top"
                                        style={{ height: '200px', objectFit: 'cover' }}
                                    />
                                    
                                    {/* Badges */}
                                    <div className="position-absolute top-0 start-0 p-2">
                                        {item.product.sale_price && (
                                            <span className="badge bg-danger">
                                                -{Math.round(((item.product.price - item.product.sale_price) / item.product.price) * 100)}%
                                            </span>
                                        )}
                                    </div>

                                    {/* Bouton supprimer */}
                                    <button
                                        className="btn btn-light btn-sm position-absolute top-0 end-0 m-2"
                                        onClick={() => removeFromWishlist(item.product.id)}
                                        title="Retirer des favoris"
                                    >
                                        <i className="fas fa-times text-danger"></i>
                                    </button>
                                </div>

                                <div className="card-body d-flex flex-column">
                                    {/* Catégorie */}
                                    <small className="text-muted mb-1">
                                        {item.product.category?.name}
                                    </small>

                                    {/* Nom */}
                                    <h6 className="card-title">
                                        <a href={`/produit/${item.product.slug}`} 
                                           className="text-decoration-none text-dark">
                                            {item.product.name}
                                        </a>
                                    </h6>

                                    {/* Prix */}
                                    <div className="mb-3">
                                        {item.product.sale_price ? (
                                            <div>
                                                <span className="h6 text-danger fw-bold">
                                                    {formatPrice(item.product.sale_price)}
                                                </span>
                                                <span className="text-muted text-decoration-line-through ms-2">
                                                    {formatPrice(item.product.price)}
                                                </span>
                                            </div>
                                        ) : (
                                            <span className="h6 text-primary fw-bold">
                                                {formatPrice(item.product.price)}
                                            </span>
                                        )}
                                    </div>

                                    {/* Actions */}
                                    <div className="mt-auto">
                                        <div className="d-grid gap-2">
                                            {item.product.stock > 0 ? (
                                                <button
                                                    className="btn btn-primary"
                                                    onClick={() => addToCart(item.product)}
                                                >
                                                    <i className="fas fa-cart-plus me-2"></i>
                                                    Ajouter au panier
                                                </button>
                                            ) : (
                                                <button className="btn btn-secondary" disabled>
                                                    <i className="fas fa-ban me-2"></i>
                                                    Rupture de stock
                                                </button>
                                            )}
                                        </div>
                                        <div className="text-center mt-2">
                                            <a href={`/produit/${item.product.slug}`} 
                                               className="btn btn-outline-primary btn-sm">
                                                <i className="fas fa-eye me-1"></i>
                                                Voir détails
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {/* Date d'ajout */}
                                <div className="card-footer bg-light text-muted text-center">
                                    <small>
                                        <i className="fas fa-calendar me-1"></i>
                                        Ajouté le {new Date(item.created_at).toLocaleDateString('fr-FR')}
                                    </small>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}

            {/* Vue Liste */}
            {viewMode === 'list' && (
                <div className="card shadow">
                    <div className="card-body p-0">
                        {wishlistItems.map((item, index) => (
                            <div key={item.id} className={`p-4 ${index < wishlistItems.length - 1 ? 'border-bottom' : ''}`}>
                                <div className="row align-items-center">
                                    {/* Image */}
                                    <div className="col-md-2">
                                        <img
                                            src={getImageUrl(item.product.images?.[0])}
                                            alt={item.product.name}
                                            className="img-fluid rounded"
                                            style={{ height: '80px', objectFit: 'cover' }}
                                        />
                                    </div>

                                    {/* Détails */}
                                    <div className="col-md-5">
                                        <h6 className="mb-1">
                                            <a href={`/produit/${item.product.slug}`} 
                                               className="text-decoration-none">
                                                {item.product.name}
                                            </a>
                                        </h6>
                                        <p className="text-muted small mb-1">
                                            {item.product.category?.name}
                                        </p>
                                        <p className="text-muted small mb-0">
                                            Ajouté le {new Date(item.created_at).toLocaleDateString('fr-FR')}
                                        </p>
                                    </div>

                                    {/* Prix */}
                                    <div className="col-md-2">
                                        {item.product.sale_price ? (
                                            <div>
                                                <div className="fw-bold text-danger">
                                                    {formatPrice(item.product.sale_price)}
                                                </div>
                                                <small className="text-muted text-decoration-line-through">
                                                    {formatPrice(item.product.price)}
                                                </small>
                                            </div>
                                        ) : (
                                            <div className="fw-bold text-primary">
                                                {formatPrice(item.product.price)}
                                            </div>
                                        )}
                                    </div>

                                    {/* Actions */}
                                    <div className="col-md-3 text-end">
                                        <div className="btn-group" role="group">
                                            {item.product.stock > 0 ? (
                                                <button
                                                    className="btn btn-primary btn-sm"
                                                    onClick={() => addToCart(item.product)}
                                                >
                                                    <i className="fas fa-cart-plus"></i>
                                                </button>
                                            ) : (
                                                <button className="btn btn-secondary btn-sm" disabled>
                                                    <i className="fas fa-ban"></i>
                                                </button>
                                            )}
                                            <a href={`/produit/${item.product.slug}`} 
                                               className="btn btn-outline-primary btn-sm">
                                                <i className="fas fa-eye"></i>
                                            </a>
                                            <button
                                                className="btn btn-outline-danger btn-sm"
                                                onClick={() => removeFromWishlist(item.product.id)}
                                                title="Retirer des favoris"
                                            >
                                                <i className="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Actions en bas */}
            <div className="row mt-4">
                <div className="col-12 text-center">
                    <a href="/produits" className="btn btn-outline-primary me-3">
                        <i className="fas fa-arrow-left me-2"></i>
                        Continuer mes achats
                    </a>
                    <button 
                        className="btn btn-outline-secondary"
                        onClick={() => {
                            if (confirm('Êtes-vous sûr de vouloir vider votre liste de souhaits ?')) {
                                // Implémenter la fonction de vidage
                                console.log('Vider la wishlist');
                            }
                        }}
                    >
                        <i className="fas fa-trash me-2"></i>
                        Vider la liste
                    </button>
                </div>
            </div>
        </div>
    );
}

// Hook pour ajouter/retirer des favoris (utilisable dans d'autres composants)
export const useWishlist = () => {
    const toggleWishlist = async (productId, isInWishlist = false) => {
        try {
            const url = isInWishlist 
                ? `/api/wishlist/remove/${productId}`
                : `/api/wishlist/add/${productId}`;
            
            const method = isInWishlist ? 'DELETE' : 'POST';
            
            const response = await fetch(url, {
                method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                const message = isInWishlist 
                    ? 'Produit retiré de vos favoris'
                    : 'Produit ajouté à vos favoris';
                
                showSuccess(message);
                
                // Mettre à jour le compteur
                const delta = isInWishlist ? -1 : 1;
                const wishlistBadge = document.querySelector('.wishlist-count');
                if (wishlistBadge) {
                    const currentCount = parseInt(wishlistBadge.textContent) || 0;
                    const newCount = Math.max(0, currentCount + delta);
                    wishlistBadge.textContent = newCount;
                    wishlistBadge.style.display = newCount > 0 ? 'inline' : 'none';
                }
                
                return !isInWishlist;
            } else {
                showError(result.message || 'Erreur lors de la modification');
                return isInWishlist;
            }
        } catch (error) {
            console.error('Erreur:', error);
            showError('Erreur de connexion');
            return isInWishlist;
        }
    };

    return { toggleWishlist };
};

export default Wishlist;