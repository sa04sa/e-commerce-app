import React, { useState, useEffect } from 'react';
import { showSuccess, showError } from './NotificationSystem';

function ShoppingCart() {
    const [cartItems, setCartItems] = useState([]);
    const [loading, setLoading] = useState(true);
    const [updating, setUpdating] = useState({});
    const [couponCode, setCouponCode] = useState('');
    const [appliedCoupon, setAppliedCoupon] = useState(null);
    const [couponLoading, setCouponLoading] = useState(false);

    useEffect(() => {
        fetchCartItems();
    }, []);

    const fetchCartItems = async () => {
        try {
            setLoading(true);
            const response = await fetch('/api/v1/cart');
            const data = await response.json();
            
            if (response.ok) {
                setCartItems(data.items || []);
            } else {
                showError('Erreur lors du chargement du panier');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showError('Erreur de connexion');
        } finally {
            setLoading(false);
        }
    };

    const updateQuantity = async (itemId, newQuantity) => {
        if (newQuantity < 1) {
            removeItem(itemId);
            return;
        }

        setUpdating(prev => ({ ...prev, [itemId]: true }));

        try {
            const response = await fetch(`/api/v1/cart/${itemId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: newQuantity })
            });

            const result = await response.json();

            if (result.success) {
                setCartItems(prev => prev.map(item => 
                    item.id === itemId 
                        ? { ...item, quantity: newQuantity }
                        : item
                ));
                showSuccess('Quantité mise à jour');
                updateCartCount();
            } else {
                showError('Erreur lors de la mise à jour');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showError('Erreur de connexion');
        } finally {
            setUpdating(prev => ({ ...prev, [itemId]: false }));
        }
    };

    const removeItem = async (itemId) => {
        if (!confirm('Êtes-vous sûr de vouloir retirer ce produit du panier ?')) {
            return;
        }

        setUpdating(prev => ({ ...prev, [itemId]: true }));

        try {
            const response = await fetch(`/api/v1/cart/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                setCartItems(prev => prev.filter(item => item.id !== itemId));
                showSuccess('Produit retiré du panier');
                updateCartCount();
            } else {
                showError('Erreur lors de la suppression');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showError('Erreur de connexion');
        } finally {
            setUpdating(prev => ({ ...prev, [itemId]: false }));
        }
    };

    const clearCart = async () => {
        if (!confirm('Êtes-vous sûr de vouloir vider complètement votre panier ?')) {
            return;
        }

        try {
            const response = await fetch('/api/v1/cart/clear', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                setCartItems([]);
                setAppliedCoupon(null);
                showSuccess('Panier vidé');
                updateCartCount();
            } else {
                showError('Erreur lors du vidage du panier');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showError('Erreur de connexion');
        }
    };

    const applyCoupon = async () => {
        if (!couponCode.trim()) {
            showError('Veuillez saisir un code promo');
            return;
        }

        setCouponLoading(true);

        try {
            const response = await fetch('/api/v1/coupons/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ code: couponCode })
            });

            const result = await response.json();

            if (result.success) {
                setAppliedCoupon(result.coupon);
                showSuccess('Code promo appliqué !');
                setCouponCode('');
            } else {
                showError(result.message || 'Code promo invalide');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showError('Erreur de connexion');
        } finally {
            setCouponLoading(false);
        }
    };

    const removeCoupon = () => {
        setAppliedCoupon(null);
        showSuccess('Code promo retiré');
    };

    const updateCartCount = async () => {
        try {
            const response = await fetch('/api/v1/cart/count');
            const data = await response.json();
            
            const cartBadge = document.querySelector('.cart-count');
            if (cartBadge) {
                cartBadge.textContent = data.count || 0;
                cartBadge.style.display = data.count > 0 ? 'inline' : 'none';
            }
        } catch (error) {
            console.warn('Impossible de mettre à jour le compteur du panier:', error);
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

    const calculateSubtotal = () => {
        return cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    };

    const calculateDiscount = () => {
        if (!appliedCoupon) return 0;
        
        const subtotal = calculateSubtotal();
        
        if (appliedCoupon.type === 'percentage') {
            return (subtotal * appliedCoupon.value) / 100;
        } else {
            return Math.min(appliedCoupon.value, subtotal);
        }
    };

    const calculateTotal = () => {
        const subtotal = calculateSubtotal();
        const discount = calculateDiscount();
        return Math.max(0, subtotal - discount);
    };

    if (loading) {
        return (
            <div className="container py-5">
                <div className="text-center">
                    <div className="spinner-border text-primary" role="status">
                        <span className="visually-hidden">Chargement...</span>
                    </div>
                    <p className="mt-3">Chargement de votre panier...</p>
                </div>
            </div>
        );
    }

    if (cartItems.length === 0) {
        return (
            <div className="container py-5">
                <div className="text-center">
                    <i className="fas fa-shopping-cart text-muted mb-4" style={{ fontSize: '4rem' }}></i>
                    <h3 className="mb-3">Votre panier est vide</h3>
                    <p className="text-muted mb-4">
                        Découvrez nos produits et ajoutez vos articles préférés à votre panier.
                    </p>
                    <a href="/produits" className="btn btn-primary btn-lg">
                        <i className="fas fa-shopping-bag me-2"></i>
                        Continuer mes achats
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
                        <i className="fas fa-shopping-cart me-3"></i>
                        Mon Panier
                    </h1>
                    <p className="text-muted">
                        {cartItems.length} article{cartItems.length > 1 ? 's' : ''} dans votre panier
                    </p>
                </div>
                <div className="col-md-4 text-md-end">
                    <button
                        className="btn btn-outline-danger"
                        onClick={clearCart}
                    >
                        <i className="fas fa-trash me-2"></i>
                        Vider le panier
                    </button>
                </div>
            </div>

            <div className="row">
                {/* Articles du panier */}
                <div className="col-lg-8">
                    <div className="card shadow">
                        <div className="card-header bg-light">
                            <h5 className="mb-0">Articles</h5>
                        </div>
                        <div className="card-body p-0">
                            {cartItems.map((item, index) => (
                                <div key={item.id} className={`p-4 ${index < cartItems.length - 1 ? 'border-bottom' : ''}`}>
                                    <div className="row align-items-center">
                                        {/* Image */}
                                        <div className="col-md-2">
                                            <img
                                                src={getImageUrl(item.product?.images?.[0])}
                                                alt={item.product?.name}
                                                className="img-fluid rounded"
                                                style={{ height: '80px', objectFit: 'cover' }}
                                            />
                                        </div>

                                        {/* Détails du produit */}
                                        <div className="col-md-4">
                                            <h6 className="mb-1">
                                                <a href={`/produit/${item.product?.slug}`} 
                                                   className="text-decoration-none">
                                                    {item.product?.name}
                                                </a>
                                            </h6>
                                            <p className="text-muted small mb-1">
                                                {item.product?.category?.name}
                                            </p>
                                            <p className="text-muted small mb-0">
                                                SKU: {item.product?.sku}
                                            </p>
                                        </div>

                                        {/* Prix unitaire */}
                                        <div className="col-md-2">
                                            <div className="fw-bold">
                                                {formatPrice(item.price)}
                                            </div>
                                            {item.product?.sale_price && item.product.sale_price < item.product.price && (
                                                <small className="text-muted">
                                                    <del>{formatPrice(item.product.price)}</del>
                                                </small>
                                            )}
                                        </div>

                                        {/* Quantité */}
                                        <div className="col-md-2">
                                            <div className="input-group input-group-sm">
                                                <button
                                                    className="btn btn-outline-secondary"
                                                    type="button"
                                                    onClick={() => updateQuantity(item.id, item.quantity - 1)}
                                                    disabled={updating[item.id] || item.quantity <= 1}
                                                >
                                                    <i className="fas fa-minus"></i>
                                                </button>
                                                <input
                                                    type="number"
                                                    className="form-control text-center"
                                                    value={item.quantity}
                                                    min="1"
                                                    max={item.product?.stock}
                                                    onChange={(e) => {
                                                        const newQuantity = parseInt(e.target.value) || 1;
                                                        if (newQuantity !== item.quantity) {
                                                            updateQuantity(item.id, newQuantity);
                                                        }
                                                    }}
                                                    disabled={updating[item.id]}
                                                />
                                                <button
                                                    className="btn btn-outline-secondary"
                                                    type="button"
                                                    onClick={() => updateQuantity(item.id, item.quantity + 1)}
                                                    disabled={updating[item.id] || item.quantity >= (item.product?.stock || 0)}
                                                >
                                                    <i className="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <small className="text-muted">
                                                Stock: {item.product?.stock}
                                            </small>
                                        </div>

                                        {/* Total et actions */}
                                        <div className="col-md-2 text-end">
                                            <div className="fw-bold text-primary mb-2">
                                                {formatPrice(item.price * item.quantity)}
                                            </div>
                                            <button
                                                className="btn btn-outline-danger btn-sm"
                                                onClick={() => removeItem(item.id)}
                                                disabled={updating[item.id]}
                                            >
                                                {updating[item.id] ? (
                                                    <span className="spinner-border spinner-border-sm"></span>
                                                ) : (
                                                    <i className="fas fa-trash"></i>
                                                )}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Code promo */}
                    <div className="card shadow mt-4">
                        <div className="card-body">
                            <h6 className="card-title">
                                <i className="fas fa-tag me-2"></i>
                                Code promo
                            </h6>
                            
                            {appliedCoupon ? (
                                <div className="alert alert-success d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{appliedCoupon.code}</strong> appliqué
                                        <div className="small">
                                            Réduction de {appliedCoupon.type === 'percentage' 
                                                ? `${appliedCoupon.value}%` 
                                                : formatPrice(appliedCoupon.value)
                                            }
                                        </div>
                                    </div>
                                    <button
                                        className="btn btn-outline-danger btn-sm"
                                        onClick={removeCoupon}
                                    >
                                        <i className="fas fa-times"></i>
                                    </button>
                                </div>
                            ) : (
                                <div className="input-group">
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Entrez votre code promo"
                                        value={couponCode}
                                        onChange={(e) => setCouponCode(e.target.value.toUpperCase())}
                                        disabled={couponLoading}
                                    />
                                    <button
                                        className="btn btn-primary"
                                        type="button"
                                        onClick={applyCoupon}
                                        disabled={couponLoading || !couponCode.trim()}
                                    >
                                        {couponLoading ? (
                                            <span className="spinner-border spinner-border-sm"></span>
                                        ) : (
                                            'Appliquer'
                                        )}
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Résumé de commande */}
                <div className="col-lg-4">
                    <div className="card shadow sticky-top" style={{ top: '20px' }}>
                        <div className="card-header bg-primary text-white">
                            <h5 className="mb-0">
                                <i className="fas fa-receipt me-2"></i>
                                Résumé de commande
                            </h5>
                        </div>
                        <div className="card-body">
                            {/* Sous-total */}
                            <div className="d-flex justify-content-between mb-2">
                                <span>Sous-total ({cartItems.length} article{cartItems.length > 1 ? 's' : ''})</span>
                                <span>{formatPrice(calculateSubtotal())}</span>
                            </div>

                            {/* Réduction */}
                            {appliedCoupon && calculateDiscount() > 0 && (
                                <div className="d-flex justify-content-between mb-2 text-success">
                                    <span>
                                        <i className="fas fa-tag me-1"></i>
                                        Réduction ({appliedCoupon.code})
                                    </span>
                                    <span>-{formatPrice(calculateDiscount())}</span>
                                </div>
                            )}

                            {/* Livraison */}
                            <div className="d-flex justify-content-between mb-2">
                                <span>Livraison</span>
                                <span className="text-success">
                                    {calculateTotal() >= 50 ? 'GRATUITE' : formatPrice(5.99)}
                                </span>
                            </div>

                            {calculateTotal() < 50 && (
                                <small className="text-muted mb-3 d-block">
                                    <i className="fas fa-info-circle me-1"></i>
                                    Livraison gratuite dès {formatPrice(50)}
                                    <div className="progress mt-1" style={{ height: '4px' }}>
                                        <div 
                                            className="progress-bar bg-success" 
                                            style={{ width: `${(calculateTotal() / 50) * 100}%` }}
                                        ></div>
                                    </div>
                                </small>
                            )}

                            <hr />

                            {/* Total */}
                            <div className="d-flex justify-content-between fw-bold fs-5 mb-4">
                                <span>Total</span>
                                <span className="text-primary">
                                    {formatPrice(calculateTotal() + (calculateTotal() >= 50 ? 0 : 5.99))}
                                </span>
                            </div>

                            {/* Boutons d'action */}
                            <div className="d-grid gap-2">
                                <a href="/commande" className="btn btn-primary btn-lg">
                                    <i className="fas fa-credit-card me-2"></i>
                                    Passer commande
                                </a>
                                <a href="/produits" className="btn btn-outline-primary">
                                    <i className="fas fa-arrow-left me-2"></i>
                                    Continuer mes achats
                                </a>
                            </div>

                            {/* Sécurité */}
                            <div className="text-center mt-4 pt-3 border-top">
                                <small className="text-muted">
                                    <i className="fas fa-lock me-1"></i>
                                    Paiement 100% sécurisé SSL
                                </small>
                                <div className="mt-2">
                                    <i className="fab fa-cc-visa text-muted me-2"></i>
                                    <i className="fab fa-cc-mastercard text-muted me-2"></i>
                                    <i className="fab fa-cc-paypal text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Articles recommandés */}
                    <div className="card shadow mt-4">
                        <div className="card-header">
                            <h6 className="mb-0">
                                <i className="fas fa-thumbs-up me-2"></i>
                                Vous pourriez aussi aimer
                            </h6>
                        </div>
                        <div className="card-body">
                            <p className="text-muted small">
                                Fonctionnalité de recommandations à venir...
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default ShoppingCart;