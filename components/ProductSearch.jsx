import React, { useState, useEffect, useCallback } from 'react';
import ProductGrid from './ProductGrid';
import { showError, showSuccess } from './NotificationSystem';

function ProductSearch() {
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');
    const [selectedCategory, setSelectedCategory] = useState('');
    const [sortBy, setSortBy] = useState('name');
    const [sortOrder, setSortOrder] = useState('asc');
    const [priceRange, setPriceRange] = useState({ min: '', max: '' });
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [totalProducts, setTotalProducts] = useState(0);
    const [categories, setCategories] = useState([]);
    const [viewMode, setViewMode] = useState('grid');
    const [showFilters, setShowFilters] = useState(false);

    // Charger les catégories au montage
    useEffect(() => {
        fetchCategories();
    }, []);

    // Charger les produits à chaque changement de filtre
    useEffect(() => {
        fetchProducts();
    }, [searchQuery, selectedCategory, sortBy, sortOrder, priceRange, currentPage]);

    const fetchCategories = async () => {
        try {
            const response = await fetch('/api/v1/categories');
            const data = await response.json();
            setCategories(data);
        } catch (error) {
            console.error('Erreur lors du chargement des catégories:', error);
        }
    };

    const fetchProducts = useCallback(async () => {
        setLoading(true);
        try {
            const params = new URLSearchParams({
                page: currentPage,
                per_page: 12,
                sort_by: sortBy,
                sort_order: sortOrder
            });

            if (searchQuery) params.append('search', searchQuery);
            if (selectedCategory) params.append('category', selectedCategory);
            if (priceRange.min) params.append('price_min', priceRange.min);
            if (priceRange.max) params.append('price_max', priceRange.max);

            const response = await fetch(`/api/v1/products?${params}`);
            const data = await response.json();

            if (response.ok) {
                setProducts(data.data || data.products || []);
                setCurrentPage(data.current_page || 1);
                setTotalPages(data.last_page || 1);
                setTotalProducts(data.total || data.data?.length || 0);
            } else {
                showError('Erreur lors du chargement des produits');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showError('Erreur de connexion');
        } finally {
            setLoading(false);
        }
    }, [searchQuery, selectedCategory, sortBy, sortOrder, priceRange, currentPage]);

    const handleSearch = (e) => {
        e.preventDefault();
        setCurrentPage(1);
        fetchProducts();
    };

    const handleCategoryChange = (categoryId) => {
        setSelectedCategory(categoryId);
        setCurrentPage(1);
    };

    const handleSortChange = (newSortBy) => {
        if (sortBy === newSortBy) {
            setSortOrder(sortOrder === 'asc' ? 'desc' : 'asc');
        } else {
            setSortBy(newSortBy);
            setSortOrder('asc');
        }
        setCurrentPage(1);
    };

    const handlePriceRangeChange = (field, value) => {
        setPriceRange(prev => ({
            ...prev,
            [field]: value
        }));
        setCurrentPage(1);
    };

    const clearFilters = () => {
        setSearchQuery('');
        setSelectedCategory('');
        setSortBy('name');
        setSortOrder('asc');
        setPriceRange({ min: '', max: '' });
        setCurrentPage(1);
    };

    const getSortIcon = (field) => {
        if (sortBy !== field) return 'fas fa-sort';
        return sortOrder === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
    };

    return (
        <div className="container py-4">
            {/* Header avec barre de recherche */}
            <div className="row mb-4">
                <div className="col-12">
                    <h1 className="mb-4">
                        <i className="fas fa-search me-2"></i>
                        Rechercher des produits
                    </h1>
                    
                    <form onSubmit={handleSearch} className="mb-4">
                        <div className="input-group input-group-lg">
                            <input
                                type="text"
                                className="form-control"
                                placeholder="Rechercher un produit..."
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                            />
                            <button className="btn btn-primary" type="submit">
                                <i className="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {/* Filtres et tri */}
            <div className="row mb-4">
                <div className="col-12">
                    <div className="d-flex justify-content-between align-items-center mb-3">
                        <div className="d-flex gap-2 align-items-center">
                            <button
                                className="btn btn-outline-primary"
                                onClick={() => setShowFilters(!showFilters)}
                            >
                                <i className="fas fa-filter me-2"></i>
                                Filtres
                                {(selectedCategory || priceRange.min || priceRange.max) && (
                                    <span className="badge bg-danger ms-2">!</span>
                                )}
                            </button>
                            
                            {(searchQuery || selectedCategory || priceRange.min || priceRange.max) && (
                                <button
                                    className="btn btn-outline-secondary btn-sm"
                                    onClick={clearFilters}
                                >
                                    <i className="fas fa-times me-1"></i>
                                    Effacer
                                </button>
                            )}
                        </div>

                        <div className="d-flex gap-2 align-items-center">
                            {/* Affichage du nombre de résultats */}
                            <span className="text-muted">
                                {totalProducts} résultat{totalProducts > 1 ? 's' : ''}
                            </span>

                            {/* Mode d'affichage */}
                            <div className="btn-group" role="group">
                                <button
                                    type="button"
                                    className={`btn ${viewMode === 'grid' ? 'btn-primary' : 'btn-outline-primary'} btn-sm`}
                                    onClick={() => setViewMode('grid')}
                                >
                                    <i className="fas fa-th"></i>
                                </button>
                                <button
                                    type="button"
                                    className={`btn ${viewMode === 'list' ? 'btn-primary' : 'btn-outline-primary'} btn-sm`}
                                    onClick={() => setViewMode('list')}
                                >
                                    <i className="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Panel des filtres */}
                    {showFilters && (
                        <div className="card mb-4">
                            <div className="card-body">
                                <div className="row">
                                    {/* Filtre par catégorie */}
                                    <div className="col-md-4 mb-3">
                                        <label className="form-label">Catégorie</label>
                                        <select
                                            className="form-select"
                                            value={selectedCategory}
                                            onChange={(e) => handleCategoryChange(e.target.value)}
                                        >
                                            <option value="">Toutes les catégories</option>
                                            {categories.map(category => (
                                                <option key={category.id} value={category.id}>
                                                    {category.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    {/* Filtre par prix */}
                                    <div className="col-md-4 mb-3">
                                        <label className="form-label">Prix minimum</label>
                                        <div className="input-group">
                                            <input
                                                type="number"
                                                className="form-control"
                                                placeholder="0"
                                                min="0"
                                                step="0.01"
                                                value={priceRange.min}
                                                onChange={(e) => handlePriceRangeChange('min', e.target.value)}
                                            />
                                            <span className="input-group-text">€</span>
                                        </div>
                                    </div>

                                    <div className="col-md-4 mb-3">
                                        <label className="form-label">Prix maximum</label>
                                        <div className="input-group">
                                            <input
                                                type="number"
                                                className="form-control"
                                                placeholder="1000"
                                                min="0"
                                                step="0.01"
                                                value={priceRange.max}
                                                onChange={(e) => handlePriceRangeChange('max', e.target.value)}
                                            />
                                            <span className="input-group-text">€</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Tri */}
                    <div className="d-flex gap-2 mb-3">
                        <span className="text-muted align-self-center">Trier par:</span>
                        <button
                            className={`btn btn-sm ${sortBy === 'name' ? 'btn-primary' : 'btn-outline-primary'}`}
                            onClick={() => handleSortChange('name')}
                        >
                            Nom <i className={getSortIcon('name')}></i>
                        </button>
                        <button
                            className={`btn btn-sm ${sortBy === 'price' ? 'btn-primary' : 'btn-outline-primary'}`}
                            onClick={() => handleSortChange('price')}
                        >
                            Prix <i className={getSortIcon('price')}></i>
                        </button>
                        <button
                            className={`btn btn-sm ${sortBy === 'created_at' ? 'btn-primary' : 'btn-outline-primary'}`}
                            onClick={() => handleSortChange('created_at')}
                        >
                            Date <i className={getSortIcon('created_at')}></i>
                        </button>
                    </div>
                </div>
            </div>

            {/* Résultats */}
            <div className="row">
                <div className="col-12">
                    {loading ? (
                        <div className="text-center py-5">
                            <div className="spinner-border text-primary" role="status">
                                <span className="visually-hidden">Chargement...</span>
                            </div>
                            <p className="mt-3">Recherche en cours...</p>
                        </div>
                    ) : products.length > 0 ? (
                        <>
                            <ProductGrid 
                                products={products} 
                                emptyMessage="Aucun produit trouvé pour votre recherche."
                            />
                            
                            {/* Pagination */}
                            {totalPages > 1 && (
                                <nav className="mt-4">
                                    <ul className="pagination justify-content-center">
                                        <li className={`page-item ${currentPage === 1 ? 'disabled' : ''}`}>
                                            <button
                                                className="page-link"
                                                onClick={() => setCurrentPage(currentPage - 1)}
                                                disabled={currentPage === 1}
                                            >
                                                <i className="fas fa-chevron-left"></i>
                                            </button>
                                        </li>
                                        
                                        {[...Array(totalPages)].map((_, index) => {
                                            const pageNum = index + 1;
                                            // Afficher seulement quelques pages autour de la page courante
                                            if (
                                                pageNum === 1 || 
                                                pageNum === totalPages || 
                                                (pageNum >= currentPage - 2 && pageNum <= currentPage + 2)
                                            ) {
                                                return (
                                                    <li key={pageNum} className={`page-item ${currentPage === pageNum ? 'active' : ''}`}>
                                                        <button
                                                            className="page-link"
                                                            onClick={() => setCurrentPage(pageNum)}
                                                        >
                                                            {pageNum}
                                                        </button>
                                                    </li>
                                                );
                                            } else if (
                                                pageNum === currentPage - 3 || 
                                                pageNum === currentPage + 3
                                            ) {
                                                return (
                                                    <li key={pageNum} className="page-item disabled">
                                                        <span className="page-link">...</span>
                                                    </li>
                                                );
                                            }
                                            return null;
                                        })}
                                        
                                        <li className={`page-item ${currentPage === totalPages ? 'disabled' : ''}`}>
                                            <button
                                                className="page-link"
                                                onClick={() => setCurrentPage(currentPage + 1)}
                                                disabled={currentPage === totalPages}
                                            >
                                                <i className="fas fa-chevron-right"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            )}
                        </>
                    ) : (
                        <div className="text-center py-5">
                            <i className="fas fa-search text-muted mb-3" style={{ fontSize: '4rem' }}></i>
                            <h4 className="text-muted">Aucun produit trouvé</h4>
                            <p className="text-muted">
                                {searchQuery ? (
                                    <>Essayez d'autres mots-clés ou modifiez vos filtres</>
                                ) : (
                                    <>Commencez votre recherche pour voir les produits</>
                                )}
                            </p>
                            {(searchQuery || selectedCategory || priceRange.min || priceRange.max) && (
                                <button
                                    className="btn btn-primary mt-3"
                                    onClick={clearFilters}
                                >
                                    <i className="fas fa-refresh me-2"></i>
                                    Effacer les filtres
                                </button>
                            )}
                        </div>
                    )}
                </div>
            </div>

            {/* Suggestions de recherche (optionnel) */}
            {!loading && products.length === 0 && searchQuery && (
                <div className="row mt-4">
                    <div className="col-12">
                        <div className="card bg-light">
                            <div className="card-body">
                                <h6 className="card-title">
                                    <i className="fas fa-lightbulb me-2"></i>
                                    Suggestions
                                </h6>
                                <ul className="list-unstyled mb-0">
                                    <li>• Vérifiez l'orthographe des mots-clés</li>
                                    <li>• Essayez des termes plus généraux</li>
                                    <li>• Utilisez moins de mots-clés</li>
                                    <li>• Parcourez nos catégories</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

export default ProductSearch;