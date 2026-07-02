<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ITAsset;
use App\Models\Room;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ITAsset::class, 'it_asset_id')->constrained('it_assets')->cascadeOnDelete();
            $table->string('action_type'); // created, movement(owner_changed), status_changed, replaced, disposed
            $table->string('from_value')->nullable();
            $table->string('to_value')->nullable();

            $table->string('reference_type')->nullable(); // "ticket" and "change" are for IT Service Management. and "system" for automatic
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('description')->nullable();
            $table->foreignIdFor(User::class, 'user_id')->comment("Performed by")->constrained('users'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_histories');
    }
};
