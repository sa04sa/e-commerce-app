<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Web Routes - E-commerce complet
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes des produits
Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
Route::get('/produit/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Routes des catégories
Route::get('/categorie/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Routes du panier
Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter', [CartController::class, 'add'])->name('cart.add');
Route::patch('/panier/modifier/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/panier/supprimer/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/panier/vider', [CartController::class, 'clear'])->name('cart.clear');
    // Appliquer/retirer un code promo
    Route::post('/api/v1/coupons/validate', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::delete('/api/v1/coupons/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// Route de recherche
Route::get('/recherche', [HomeController::class, 'search'])->name('search');

// Routes temporaires pour les pages en construction
Route::get('/commande', function() {
    return view('checkout.index');
})->name('checkout.index');

Route::get('/favoris', function() {
    return view('wishlist.index');
})->name('wishlist.index');

Route::get('/compte', function() {
    return view('account.index');
})->name('account.index');

// Routes d'authentification Laravel par défaut
Auth::routes();

// Route de test (à supprimer en production)
Route::get('/hello', function () {
    return response()->json([
        'message' => 'Laravel fonctionne !',
        'timestamp' => now(),
        'environment' => app()->environment()
    ]);
});