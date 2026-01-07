<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $turkiyeId = DB::table('countries')->where('name', 'Türkiye')->value('id');

        if (!$turkiyeId) {
            return;
        }

        $items = DB::table('reference_items')
            ->where('category_key', 'CITIES')
            ->get();

        foreach ($items as $item) {
            DB::table('cities')->updateOrInsert(
                ['id' => $item->id],
                [
                    'country_id' => $turkiyeId,
                    'name' => $item->display_label,
                    'is_active' => $item->is_active,
                    'sort_order' => $item->sort_order,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('cities')->whereNotNull('id')->delete();
    }
};
