<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce - Configuration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="text-center">
            <i class="fas fa-cog fa-3x text-warning mb-4"></i>
            <h1 class="mb-4">E-commerce en configuration</h1>
            <p class="lead mb-4">L'application fonctionne mais il n'y a pas encore de données.</p>
            
            <div class="card mx-auto" style="max-width: 600px;">
                <div class="card-body">
                    <h5 class="card-title">État de la base de données :</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Catégories:</span>
                            <span class="badge bg-secondary">{{ \App\Models\Category::count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Produits:</span>
                            <span class="badge bg-secondary">{{ \App\Models\Product::count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Utilisateurs:</span>
                            <span class="badge bg-secondary">{{ \App\Models\User::count() }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-4">
                <h6>Pour ajouter des données de test :</h6>
                <code class="bg-light p-2 rounded d-block my-3">php artisan db:seed</code>
                
                <h6 class="mt-4">Ou créez manuellement :</h6>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-primary" onclick="createTestData()">
                        <i class="fas fa-plus me-2"></i>Créer des données de test
                    </button>
                    <a href="/test" class="btn btn-outline-secondary">
                        <i class="fas fa-check me-2"></i>Test API
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createTestData() {
            alert('Exécutez: php artisan db:seed dans votre terminal');
        }
    </script>
</body>
</html>