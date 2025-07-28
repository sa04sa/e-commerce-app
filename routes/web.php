<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes des produits
Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
Route::get('/produit/{product}', [ProductController::class, 'show'])->name('products.show');

// Routes des catégories
Route::get('/categorie/{category}', [CategoryController::class, 'show'])->name('categories.show');

// Routes du panier
Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter', [CartController::class, 'add'])->name('cart.add');
Route::patch('/panier/modifier/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/panier/supprimer/{id}', [CartController::class, 'remove'])->name('cart.remove');

// Route de recherche
Route::get('/recherche', [HomeController::class, 'search'])->name('search');

// Routes d'authentification (Laravel par défaut)
Auth::routes();

// Routes de test (à supprimer en production)
Route::get('/test-db', function () {
    return [
        'status' => 'OK',
        'categories' => \App\Models\Category::count(),
        'products' => \App\Models\Product::count(),
        'latest_products' => \App\Models\Product::latest()->limit(3)->pluck('name'),
        'categories_list' => \App\Models\Category::pluck('name')
    ];
});