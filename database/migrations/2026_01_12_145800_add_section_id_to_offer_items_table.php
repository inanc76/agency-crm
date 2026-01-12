<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('offer_items', function (Blueprint $table) {
            $table->foreignUuid('section_id')->nullable()->after('offer_id')->constrained('offer_sections')->nullOnDelete();
        });

        // Data Migration: Create a default section for each offer and link existing items
        $offers = \DB::table('offers')->get();
        foreach ($offers as $offer) {
            $sectionId = (string) \Illuminate\Support\Str::uuid();
            \DB::table('offer_sections')->insert([
                'id' => $sectionId,
                'offer_id' => $offer->id,
                'title' => 'Teklif Bölümü - 1',
                'description' => null,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \DB::table('offer_items')
                ->where('offer_id', $offer->id)
                ->whereNull('section_id')
                ->update(['section_id' => $sectionId]);
        }
    }

    public function down(): void
    {
        Schema::table('offer_items', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });
    }
};
