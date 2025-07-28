import React, { useState, useEffect } from 'react';

function Checkout() {
    const [currentStep, setCurrentStep] = useState(1);
    const [cartItems, setCartItems] = useState([]);
    const [customerInfo, setCustomerInfo] = useState({
        email: '',
        firstName: '',
        lastName: '',
        phone: ''
    });
    const [shippingAddress, setShippingAddress] = useState({
        address: '',
        city: '',
        postalCode: '',
        country: 'France'
    });
    const [billingAddress, setBillingAddress] = useState({
        address: '',
        city: '',
        postalCode: '',
        country: 'France'
    });
    const [sameAsBilling, setSameAsBilling] = useState(true);
    const [shippingMethod, setShippingMethod] = useState('standard');
    const [paymentMethod, setPaymentMethod] = useState('card');
    const [cardInfo, setCardInfo] = useState({
        number: '',
        expiry: '',
        cvv: '',
        name: ''
    });
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errors, setErrors] = useState({});

    const steps = [
        { id: 1, title: 'Informations', icon: 'fas fa-user' },
        { id: 2, title: 'Livraison', icon: 'fas fa-truck' },
        { id: 3, title: 'Paiement', icon: 'fas fa-credit-card' },
        { id: 4, title: 'Confirmation', icon: 'fas fa-check' }
    ];

    const shippingMethods = [
        { id: 'standard', name: 'Livraison Standard', price: 5.99, duration: '3-5 jours' },
        { id: 'express', name: 'Livraison Express', price: 9.99, duration: '1-2 jours' },
        { id: 'premium', name: 'Livraison Premium', price: 14.99, duration: '24h' }
    ];

    useEffect(() => {
        fetchCartItems();
    }, []);

    const fetchCartItems = async () => {
        try {
            const response = await fetch('/api/cart');
            const data = await response.json();
            setCartItems(data.items || []);
        } catch (error) {
            console.error('Erreur lors du chargement du panier:', error);
        }
    };

    const validateStep = (step) => {
        const newErrors = {};

        if (step === 1) {
            if (!customerInfo.email) newErrors.email = 'Email requis';
            if (!customerInfo.firstName) newErrors.firstName = 'Prénom requis';
            if (!customerInfo.lastName) newErrors.lastName = 'Nom requis';
            if (!customerInfo.phone) newErrors.phone = 'Téléphone requis';
        }

        if (step === 2) {
            if (!shippingAddress.address) newErrors.shippingAddress = 'Adresse requise';
            if (!shippingAddress.city) newErrors.shippingCity = 'Ville requise';
            if (!shippingAddress.postalCode) newErrors.shippingPostalCode = 'Code postal requis';
        }

        if (step === 3) {
            if (paymentMethod === 'card') {
                if (!cardInfo.number) newErrors.cardNumber = 'Numéro de carte requis';
                if (!cardInfo.expiry) newErrors.cardExpiry = 'Date d\'expiration requise';
                if (!cardInfo.cvv) newErrors.cardCvv = 'CVV requis';
                if (!cardInfo.name) newErrors.cardName = 'Nom sur la carte requis';
            }
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const nextStep = () => {
        if (validateStep(currentStep)) {
            setCurrentStep(prev => Math.min(prev + 1, 4));
        }
    };

    const prevStep = () => {
        setCurrentStep(prev => Math.max(prev - 1, 1));
    };

    const handleSubmitOrder = async () => {
        if (!validateStep(3)) return;

        setIsSubmitting(true);

        try {
            const orderData = {
                customer: customerInfo,
                shipping_address: shippingAddress,
                billing_address: sameAsBilling ? shippingAddress : billingAddress,
                shipping_method: shippingMethod,
                payment_method: paymentMethod,
                payment_details: paymentMethod === 'card' ? cardInfo : null
            };

            const response = await fetch('/commande/traiter', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(orderData)
            });

            const result = await response.json();

            if (result.success) {
                setCurrentStep(4);
                // Vider le panier
                setCartItems([]);
                // Redirection vers la page de confirmation
                setTimeout(() => {
                    window.location.href = `/commande/confirmation/${result.order_id}`;
                }, 3000);
            } else {
                setErrors({ submit: result.message || 'Erreur lors de la commande' });
            }
        } catch (error) {
            console.error('Erreur:', error);
            setErrors({ submit: 'Erreur de connexion' });
        } finally {
            setIsSubmitting(false);
        }
    };

    const formatPrice = (price) => {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(price);
    };

    const calculateSubtotal = () => {
        return cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    };

    const getShippingPrice = () => {
        const method = shippingMethods.find(m => m.id === shippingMethod);
        return method ? method.price : 0;
    };

    const calculateTotal = () => {
        return calculateSubtotal() + getShippingPrice();
    };

    if (cartItems.length === 0) {
        return (
            <div className="container py-5">
                <div className="text-center">
                    <i className="fas fa-shopping-cart text-muted mb-4" style={{ fontSize: '4rem' }}></i>
                    <h3>Votre panier est vide</h3>
                    <p className="text-muted">Ajoutez des produits pour passer commande.</p>
                    <a href="/produits" className="btn btn-primary">
                        Continuer mes achats
                    </a>
                </div>
            </div>
        );
    }

    return (
        <div className="container py-5">
            {/* Header avec étapes */}
            <div className="row mb-5">
                <div className="col-12">
                    <h1 className="text-center mb-4">Finaliser ma commande</h1>
                    
                    {/* Progress bar */}
                    <div className="checkout-progress">
                        <div className="d-flex justify-content-between align-items-center">
                            {steps.map((step, index) => (
                                <div key={step.id} className="d-flex flex-column align-items-center">
                                    <div className={`step-circle ${currentStep >= step.id ? 'active' : ''} ${currentStep > step.id ? 'completed' : ''}`}>
                                        <i className={step.icon}></i>
                                    </div>
                                    <span className="step-title mt-2">{step.title}</span>
                                    {index < steps.length - 1 && (
                                        <div className={`step-line ${currentStep > step.id ? 'completed' : ''}`}></div>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>

            <div className="row">
                {/* Formulaire de commande */}
                <div className="col-lg-8">
                    <div className="card shadow">
                        <div className="card-body">
                            {/* Étape 1: Informations client */}
                            {currentStep === 1 && (
                                <div className="step-content">
                                    <h4 className="mb-4">
                                        <i className="fas fa-user me-2"></i>
                                        Vos informations
                                    </h4>
                                    
                                    <div className="row">
                                        <div className="col-md-6 mb-3">
                                            <label htmlFor="firstName" className="form-label">Prénom *</label>
                                            <input
                                                type="text"
                                                className={`form-control ${errors.firstName ? 'is-invalid' : ''}`}
                                                id="firstName"
                                                value={customerInfo.firstName}
                                                onChange={(e) => setCustomerInfo({...customerInfo, firstName: e.target.value})}
                                            />
                                            {errors.firstName && <div className="invalid-feedback">{errors.firstName}</div>}
                                        </div>
                                        <div className="col-md-6 mb-3">
                                            <label htmlFor="lastName" className="form-label">Nom *</label>
                                            <input
                                                type="text"
                                                className={`form-control ${errors.lastName ? 'is-invalid' : ''}`}
                                                id="lastName"
                                                value={customerInfo.lastName}
                                                onChange={(e) => setCustomerInfo({...customerInfo, lastName: e.target.value})}
                                            />
                                            {errors.lastName && <div className="invalid-feedback">{errors.lastName}</div>}
                                        </div>
                                    </div>
                                    
                                    <div className="row">
                                        <div className="col-md-6 mb-3">
                                            <label htmlFor="email" className="form-label">Email *</label>
                                            <input
                                                type="email"
                                                className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                                                id="email"
                                                value={customerInfo.email}
                                                onChange={(e) => setCustomerInfo({...customerInfo, email: e.target.value})}
                                            />
                                            {errors.email && <div className="invalid-feedback">{errors.email}</div>}
                                        </div>
                                        <div className="col-md-6 mb-3">
                                            <label htmlFor="phone" className="form-label">Téléphone *</label>
                                            <input
                                                type="tel"
                                                className={`form-control ${errors.phone ? 'is-invalid' : ''}`}
                                                id="phone"
                                                value={customerInfo.phone}
                                                onChange={(e) => setCustomerInfo({...customerInfo, phone: e.target.value})}
                                            />
                                            {errors.phone && <div className="invalid-feedback">{errors.phone}</div>}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Étape 2: Adresse de livraison */}
                            {currentStep === 2 && (
                                <div className="step-content">
                                    <h4 className="mb-4">
                                        <i className="fas fa-truck me-2"></i>
                                        Adresse de livraison
                                    </h4>
                                    
                                    <div className="mb-3">
                                        <label htmlFor="shippingAddress" className="form-label">Adresse *</label>
                                        <input
                                            type="text"
                                            className={`form-control ${errors.shippingAddress ? 'is-invalid' : ''}`}
                                            id="shippingAddress"
                                            value={shippingAddress.address}
                                            onChange={(e) => setShippingAddress({...shippingAddress, address: e.target.value})}
                                        />
                                        {errors.shippingAddress && <div className="invalid-feedback">{errors.shippingAddress}</div>}
                                    </div>
                                    
                                    <div className="row">
                                        <div className="col-md-6 mb-3">
                                            <label htmlFor="shippingCity" className="form-label">Ville *</label>
                                            <input
                                                type="text"
                                                className={`form-control ${errors.shippingCity ? 'is-invalid' : ''}`}
                                                id="shippingCity"
                                                value={shippingAddress.city}
                                                onChange={(e) => setShippingAddress({...shippingAddress, city: e.target.value})}
                                            />
                                            {errors.shippingCity && <div className="invalid-feedback">{errors.shippingCity}</div>}
                                        </div>
                                        <div className="col-md-6 mb-3">
                                            <label htmlFor="shippingPostalCode" className="form-label">Code postal *</label>
                                            <input
                                                type="text"
                                                className={`form-control ${errors.shippingPostalCode ? 'is-invalid' : ''}`}
                                                id="shippingPostalCode"
                                                value={shippingAddress.postalCode}
                                                onChange={(e) => setShippingAddress({...shippingAddress, postalCode: e.target.value})}
                                            />
                                            {errors.shippingPostalCode && <div className="invalid-feedback">{errors.shippingPostalCode}</div>}
                                        </div>
                                    </div>

                                    {/* Méthodes de livraison */}
                                    <h5 className="mt-4 mb-3">Méthode de livraison</h5>
                                    {shippingMethods.map(method => (
                                        <div key={method.id} className="form-check mb-3">
                                            <input
                                                className="form-check-input"
                                                type="radio"
                                                name="shippingMethod"
                                                id={method.id}
                                                value={method.id}
                                                checked={shippingMethod === method.id}
                                                onChange={(e) => setShippingMethod(e.target.value)}
                                            />
                                            <label className="form-check-label d-flex justify-content-between w-100" htmlFor={method.id}>
                                                <div>
                                                    <strong>{method.name}</strong>
                                                    <div className="text-muted small">{method.duration}</div>
                                                </div>
                                                <div className="fw-bold">
                                                    {formatPrice(method.price)}
                                                </div>
                                            </label>
                                        </div>
                                    ))}
                                </div>
                            )}

                            {/* Étape 3: Paiement */}
                            {currentStep === 3 && (
                                <div className="step-content">
                                    <h4 className="mb-4">
                                        <i className="fas fa-credit-card me-2"></i>
                                        Paiement
                                    </h4>

                                    {/* Méthodes de paiement */}
                                    <div className="mb-4">
                                        <div className="form-check mb-3">
                                            <input
                                                className="form-check-input"
                                                type="radio"
                                                name="paymentMethod"
                                                id="card"
                                                value="card"
                                                checked={paymentMethod === 'card'}
                                                onChange={(e) => setPaymentMethod(e.target.value)}
                                            />
                                            <label className="form-check-label" htmlFor="card">
                                                <i className="fas fa-credit-card me-2"></i>
                                                Carte bancaire
                                            </label>
                                        </div>
                                        <div className="form-check mb-3">
                                            <input
                                                className="form-check-input"
                                                type="radio"
                                                name="paymentMethod"
                                                id="paypal"
                                                value="paypal"
                                                checked={paymentMethod === 'paypal'}
                                                onChange={(e) => setPaymentMethod(e.target.value)}
                                            />
                                            <label className="form-check-label" htmlFor="paypal">
                                                <i className="fab fa-paypal me-2"></i>
                                                PayPal
                                            </label>
                                        </div>
                                    </div>

                                    {/* Informations carte */}
                                    {paymentMethod === 'card' && (
                                        <div className="card bg-light p-4">
                                            <div className="row">
                                                <div className="col-12 mb-3">
                                                    <label htmlFor="cardNumber" className="form-label">Numéro de carte *</label>
                                                    <input
                                                        type="text"
                                                        className={`form-control ${errors.cardNumber ? 'is-invalid' : ''}`}
                                                        id="cardNumber"
                                                        placeholder="1234 5678 9012 3456"
                                                        value={cardInfo.number}
                                                        onChange={(e) => setCardInfo({...cardInfo, number: e.target.value})}
                                                    />
                                                    {errors.cardNumber && <div className="invalid-feedback">{errors.cardNumber}</div>}
                                                </div>
                                            </div>
                                            <div className="row">
                                                <div className="col-md-4 mb-3">
                                                    <label htmlFor="cardExpiry" className="form-label">Expiration *</label>
                                                    <input
                                                        type="text"
                                                        className={`form-control ${errors.cardExpiry ? 'is-invalid' : ''}`}
                                                        id="cardExpiry"
                                                        placeholder="MM/YY"
                                                        value={cardInfo.expiry}
                                                        onChange={(e) => setCardInfo({...cardInfo, expiry: e.target.value})}
                                                    />
                                                    {errors.cardExpiry && <div className="invalid-feedback">{errors.cardExpiry}</div>}
                                                </div>
                                                <div className="col-md-4 mb-3">
                                                    <label htmlFor="cardCvv" className="form-label">CVV *</label>
                                                    <input
                                                        type="text"
                                                        className={`form-control ${errors.cardCvv ? 'is-invalid' : ''}`}
                                                        id="cardCvv"
                                                        placeholder="123"
                                                        value={cardInfo.cvv}
                                                        onChange={(e) => setCardInfo({...cardInfo, cvv: e.target.value})}
                                                    />
                                                    {errors.cardCvv && <div className="invalid-feedback">{errors.cardCvv}</div>}
                                                </div>
                                                <div className="col-md-4 mb-3">
                                                    <label htmlFor="cardName" className="form-label">Nom sur la carte *</label>
                                                    <input
                                                        type="text"
                                                        className={`form-control ${errors.cardName ? 'is-invalid' : ''}`}
                                                        id="cardName"
                                                        value={cardInfo.name}
                                                        onChange={(e) => setCardInfo({...cardInfo, name: e.target.value})}
                                                    />
                                                    {errors.cardName && <div className="invalid-feedback">{errors.cardName}</div>}
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {errors.submit && (
                                        <div className="alert alert-danger mt-3">
                                            {errors.submit}
                                        </div>
                                    )}
                                </div>
                            )}

                            {/* Étape 4: Confirmation */}
                            {currentStep === 4 && (
                                <div className="step-content text-center">
                                    <div className="success-animation mb-4">
                                        <i className="fas fa-check-circle text-success" style={{ fontSize: '4rem' }}></i>
                                    </div>
                                    <h4 className="text-success mb-3">Commande confirmée !</h4>
                                    <p className="text-muted mb-4">
                                        Merci pour votre commande. Vous recevrez un email de confirmation sous peu.
                                    </p>
                                    <div className="d-flex justify-content-center gap-3">
                                        <a href="/commandes" className="btn btn-primary">
                                            Voir mes commandes
                                        </a>
                                        <a href="/produits" className="btn btn-outline-primary">
                                            Continuer mes achats
                                        </a>
                                    </div>
                                </div>
                            )}

                            {/* Boutons de navigation */}
                            {currentStep < 4 && (
                                <div className="d-flex justify-content-between mt-4 pt-4 border-top">
                                    <button 
                                        className="btn btn-outline-secondary"
                                        onClick={prevStep}
                                        disabled={currentStep === 1}
                                    >
                                        <i className="fas fa-arrow-left me-2"></i>
                                        Précédent
                                    </button>
                                    
                                    {currentStep < 3 ? (
                                        <button 
                                            className="btn btn-primary"
                                            onClick={nextStep}
                                        >
                                            Suivant
                                            <i className="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    ) : (
                                        <button 
                                            className="btn btn-success"
                                            onClick={handleSubmitOrder}
                                            disabled={isSubmitting}
                                        >
                                            {isSubmitting ? (
                                                <>
                                                    <span className="spinner-border spinner-border-sm me-2"></span>
                                                    Traitement...
                                                </>
                                            ) : (
                                                <>
                                                    <i className="fas fa-credit-card me-2"></i>
                                                    Payer {formatPrice(calculateTotal())}
                                                </>
                                            )}
                                        </button>
                                    )}
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
                            {/* Articles */}
                            <div className="mb-3">
                                <h6 className="border-bottom pb-2">Articles ({cartItems.length})</h6>
                                {cartItems.slice(0, 3).map(item => (
                                    <div key={item.id} className="d-flex align-items-center mb-2">
                                        <img 
                                            src={`/storage/products/${item.product?.images?.[0] || 'no-product.png'}`}
                                            alt={item.product?.name}
                                            className="rounded me-2"
                                            style={{ width: '40px', height: '40px', objectFit: 'cover' }}
                                        />
                                        <div className="flex-grow-1">
                                            <div className="fw-bold small">{item.product?.name}</div>
                                            <div className="text-muted small">Qté: {item.quantity}</div>
                                        </div>
                                        <div className="fw-bold">
                                            {formatPrice(item.price * item.quantity)}
                                        </div>
                                    </div>
                                ))}
                                {cartItems.length > 3 && (
                                    <div className="text-muted small">
                                        ... et {cartItems.length - 3} autre{cartItems.length - 3 > 1 ? 's' : ''} article{cartItems.length - 3 > 1 ? 's' : ''}
                                    </div>
                                )}
                            </div>

                            {/* Totaux */}
                            <div className="border-top pt-3">
                                <div className="d-flex justify-content-between mb-2">
                                    <span>Sous-total</span>
                                    <span>{formatPrice(calculateSubtotal())}</span>
                                </div>
                                <div className="d-flex justify-content-between mb-2">
                                    <span>Livraison</span>
                                    <span>{formatPrice(getShippingPrice())}</span>
                                </div>
                                <div className="d-flex justify-content-between fw-bold fs-5 border-top pt-2">
                                    <span>Total</span>
                                    <span className="text-primary">{formatPrice(calculateTotal())}</span>
                                </div>
                            </div>

                            {/* Sécurité */}
                            <div className="text-center mt-4 pt-3 border-top">
                                <small className="text-muted">
                                    <i className="fas fa-lock me-1"></i>
                                    Paiement 100% sécurisé SSL
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Checkout;