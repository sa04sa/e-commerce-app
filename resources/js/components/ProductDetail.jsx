import React, { useState, useEffect } from 'react';

function ProductDetail({ productSlug }) {
    const [product, setProduct] = useState(null);
    const [selectedImage, setSelectedImage] = useState(0);
    const [quantity, setQuantity] = useState(1);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        fetchProduct();
    }, [productSlug]);

    const fetchProduct = async () => {
        try {
            setIsLoading(true);
            const response = await fetch(`/api/products/${productSlug}`);
            const data = await response.json();
            
            if (response.ok) {
                setProduct(data);
            } else {
                setError('Produit non trouvé');
            }
        } catch (err) {
            setError('Erreur lors du chargement');
        } finally {
            setIsLoading(false);
        }
    };

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
                    quantity: quantity
                })
            });

            const result = await response.json();
            
            if (result.success) {
                // Animation de succès
                showSuccessAnimation();
                updateCartCount(quantity);
            } else {
                alert('Erreur lors de l\'ajout au panier');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur de connexion');
        }
    };

    const showSuccessAnimation = () => {
        // Animation de succès avec notification toast
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.innerHTML = `
            <div class="alert alert-success alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999;">
                <i class="fas fa-check-circle me-2"></i>
                Produit ajouté au panier !
                <button type="button" class="btn-close" aria-label="Close"></button>
            </div>
        `;
        document.body.appendChild(toast);

        // Supprimer après 3 secondes
        setTimeout(() => {
            toast.remove();
        }, 3000);
    };

    const updateCartCount = (addedQuantity) => {
        const cartBadge = document.querySelector('.cart-count');
        if (cartBadge) {
            const currentCount = parseInt(cartBadge.textContent) || 0;
            cartBadge.textContent = currentCount + addedQuantity;
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
                    <p className="mt-3">Chargement du produit...</p>
                </div>
            </div>
        );
    }

    if (error || !product) {
        return (
            <div className="container py-5">
                <div className="text-center">
                    <i className="fas fa-exclamation-triangle text-warning mb-3" style={{ fontSize: '3rem' }}></i>
                    <h4>{error || 'Produit non trouvé'}</h4>
                    <a href="/produits" className="btn btn-primary mt-3">
                        <i className="fas fa-arrow-left me-2"></i>
                        Retour aux produits
                    </a>
                </div>
            </div>
        );
    }

    const images = product.images || ['no-product.png'];
    const isOnSale = product.sale_price && product.sale_price < product.price;
    const discountPercentage = isOnSale ? 
        Math.round(((product.price - product.sale_price) / product.price) * 100) : 0;

    return (
        <div className="container py-5">
            {/* Breadcrumb */}
            <nav aria-label="breadcrumb" className="mb-4">
                <ol className="breadcrumb">
                    <li className="breadcrumb-item">
                        <a href="/">Accueil</a>
                    </li>
                    <li className="breadcrumb-item">
                        <a href="/produits">Produits</a>
                    </li>
                    <li className="breadcrumb-item">
                        <a href={`/categorie/${product.category?.slug}`}>
                            {product.category?.name}
                        </a>
                    </li>
                    <li className="breadcrumb-item active" aria-current="page">
                        {product.name}
                    </li>
                </ol>
            </nav>

            <div className="row">
                {/* Images du produit */}
                <div className="col-md-6">
                    <div className="product-images">
                        {/* Image principale */}
                        <div className="main-image mb-3">
                            <div className="position-relative">
                                <img 
                                    src={getImageUrl(images[selectedImage])}
                                    alt={product.name}
                                    className="img-fluid rounded shadow"
                                    style={{ width: '100%', height: '400px', objectFit: 'cover' }}
                                />
                                
                                {/* Badges */}
                                <div className="position-absolute top-0 start-0 p-3">
                                    {isOnSale && (
                                        <span className="badge bg-danger fs-6 me-2">
                                            -{discountPercentage}%
                                        </span>
                                    )}
                                    {product.featured && (
                                        <span className="badge bg-warning fs-6">
                                            ⭐ Vedette
                                        </span>
                                    )}
                                </div>

                                {/* Zoom au survol */}
                                <div className="position-absolute top-0 end-0 p-3">
                                    <button className="btn btn-light btn-sm rounded-circle">
                                        <i className="fas fa-search-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {/* Miniatures */}
                        {images.length > 1 && (
                            <div className="row g-2">
                                {images.map((image, index) => (
                                    <div key={index} className="col-3">
                                        <img 
                                            src={getImageUrl(image)}
                                            alt={`${product.name} ${index + 1}`}
                                            className={`img-fluid rounded cursor-pointer border ${
                                                selectedImage === index ? 'border-primary border-3' : 'border-light'
                                            }`}
                                            style={{ height: '80px', objectFit: 'cover' }}
                                            onClick={() => setSelectedImage(index)}
                                        />
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>

                {/* Détails du produit */}
                <div className="col-md-6">
                    <div className="product-details">
                        {/* Nom et catégorie */}
                        <div className="mb-3">
                            <span className="badge bg-secondary mb-2">
                                {product.category?.name}
                            </span>
                            <h1 className="display-5 fw-bold">{product.name}</h1>
                            <p className="text-muted">SKU: {product.sku}</p>
                        </div>

                        {/* Prix */}
                        <div className="mb-4">
                            <div className="d-flex align-items-baseline">
                                {isOnSale ? (
                                    <>
                                        <span className="h2 text-danger fw-bold me-3">
                                            {formatPrice(product.sale_price)}
                                        </span>
                                        <span className="h5 text-muted text-decoration-line-through">
                                            {formatPrice(product.price)}
                                        </span>
                                        <span className="badge bg-success ms-2">
                                            Économisez {formatPrice(product.price - product.sale_price)}
                                        </span>
                                    </>
                                ) : (
                                    <span className="h2 text-primary fw-bold">
                                        {formatPrice(product.price)}
                                    </span>
                                )}
                            </div>
                        </div>

                        {/* Description courte */}
                        {product.short_description && (
                            <div className="mb-4">
                                <p className="lead">{product.short_description}</p>
                            </div>
                        )}

                        {/* Stock */}
                        <div className="mb-4">
                            {product.stock > 0 ? (
                                <div className="d-flex align-items-center">
                                    <i className="fas fa-check-circle text-success me-2"></i>
                                    <span className="text-success fw-bold">
                                        En stock ({product.stock} disponible{product.stock > 1 ? 's' : ''})
                                    </span>
                                </div>
                            ) : (
                                <div className="d-flex align-items-center">
                                    <i className="fas fa-times-circle text-danger me-2"></i>
                                    <span className="text-danger fw-bold">Rupture de stock</span>
                                </div>
                            )}
                        </div>

                        {/* Sélection quantité et ajout panier */}
                        {product.stock > 0 && (
                            <div className="mb-4">
                                <div className="row g-3">
                                    <div className="col-4">
                                        <label htmlFor="quantity" className="form-label">
                                            Quantité
                                        </label>
                                        <input 
                                            type="number" 
                                            className="form-control"
                                            id="quantity"
                                            min="1" 
                                            max={product.stock}
                                            value={quantity}
                                            onChange={(e) => setQuantity(parseInt(e.target.value) || 1)}
                                        />
                                    </div>
                                    <div className="col-8 d-flex align-items-end">
                                        <button 
                                            className="btn btn-primary btn-lg w-100"
                                            onClick={addToCart}
                                        >
                                            <i className="fas fa-cart-plus me-2"></i>
                                            Ajouter au panier
                                        </button>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Actions supplémentaires */}
                        <div className="mb-4">
                            <div className="d-flex gap-2">
                                <button className="btn btn-outline-danger">
                                    <i className="fas fa-heart me-2"></i>
                                    Ajouter aux favoris
                                </button>
                                <button className="btn btn-outline-primary">
                                    <i className="fas fa-share-alt me-2"></i>
                                    Partager
                                </button>
                            </div>
                        </div>

                        {/* Informations de livraison */}
                        <div className="card bg-light">
                            <div className="card-body">
                                <h6 className="card-title">
                                    <i className="fas fa-truck me-2"></i>
                                    Livraison
                                </h6>
                                <ul className="list-unstyled mb-0">
                                    <li>
                                        <i className="fas fa-check text-success me-2"></i>
                                        Livraison gratuite dès 50€
                                    </li>
                                    <li>
                                        <i className="fas fa-check text-success me-2"></i>
                                        Livraison en 24-48h
                                    </li>
                                    <li>
                                        <i className="fas fa-check text-success me-2"></i>
                                        Retour gratuit sous 30 jours
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Onglets description et avis */}
            <div className="row mt-5">
                <div className="col-12">
                    <ul className="nav nav-tabs" id="productTabs" role="tablist">
                        <li className="nav-item" role="presentation">
                            <button className="nav-link active" id="description-tab" data-bs-toggle="tab" 
                                    data-bs-target="#description" type="button" role="tab">
                                Description
                            </button>
                        </li>
                        <li className="nav-item" role="presentation">
                            <button className="nav-link" id="reviews-tab" data-bs-toggle="tab" 
                                    data-bs-target="#reviews" type="button" role="tab">
                                Avis clients
                            </button>
                        </li>
                        <li className="nav-item" role="presentation">
                            <button className="nav-link" id="shipping-tab" data-bs-toggle="tab" 
                                    data-bs-target="#shipping" type="button" role="tab">
                                Livraison
                            </button>
                        </li>
                    </ul>
                    
                    <div className="tab-content mt-3" id="productTabsContent">
                        <div className="tab-pane fade show active" id="description" role="tabpanel">
                            <div className="p-4">
                                <div dangerouslySetInnerHTML={{ __html: product.description }} />
                            </div>
                        </div>
                        <div className="tab-pane fade" id="reviews" role="tabpanel">
                            <div className="p-4">
                                <p>Fonctionnalité des avis à venir...</p>
                            </div>
                        </div>
                        <div className="tab-pane fade" id="shipping" role="tabpanel">
                            <div className="p-4">
                                <h5>Informations de livraison</h5>
                                <p>Détails sur les options de livraison...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default ProductDetail;