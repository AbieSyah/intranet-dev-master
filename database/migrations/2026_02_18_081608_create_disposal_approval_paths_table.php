<?php

use App\Models\AssetDisposal;
use App\Models\Employee;
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
        Schema::create('disposal_approval_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained(); // The actual Person
            $table->foreignIdFor(AssetDisposal::class)->constrained()->cascadeOnDelete();
            $table->string('role_name'); // "As What" (e.g., Dept Head, Finance, Manager)
            $table->integer('step_order'); // 1, 2, 3...
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposal_approval_paths');
    }
};
