<?php

namespace Database\Seeders;

use App\Models\ReferenceCategory;
use App\Models\ReferenceItem;
use Illuminate\Database\Seeder;

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

        // Service Category
        $serviceCategory = ReferenceCategory::firstOrCreate(
            ['key' => 'SERVICE_CATEGORY'],
            ['name' => 'Hizmet Kategorileri', 'description' => 'Hizmet türleri ve kategorileri']
        );

        ReferenceItem::updateOrCreate(
            ['category_key' => 'SERVICE_CATEGORY', 'key' => 'HOSTING'],
            ['display_label' => 'Hosting', 'sort_order' => 1, 'metadata' => ['color' => 'blue']]
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'SERVICE_CATEGORY', 'key' => 'DOMAIN'],
            ['display_label' => 'Domain', 'sort_order' => 2, 'metadata' => ['color' => 'purple']]
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'SERVICE_CATEGORY', 'key' => 'SSL'],
            ['display_label' => 'SSL Sertifikası', 'sort_order' => 3, 'metadata' => ['color' => 'green']]
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'SERVICE_CATEGORY', 'key' => 'MAINTENANCE'],
            ['display_label' => 'Web Bakım (Aylık)', 'sort_order' => 4, 'metadata' => ['color' => 'orange']]
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'SERVICE_CATEGORY', 'key' => 'MAINTENANCE_YEARLY'],
            ['display_label' => 'Web Bakım (Yıllık)', 'sort_order' => 5, 'metadata' => ['color' => 'amber']]
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'SERVICE_CATEGORY', 'key' => 'MAINTENANCE_MONTHLY'],
            ['display_label' => 'Destek (Adam-Saat)', 'sort_order' => 6, 'metadata' => ['color' => 'yellow']]
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'SERVICE_CATEGORY', 'key' => 'MAINTENANCE_SERVER'],
            ['display_label' => 'Sunucu Bakım', 'sort_order' => 7, 'metadata' => ['color' => 'red']]
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'SERVICE_CATEGORY', 'key' => 'WEB_DEVELOPMENT'],
            ['display_label' => 'Web Geliştirme', 'sort_order' => 8, 'metadata' => ['color' => 'indigo']]
        );

        ReferenceItem::firstOrCreate(
            ['category_key' => 'SERVICE_CATEGORY', 'key' => 'MOBILE_DEVELOPMENT'],
            ['display_label' => 'Mobil Geliştirme', 'sort_order' => 9, 'metadata' => ['color' => 'pink']]
        );
    }
}
