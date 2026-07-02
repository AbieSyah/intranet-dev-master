<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItsmPriority extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_score',
        'max_score',
        'level',
        'min_sla_hours',
        'max_sla_hours',
        'sla_label'
    ];

    protected $appends = ['formated_sla'];

    const LEVEL_CRITICAL = 'critical';
    const LEVEL_HIGH = 'high';
    const LEVEL_MEDIUM = 'medium';
    const LEVEL_LOW = 'low';

    const SLA_LABEL_LESS_THAN = '<';
    const SLA_LABEL_GREATER_THAN = '>';
    const SLA_LABEL_APPROXIMATELY = '~';
    const SLA_LABEL_RANGE = '-';

    protected function formatedSla(): Attribute
    {
        return Attribute::make(
            get: function () {
                $minHours = $this->min_sla_hours;
                $maxHours = $this->max_sla_hours;
                $label = $this->sla_label; // <, >, ~, -

                // Fungsi pembantu untuk konversi jam ke teks hari
                $formatHours = function ($hours) {
                    if ($hours < 8) {
                        return "$hours Jam";
                    }
                    
                    $days = floor($hours / 8);
                    $remainder = $hours % 8;

                    if ($remainder > 0) {
                        return "$days Hari $remainder Jam Kerja";
                    }
                    
                    return "$days Hari Kerja";
                };

                // Logika berdasarkan Simbol Label SLA
                if ($label === '<') {
                    return "< " . $formatHours($maxHours);
                } 
                
                if ($label === '>') {
                    return "> " . $formatHours($maxHours);
                }

                if ($label === '~') {
                    return "~ " . $formatHours($maxHours);
                }

                if ($label === '-') {
                    return $formatHours($minHours) . " - " . $formatHours($maxHours);
                }

                // Default adalah Range (-) e.g: "4 - 8 Jam" atau "2 - 3 Hari Kerja"
                if ($minHours < 8 && $maxHours <= 8) {
                    return "$minHours - $maxHours Jam Kerja";
                }

                return floor($minHours / 8) . " - " . floor($maxHours / 8) . " Hari Kerja";
            }
        );
    }

    public static function getColorMap()
    {
        $levels = static::orderBy('min_score', 'asc')->get();
        $total = $levels->count();
        $colorMap = [];

        if ($total === 0) return $colorMap;

        // Batas spektrum HSL yang kita gunakan
        $startHue = 75; // Biru (Low / Aman)
        $endHue = 0;     // Merah (Critical / Bahaya)

        if ($total === 1) {
            $colorMap[$levels->first()->level] = "hsl($endHue, 85%, 45%)";
            return $colorMap;
        }

        foreach ($levels as $index => $item) {
            // Hitung rasio posisi data (0.0 hingga 1.0)
            $ratio = $index / ($total - 1);

            // Rumus interpolasi menurunkan nilai Hue dari 220 ke 0
            $currentHue = $startHue - ($ratio * ($startHue - $endHue));

            // KORIDOR ANTI-HIJAU:
            // Spektrum hijau berada di kisaran 85 - 155.
            // Jika matematika menghasilkan warna di area ini, kita paksa geser ke Kuning (65)
            // if ($currentHue > 85 && $currentHue < 155) {
            //     $currentHue = 65; 
            // }

            // Saturation 85% dan Lightness 45% sangat pas untuk teks putih/kontras dashboard
            $colorMap[$item->level] = [
                'label' => $item->level,
                'min_score' => $item->min_score,
                'max_score' => $item->max_score,
                'color' => "hsl($currentHue, 85%, 45%)",
                'formatted_sla' => $item->formated_sla
            ];
        }

        return $colorMap;
    }
}
