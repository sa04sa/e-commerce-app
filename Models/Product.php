<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'price',
        'sale_price',
        'stock',
        'in_stock',
        'status',
        'featured',
        'images',
        'category_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
        'in_stock' => 'boolean',
        'featured' => 'boolean',
        'images' => 'array'
    ];

    protected $appends = [
        'is_on_sale',
        'discount_percentage'
    ];

    /**
     * Relation avec la catégorie
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relation avec les items du panier
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Relation avec les items de commande
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relation avec la wishlist
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Scope pour les produits actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour les produits vedettes
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope pour les produits en stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope pour recherche
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%")
              ->orWhere('short_description', 'LIKE', "%{$term}%");
        });
    }

    /**
     * Accesseur pour vérifier si le produit est en promotion
     */
    public function getIsOnSaleAttribute()
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    /**
     * Accesseur pour le pourcentage de réduction
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->is_on_sale) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    /**
     * Accesseur pour le prix final
     */
    public function getFinalPriceAttribute()
    {
        return $this->is_on_sale ? $this->sale_price : $this->price;
    }

    /**
     * Accesseur pour la première image
     */
    public function getMainImageAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            return $this->images[0];
        }
        return 'no-product.png';
    }

    /**
     * Accesseur pour l'URL de l'image principale
     */
    public function getImageUrlAttribute()
    {
        return asset('storage/products/' . $this->main_image);
    }

    /**
     * Mutateur pour générer automatiquement le slug
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        if (!$this->slug) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    /**
     * Mutateur pour mettre à jour le stock et le statut
     */
    public function setStockAttribute($value)
    {
        $this->attributes['stock'] = $value;
        $this->attributes['in_stock'] = $value > 0;
    }

    /**
     * Boot method pour les événements du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (!$product->slug) {
                $product->slug = Str::slug($product->name);
            }
            if (!$product->sku) {
                $product->sku = 'PRD-' . strtoupper(Str::random(6));
            }
        });

        static::updating(function ($product) {
            // Mettre à jour in_stock basé sur le stock
            if ($product->isDirty('stock')) {
                $product->in_stock = $product->stock > 0;
            }
        });
    }

    /**
     * Route key name pour les URLs
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Méthode pour décrémenter le stock
     */
    public function decrementStock($quantity = 1)
    {
        $this->decrement('stock', $quantity);
        $this->update(['in_stock' => $this->stock > 0]);
    }

    /**
     * Méthode pour incrémenter le stock
     */
    public function incrementStock($quantity = 1)
    {
        $this->increment('stock', $quantity);
        $this->update(['in_stock' => true]);
    }

    /**
     * Vérifier si le produit peut être acheté
     */
    public function canBePurchased($quantity = 1)
    {
        return $this->status === 'active' && 
               $this->in_stock && 
               $this->stock >= $quantity;
    }

    /**
     * Obtenir le prix formaté
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2, ',', ' ') . ' €';
    }

    /**
     * Obtenir le prix de vente formaté
     */
    public function getFormattedSalePriceAttribute()
    {
        return $this->sale_price ? number_format($this->sale_price, 2, ',', ' ') . ' €' : null;
    }
}