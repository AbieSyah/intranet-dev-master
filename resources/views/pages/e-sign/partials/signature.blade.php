{{--
-- ============================================================================
-- PARTIAL: signature.blade.php
-- Document signature area: 3 signature slots (Sign 1, Sign 2, Sign 3).
-- Each slot shows: label, QR Code box, nama + jabatan employee.
-- Shared by all letter type templates.
--
-- Variables expected:
--   $doc   – Instance of App\Models\ESign (with employee loaded)
--   $signees – Collection of signee data (optional, falls back to $doc relations)
-- ============================================================================
--}}
@php
    // Load signee employees
    $signee1 = $doc->employee1_signee_id ? \App\Models\Employee::with('position')->find($doc->employee1_signee_id) : null;
    $signee2 = $doc->employee2_signee_id ? \App\Models\Employee::with('position')->find($doc->employee2_signee_id) : null;
    $signee3 = $doc->employee3_signee_id ? \App\Models\Employee::with('position')->find($doc->employee3_signee_id) : null;

    // Hanya tampilkan slot yang aktif di template (default: semua)
    $signActive = $signActive ?? [1, 2, 3];

    $hasSign1 = in_array(1, $signActive) && ($signee1 || $doc->employee1_signee_id);
    $hasSign2 = in_array(2, $signActive) && ($signee2 || $doc->employee2_signee_id);
    $hasSign3 = in_array(3, $signActive) && ($signee3 || $doc->employee3_signee_id);

    $signCount = ($hasSign1 ? 1 : 0) + ($hasSign2 ? 1 : 0) + ($hasSign3 ? 1 : 0);
    $signClass = $signCount === 1 ? 'sign-one' : ($signCount === 2 ? 'sign-two' : 'sign-three');
@endphp
<div class="signature-area {{ $signClass }} esign-signature-area" style="display:flex;flex-wrap:wrap;gap:24px;margin-top:18px;">
    @if($hasSign1)
    <div class="signature-box" style="text-align:center;min-width:160px;">
        <div style="font-weight:700;font-size:13px;margin-bottom:4px;color:#1e293b;">Sign 1</div>
        <div style="width:130px;height:90px;border:2px dashed #adb5bd;border-radius:8px;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;font-size:10px;color:#adb5bd;background:#f8f9fa;">
            <span>QR Code<br>Digital Signature</span>
        </div>
        <div style="font-size:12px;font-weight:600;color:#212529;margin-bottom:2px;">{{ $signee1->fullname ?? '—' }}</div>
        <div style="font-size:11px;color:#6c757d;">{{ $signee1->position->nama ?? '—' }}</div>
    </div>
    @endif
    @if($hasSign2)
    <div class="signature-box" style="text-align:center;min-width:160px;">
        <div style="font-weight:700;font-size:13px;margin-bottom:4px;color:#1e293b;">Sign 2</div>
        <div style="width:130px;height:90px;border:2px dashed #adb5bd;border-radius:8px;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;font-size:10px;color:#adb5bd;background:#f8f9fa;">
            <span>QR Code<br>Digital Signature</span>
        </div>
        <div style="font-size:12px;font-weight:600;color:#212529;margin-bottom:2px;">{{ $signee2->fullname ?? '—' }}</div>
        <div style="font-size:11px;color:#6c757d;">{{ $signee2->position->nama ?? '—' }}</div>
    </div>
    @endif
    @if($hasSign3)
    <div class="signature-box" style="text-align:center;min-width:160px;">
        <div style="font-weight:700;font-size:13px;margin-bottom:4px;color:#1e293b;">Sign 3</div>
        <div style="width:130px;height:90px;border:2px dashed #adb5bd;border-radius:8px;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;font-size:10px;color:#adb5bd;background:#f8f9fa;">
            <span>QR Code<br>Digital Signature</span>
        </div>
        <div style="font-size:12px;font-weight:600;color:#212529;margin-bottom:2px;">{{ $signee3->fullname ?? '—' }}</div>
        <div style="font-size:11px;color:#6c757d;">{{ $signee3->position->nama ?? '—' }}</div>
    </div>
    @endif
</div>
