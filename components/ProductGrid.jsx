import React from 'react';
import ProductCard from './ProductCard';

function ProductGrid({ products, title, emptyMessage = "Aucun produit trouvé." }) {
    if (!products || products.length === 0) {
        return (
            <div className="text-center py-5">
                <i className="fas fa-box-open text-muted mb-3" style={{ fontSize: '3rem' }}></i>
                <h4 className="text-muted">{emptyMessage}</h4>
            </div>
        );
    }

    return (
        <div>
            {title && <h3 className="mb-4">{title}</h3>}
            <div className="row">
                {products.map(product => (
                    <ProductCard key={product.id} product={product} />
                ))}
            </div>
        </div>
    );
}

export default ProductGrid;