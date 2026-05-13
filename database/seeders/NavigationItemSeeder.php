<?php

namespace Database\Seeders;

use App\Models\NavigationItem;
use Illuminate\Database\Seeder;

class NavigationItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['label_fr' => 'Produits', 'label_en' => 'Products', 'type' => NavigationItem::TYPE_MEGA_SERVICES, 'url' => '/{locale}/products', 'sort_order' => 10],
            ['label_fr' => 'Developers', 'label_en' => 'Developers', 'type' => NavigationItem::TYPE_MEGA_DEVELOPERS, 'url' => '/{locale}/developers', 'sort_order' => 20],
            ['label_fr' => 'Solutions', 'label_en' => 'Solutions', 'type' => NavigationItem::TYPE_MEGA_SOLUTIONS, 'url' => '/{locale}/solutions', 'sort_order' => 30],
            ['label_fr' => 'Coverage', 'label_en' => 'Coverage', 'type' => NavigationItem::TYPE_LINK, 'url' => '/{locale}/coverage', 'sort_order' => 40],
            ['label_fr' => 'Pricing', 'label_en' => 'Pricing', 'type' => NavigationItem::TYPE_LINK, 'url' => '/{locale}/pricing', 'sort_order' => 50],
            ['label_fr' => 'Blog', 'label_en' => 'Blog', 'type' => NavigationItem::TYPE_LINK, 'url' => '/{locale}/blog', 'sort_order' => 60],
            ['label_fr' => 'Societe', 'label_en' => 'Company', 'type' => NavigationItem::TYPE_MEGA_COMPANY, 'url' => '/{locale}/company', 'sort_order' => 70],
        ];

        foreach ($items as $item) {
            NavigationItem::updateOrCreate(
                [
                    'menu_area' => 'main',
                    'parent_id' => null,
                    'label_fr' => $item['label_fr'],
                ],
                array_merge($item, [
                    'menu_area' => 'main',
                    'parent_id' => null,
                    'is_active' => true,
                    'opens_new_tab' => false,
                ])
            );
        }
    }
}
