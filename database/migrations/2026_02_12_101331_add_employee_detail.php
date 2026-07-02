<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Prompts\Table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function(Blueprint $table) {
            $table->text('domicile_address')->nullable()->comment('Current living address');
            $table->string('emergency_contact')->nullable()->comment('Name of person to contact in emergency');
            $table->string('emergency_contact_relation')->nullable()->comment('Relation with the emergency contact');
            $table->string('emergency_contact_handphone')->nullable()->comment('Phone number for emergency contact');
            $table->text('emergency_contact_address')->nullable()->comment('Address of the emergency contact');

            // Informasi Pekerjaan & Status
            $table->date('permanent_startdate')->nullable()->comment('The date employee became permanent');
            $table->string('iso_position')->nullable()->comment('Position name based on ISO standards');
            $table->string('cost_center')->nullable()->comment('Budget tracking code/department');

            $table->string('last_education')->nullable()->comment('Highest level of education completed');
            $table->string('major_last_education')->nullable()->comment('Field of study/Major');
            $table->string('last_education_institutional')->nullable()->comment('Name of University or School');

            $table->string('tax_dependents')->comment('Number of dependents for tax calculation (PTKP) (TK, K0, K1, K2, K3)');
            $table->string('npwp')->nullable()->comment('Tax Identification Number (NPWP)');
        });

        Schema::create('employee_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->onDelete('cascade');
            $table->string('category')->comment('career, reward, disciplinary');
            $table->string('type')->nullable()->comment('Career(Promotion, Mutation, Demotion). Disciplinary(Teguran, SP1, SP2, SP3)');
            $table->date('date');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'domicile_address',
                'emergency_contact',
                'emergency_contact_relation',
                'emergency_contact_handphone',
                'emergency_contact_address',
                'permanent_startdate',
                'iso_position',
                'cost_center',
                'last_education',
                'major_last_education',
                'last_education_institutional',
                'tax_dependents',
                'npwp'
            ]);
        });
        Schema::dropIfExists('employee_milestones');
    }
};
