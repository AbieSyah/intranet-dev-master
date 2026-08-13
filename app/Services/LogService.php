<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Shortcut to log the current authenticated user.
     *
     * @param string      $action
     * @param string      $description
     * @param Request|null $request
     * @return void
     */
    public function logCurrentUser(string $action, string $description, ?Request $request = null): void
    {
        $userId = Auth::id();
        if (!$userId) {
            return;
        }

        $ip = $request ? $request->ip() : (request()->ip() ?? '0.0.0.0');
        $this->createLog($userId, $ip, $action, $description);
    }

    /**
     * Build a long, narrative description string containing before & after changes.
     * Uses the same free-text style as existing logs.
     *
     * @param string $base        e.g. "Edit Jenis Surat pada menu E-Sign."
     * @param array  $before      ['Nama' => 'Surat Dinas', 'Prefix' => 'SD', ...]
     * @param array  $after       ['Nama' => 'Surat Dinas Internal', 'Prefix' => 'SDI', ...]
     * @param string|null $by     e.g. user name
     * @param string|null $at     e.g. '03/08/2026 10:15'
     * @return string
     */
    public function buildDescription(string $base, array $before = [], array $after = [], ?string $by = null, ?string $at = null): string
    {
        $parts = [rtrim($base)];

        if (!empty($before)) {
            $parts[] = 'Before: ' . $this->flatten($before) . '.';
        }

        if (!empty($after)) {
            $parts[] = 'After: ' . $this->flatten($after) . '.';
        }

        if ($by) {
            $parts[] = 'By: ' . $by . '.';
        }

        if ($at) {
            $parts[] = 'At: ' . $at . '.';
        }

        return implode(' ', $parts);
    }

    /**
     * Serialize an associative array into a readable inline text.
     *
     * @param array $data
     * @return string
     */
    private function flatten(array $data): string
    {
        $chunks = [];
        foreach ($data as $label => $value) {
            $strValue = is_bool($value) ? ($value ? 'Ya' : 'Tidak') : (string) $value;
            if ($strValue === '' || $strValue === '-') {
                $strValue = '-';
            }
            $chunks[] = $label . ' = ' . $strValue;
        }
        return implode(', ', $chunks);
    }
}
