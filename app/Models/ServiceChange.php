<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceChange extends Model
{
    use HasFactory;

    // $table->id();
    //         $table->string('change_no')->unique();
    //         $table->foreignIdFor(ServiceTicket::class)->constrained()->cascadeOnDelete();

    //         $table->text('it_notice')->nullable(); // internal note for IT team, invisible to user
    //         $table->string('change_type', 30); //['standard', 'normal', 'emergency']
    //         $table->string('status', 30)->default('proposed'); //['proposed', 'approved', 'rejected']

    //         $table->datetime('execution_plan')->nullable();
    //         $table->foreignIdFor(Employee::class, 'approved_by')->nullable('employees')->restrictOnDelete();
    //         $table->timestamp('approved_at')->nullable();
    //         $table->timestamps();
    protected $fillable = [
        'change_no',
        'service_ticket_id',
        'it_notice',
        'change_type',
        'status',
        'planned_start',
        'planned_end',
        'actual_start',
        'actual_end',
        'done_at',
        'approver_id',
        'proposer_id',
        'approved_at',
        'proposed_at',
    ];

    protected $casts = [
        'planned_start' => 'datetime',
        'planned_end' => 'datetime',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
        'approved_at' => 'datetime',
        'proposed_at' => 'datetime',
        'done_at' => 'datetime',
    ];

    const TYPE_STANDARD = 'standard';
    const TYPE_NORMAL = 'normal';
    const TYPE_EMERGENCY = 'emergency';

    const STATUS_PROPOSED = 'proposed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_DONE = 'done';

    public function ticket()
    {
        return parent::belongsTo(ServiceTicket::class, 'service_ticket_id', 'id');
    }

    public function proposer()
    {
        return parent::belongsTo(Employee::class, 'proposer_id', 'id');
    }

    public function approver()
    {
        return parent::belongsTo(Employee::class, 'approver_id', 'id');
    }
}
