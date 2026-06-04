<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            ServiceSeeder::class,
            ServicePriceSeeder::class,
            LegalPageSeeder::class,
            MarketingPageSeeder::class,
            ProductPageSeeder::class,
            BlogContentSeeder::class,
            NavigationItemSeeder::class,
            CompanyProfileSeeder::class,
            RoleProfileSeeder::class,
            AiWebSourceSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
