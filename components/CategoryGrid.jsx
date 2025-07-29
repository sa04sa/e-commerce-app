import React from 'react';

function CategoryGrid({ categories }) {
    const getCategoryIcon = (slug) => {
        const icons = {
            'electronique': 'fas fa-laptop',
            'vetements': 'fas fa-tshirt',
            'maison-jardin': 'fas fa-home',
            'sports-loisirs': 'fas fa-dumbbell',
            'livres-medias': 'fas fa-book',
            'beaute-sante': 'fas fa-heart'
        };
        return icons[slug] || 'fas fa-tag';
    };

    const getCategoryColor = (slug) => {
        const colors = {
            'electronique': 'primary',
            'vetements': 'info',
            'maison-jardin': 'success',
            'sports-loisirs': 'warning',
            'livres-medias': 'danger',
            'beaute-sante': 'secondary'
        };
        return colors[slug] || 'primary';
    };

    if (!categories || categories.length === 0) {
        return (
            <div className="text-center py-3">
                <p className="text-muted">Aucune catégorie disponible.</p>
            </div>
        );
    }

    return (
        <div className="row">
            {categories.map(category => (
                <div key={category.id} className="col-md-6 col-lg-4 mb-3">
                    <a 
                        href={`/categorie/${category.slug}`} 
                        className="text-decoration-none"
                    >
                        <div className={`card border-${getCategoryColor(category.slug)} h-100 shadow-sm category-card`}>
                            <div className="card-body text-center">
                                <div className={`text-${getCategoryColor(category.slug)} mb-3`}>
                                    <i className={`${getCategoryIcon(category.slug)} fa-2x`}></i>
                                </div>
                                <h5 className="card-title">{category.name}</h5>
                                <p className="card-text text-muted small">
                                    {category.description}
                                </p>
                                <div className="mt-auto">
                                    <span className={`badge bg-${getCategoryColor(category.slug)}`}>
                                        {category.products_count || 0} produit{(category.products_count || 0) !== 1 ? 's' : ''}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            ))}
        </div>
    );
}

export default CategoryGrid;