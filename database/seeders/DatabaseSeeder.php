<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->command->info('🌱 Démarrage du seeding simple...');
        
        // Créer utilisateur admin
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Test',
                'password' => bcrypt('password'),
                'email_verified_at' => now()
            ]
        );
        $this->command->info('✅ Utilisateur créé');

        // Créer catégories
        $categories = [
            ['name' => 'Électronique', 'slug' => 'electronique', 'description' => 'Produits électroniques', 'is_active' => true],
            ['name' => 'Vêtements', 'slug' => 'vetements', 'description' => 'Mode et accessoires', 'is_active' => true],
            ['name' => 'Maison', 'slug' => 'maison', 'description' => 'Décoration et mobilier', 'is_active' => true],
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
        $this->command->info('✅ ' . count($categories) . ' catégories créées');

        // Créer produits
        $electronique = \App\Models\Category::where('slug', 'electronique')->first();
        
        $products = [
            [
                'name' => 'iPhone 15',
                'slug' => 'iphone-15',
                'description' => 'Smartphone Apple dernière génération avec puce A17 Pro',
                'short_description' => 'iPhone 15 128GB',
                'sku' => 'IPH15-128',
                'price' => 999.00,
                'stock' => 10,
                'featured' => true,
                'status' => 'active',
                'in_stock' => true,
                'category_id' => $electronique->id
            ],
            [
                'name' => 'MacBook Air M2',
                'slug' => 'macbook-air-m2',
                'description' => 'Ordinateur portable ultra-fin avec puce Apple M2',
                'short_description' => 'MacBook Air M2 256GB',
                'sku' => 'MBA-M2-256',
                'price' => 1299.00,
                'sale_price' => 1199.00,
                'stock' => 5,
                'featured' => true,
                'status' => 'active',
                'in_stock' => true,
                'category_id' => $electronique->id
            ]
        ];

        foreach ($products as $prod) {
            \App\Models\Product::firstOrCreate(['sku' => $prod['sku']], $prod);
        }
        $this->command->info('✅ ' . count($products) . ' produits créés');

        $this->command->info('🎉 Seeding terminé avec succès!');
        $this->command->info('📧 Email admin: admin@test.com');
        $this->command->info('🔐 Password: password');
    }
}