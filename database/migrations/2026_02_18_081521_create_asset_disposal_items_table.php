<?php

use App\Models\AssetDisposal;
use App\Models\ITAsset;
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
        Schema::create('asset_disposal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AssetDisposal::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(ITAsset::class, 'it_asset_id')->constrained('it_assets');
            $table->string('current_status')->comment('the asset status at the time disposal requested');
            $table->decimal('buy_price', 15, 2); // Snapshot of buy price during submission
            $table->decimal('sale_price', 15, 2); // Snapshot of sale price during submission
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_disposal_items');
    }
};
