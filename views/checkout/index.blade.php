<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande - E-commerce Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #1e293b, #334155);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-gem me-2 text-warning"></i>
                E-commerce Premium
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Accueil</a>
                <a class="nav-link" href="/produits">Produits</a>
                <a class="nav-link" href="/panier">Panier</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="text-center">
            <i class="fas fa-credit-card text-primary mb-4" style="font-size: 4rem;"></i>
            <h1 class="display-6 fw-bold mb-3">Processus de commande</h1>
            <p class="lead text-muted mb-4">Cette fonctionnalité sera bientôt disponible !</p>
            
            <div class="card mx-auto" style="max-width: 600px;">
                <div class="card-body p-5">
                    <h5 class="card-title mb-4">Fonctionnalités à venir :</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user text-primary me-3"></i>
                                <span>Informations client</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-truck text-success me-3"></i>
                                <span>Adresse de livraison</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-credit-card text-warning me-3"></i>
                                <span>Paiement sécurisé</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check text-info me-3"></i>
                                <span>Confirmation</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="/panier" class="btn btn-primary btn-lg me-3">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour au panier
                </a>
                <a href="/produits" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>
                    Continuer mes achats
                </a>
            </div>
        </div>
    </div>
</body>
</html>