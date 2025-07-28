<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Électronique',
                'slug' => 'electronique',
                'description' => 'Smartphones, ordinateurs, tablettes et accessoires high-tech',
                'is_active' => true
            ],
            [
                'name' => 'Vêtements',
                'slug' => 'vetements', 
                'description' => 'Mode homme, femme et enfant',
                'is_active' => true
            ],
            [
                'name' => 'Maison & Jardin',
                'slug' => 'maison-jardin',
                'description' => 'Décoration, mobilier, jardinage et bricolage',
                'is_active' => true
            ],
            [
                'name' => 'Sports & Loisirs',
                'slug' => 'sports-loisirs',
                'description' => 'Équipements sportifs, jeux et loisirs créatifs',
                'is_active' => true
            ],
            [
                'name' => 'Livres & Médias',
                'slug' => 'livres-medias',
                'description' => 'Livres, films, musique et jeux vidéo',
                'is_active' => true
            ],
            [
                'name' => 'Beauté & Santé',
                'slug' => 'beaute-sante',
                'description' => 'Cosmétiques, parfums et produits de bien-être',
                'is_active' => true
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✅ 6 catégories créées avec succès!');
    }
}