<?php

use App\Models\Area;
use App\Models\AssetType;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Master\Building;
use App\Models\master\ITAsset;
use App\Models\Room;
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
        // Schema::create('brands', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->timestamps();
        // });
        Schema::create('it_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique(); // ITyymm-XXXX
            $table->foreignIdFor(AssetType::class)->constrained()->cascadeOnDelete();
            $table->string('brand');
            $table->text('specification')->nullable();
            $table->text('software')->nullable();
            $table->date('year_registered'); // simpan sebagai DATE
            $table->decimal('price', 15, 2)->nullable();
            $table->foreignIdFor(Employee::class)->nullable()->constrained('employees')->cascadeOnDelete();
            $table->string('status')->default(1);
            // $table->foreignIdFor(Area::class)->constrained()->cascadeOnDelete();
            // $table->foreignIdFor(Department::class)->constrained()->cascadeOnDelete();
            // $table->string('location')->nullable(); // get from the storage folder at app/city-list.json
            // $table->integer('age')->nullable();
            // $table->text('additional_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        // Schema::dropIfExists('asset_types');
        // Schema::dropIfExists('brands');
        // Schema::table('asset_histories', function (Blueprint $table) {
        //     $table->dropForeignIdFor(ITAsset::class);
        // });
        Schema::dropIfExists('it_assets');
        // Schema::dropIfExists('asset_histories');
    }
};
