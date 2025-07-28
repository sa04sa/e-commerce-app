@extends('layouts.app')

@section('title', 'Accueil - E-commerce Laravel + React')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="hero-section bg-primary text-white rounded mb-5 p-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold">Bienvenue sur notre E-commerce</h1>
                <p class="lead mb-4">
                    Découvrez notre sélection de produits de qualité avec React + Laravel
                </p>
                <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">
                    Voir tous les produits
                </a>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-shopping-cart" style="font-size: 6rem; opacity: 0.7;"></i>
            </div>
        </div>
    </div>

    <!-- Catégories -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">Nos Catégories</h2>
            <div id="categories-section"></div>
        </div>
    </div>

    <!-- Produits Vedettes -->
    @if($featuredProducts->count() > 0)
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">🌟 Produits Vedettes</h2>
            <div id="featured-products"></div>
        </div>
    </div>
    @endif

    <!-- Derniers Produits -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">✨ Derniers Produits</h2>
            <div id="latest-products"></div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="row text-center mb-5">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-box text-primary mb-3" style="font-size: 2rem;"></i>
                    <h4>{{ $latestProducts->count() }}+</h4>
                    <p class="text-muted">Produits Disponibles</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-tags text-success mb-3" style="font-size: 2rem;"></i>
                    <h4>{{ $categories->count() }}</h4>
                    <p class="text-muted">Catégories</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <i class="fas fa-shipping-fast text-warning mb-3" style="font-size: 2rem;"></i>
                    <h4>24h</h4>
                    <p class="text-muted">Livraison Rapide</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Passer les données à React -->
<script>
    window.ecommerceData = {
        featuredProducts: @json($featuredProducts),
        latestProducts: @json($latestProducts), 
        categories: @json($categories),
        baseUrl: '{{ config("app.url") }}',
        assetUrl: '{{ asset("") }}'
    };
</script>
@endsection