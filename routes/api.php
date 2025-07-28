<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\WishlistApiController;
use App\Http\Controllers\Api\SearchApiController;

/*
|--------------------------------------------------------------------------
| API Routes pour React Components
|--------------------------------------------------------------------------
*/

// Routes publiques (sans authentification)
Route::prefix('v1')->group(function () {
    
    // Products API
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/{product:slug}', [ProductApiController::class, 'show']);
    Route::get('/products/category/{category}', [ProductApiController::class, 'byCategory']);
    Route::get('/products/featured', [ProductApiController::class, 'featured']);
    
    // Categories API
    Route::get('/categories', [ProductApiController::class, 'categories']);
    
    // Search API
    Route::get('/search', [SearchApiController::class, 'search']);
    Route::get('/search/suggestions', [SearchApiController::class, 'suggestions']);
    Route::get('/search/filters', [SearchApiController::class, 'filters']);
    
    // Cart API (accessible sans authentification via session)
    Route::get('/cart', [CartApiController::class, 'index']);
    Route::post('/cart/add', [CartApiController::class, 'add']);
    Route::patch('/cart/{item}', [CartApiController::class, 'update']);
    Route::delete('/cart/{item}', [CartApiController::class, 'remove']);
    Route::get('/cart/count', [CartApiController::class, 'count']);
    Route::delete('/cart/clear', [CartApiController::class, 'clear']);
    
});

// Routes protégées (nécessitent une authentification)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    
    // Wishlist API
    Route::get('/wishlist', [WishlistApiController::class, 'index']);
    Route::post('/wishlist/add/{product}', [WishlistApiController::class, 'add']);
    Route::delete('/wishlist/remove/{product}', [WishlistApiController::class, 'remove']);
    Route::get('/wishlist/count', [WishlistApiController::class, 'count']);
    Route::post('/wishlist/toggle/{product}', [WishlistApiController::class, 'toggle']);
    
    // User Profile API
    Route::get('/user/profile', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
            'cart_count' => \App\Models\Cart::where('user_id', $request->user()->id)->sum('quantity'),
            'wishlist_count' => \App\Models\Wishlist::where('user_id', $request->user()->id)->count()
        ]);
    });
    
    // Orders API
    Route::get('/orders', [OrderApiController::class, 'index']);
    Route::get('/orders/{order}', [OrderApiController::class, 'show']);
    Route::post('/orders', [OrderApiController::class, 'store']);
    
});

// Routes pour les utilisateurs invités et authentifiés
Route::prefix('v1')->group(function () {
    
    // Newsletter
    Route::post('/newsletter/subscribe', function (Request $request) {
        $request->validate([
            'email' => 'required|email'
        ]);
        
        // Logique d'inscription newsletter ici
        // Exemple : Newsletter::firstOrCreate(['email' => $request->email]);
        
        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie à la newsletter !'
        ]);
    });
    
    // Contact
    Route::post('/contact', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string'
        ]);
        
        // Logique de contact ici
        // Exemple : Mail::to('admin@site.com')->send(new ContactMail($request->all()));
        
        return response()->json([
            'success' => true,
            'message' => 'Message envoyé avec succès !'
        ]);
    });
    
    // Coupons
    Route::post('/coupons/validate', function (Request $request) {
        $request->validate([
            'code' => 'required|string'
        ]);
        
        // Logique de validation coupon ici
        $coupon = \App\Models\Coupon::where('code', $request->code)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();
            
        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Code promo invalide ou expiré'
            ], 400);
        }
        
        return response()->json([
            'success' => true,
            'coupon' => [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'minimum_amount' => $coupon->minimum_amount
            ]
        ]);
    });
    
});

// Routes de statistiques publiques
Route::prefix('v1/stats')->group(function () {
    
    Route::get('/popular-products', function () {
        $products = \App\Models\Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(10)
            ->get();
            
        return response()->json($products);
    });
    
    Route::get('/categories-stats', function () {
        $categories = \App\Models\Category::withCount('products')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'products_count' => $category->products_count
                ];
            });
            
        return response()->json($categories);
    });
    
});

// Middleware pour les API publiques avec limitation de taux
Route::middleware(['throttle:api'])->group(function () {
    
    // Routes avec limitation plus stricte
    Route::post('/feedback', function (Request $request) {
        $request->validate([
            'type' => 'required|in:bug,suggestion,other',
            'message' => 'required|string|max:1000',
            'email' => 'nullable|email'
        ]);
        
        // Logique de feedback ici
        
        return response()->json([
            'success' => true,
            'message' => 'Feedback reçu, merci !'
        ]);
    });
    
});

// Routes de test (à supprimer en production)
if (app()->environment('local', 'testing')) {
    Route::prefix('v1/test')->group(function () {
        
        Route::get('/seed-data', function () {
            return response()->json([
                'categories' => \App\Models\Category::count(),
                'products' => \App\Models\Product::count(),
                'users' => \App\Models\User::count()
            ]);
        });
        
        Route::post('/clear-cart', function (Request $request) {
            if ($request->user()) {
                \App\Models\Cart::where('user_id', $request->user()->id)->delete();
            } else {
                \App\Models\Cart::where('session_id', session()->getId())->delete();
            }
            
            return response()->json(['success' => true]);
        });
        
    });
}

// Fallback pour les routes API non trouvées
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint API non trouvé',
        'available_endpoints' => [
            'GET /api/v1/products' => 'Liste des produits',
            'GET /api/v1/categories' => 'Liste des catégories',
            'GET /api/v1/cart' => 'Contenu du panier',
            'POST /api/v1/cart/add' => 'Ajouter au panier',
            'GET /api/v1/search' => 'Rechercher des produits'
        ]
    ], 404);
});