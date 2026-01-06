<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReferenceCategory;
use App\Models\ReferenceItem;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        // Gender Category
        $gender = ReferenceCategory::firstOrCreate(
            ['key' => 'GENDER'],
            ['name' => 'Cinsiyet', 'description' => 'Kullanıcı cinsiyet tercihleri']
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'GENDER', 'key' => 'MALE'],
            ['display_label' => 'Erkek', 'sort_order' => 1, 'metadata' => ['color' => 'blue']]
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'GENDER', 'key' => 'FEMALE'],
            ['display_label' => 'Kadın', 'sort_order' => 2, 'metadata' => ['color' => 'pink']]
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'GENDER', 'key' => 'OTHER'],
            ['display_label' => 'Diğer', 'sort_order' => 3, 'metadata' => ['color' => 'gray']]
        );

        // Service Status Category
        $serviceStatus = ReferenceCategory::firstOrCreate(
            ['key' => 'SERVICE_STATUS'],
            ['name' => 'Hizmet Durumları', 'description' => 'Hizmetlerin aktiflik durumları']
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'SERVICE_STATUS', 'key' => 'ACTIVE'],
            ['display_label' => 'Aktif', 'sort_order' => 1, 'metadata' => ['color' => 'green']]
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'SERVICE_STATUS', 'key' => 'PASSIVE'],
            ['display_label' => 'Pasif', 'sort_order' => 2, 'metadata' => ['color' => 'red']]
        );
    }
}
