import React from 'react';
import ReactDOM from 'react-dom';
import ProductGrid from './components/ProductGrid';
import CategoryGrid from './components/CategoryGrid';

// Montage des composants React
document.addEventListener('DOMContentLoaded', function() {
    
    // Vérifier que les données sont disponibles
    if (typeof window.ecommerceData === 'undefined') {
        console.warn('Données e-commerce non trouvées');
        return;
    }

    const data = window.ecommerceData;

    // Monter les catégories
    const categoriesElement = document.getElementById('categories-section');
    if (categoriesElement && data.categories) {
        ReactDOM.render(
            <CategoryGrid categories={data.categories} />,
            categoriesElement
        );
    }

    // Monter les produits vedettes
    const featuredElement = document.getElementById('featured-products');
    if (featuredElement && data.featuredProducts) {
        ReactDOM.render(
            <ProductGrid 
                products={data.featuredProducts} 
                emptyMessage="Aucun produit vedette pour le moment."
            />,
            featuredElement
        );
    }

    // Monter les derniers produits
    const latestElement = document.getElementById('latest-products');
    if (latestElement && data.latestProducts) {
        ReactDOM.render(
            <ProductGrid 
                products={data.latestProducts}
                emptyMessage="Aucun produit disponible."
            />,
            latestElement
        );
    }

    console.log('✅ Composants React e-commerce montés avec succès !');
});