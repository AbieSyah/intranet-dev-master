<?php

namespace App\Services;

use App\Models\Log;

class LogService
{
    /**
     * Create a log entry.
     *
     * @param int $userId
     * @param string $ipAddress
     * @param string $action
     * @param string $description
     * @return void
     */
    public function createLog(int $userId, string $ipAddress, string $action, string $description): void
    {
        $log = new Log();
        $log->user_id = $userId;
        $log->ip_address = $ipAddress;
        $log->action = $action;
        $log->description = $description;
        $log->save();
    }
}
