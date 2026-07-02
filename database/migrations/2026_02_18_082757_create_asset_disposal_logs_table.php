<?php

use App\Models\AssetDisposal;
use App\Models\DisposalApprovalPath;
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
        Schema::create('asset_disposal_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AssetDisposal::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(DisposalApprovalPath::class)->nullable()->constrained();
            $table->boolean('for_buyer')->default(false);
            $table->string('status')->default('waiting');
            $table->text('comments')->nullable();
            $table->dateTime('actioned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_disposal_logs');
    }
};
