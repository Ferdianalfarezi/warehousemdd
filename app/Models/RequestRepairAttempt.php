<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestRepairAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_repair_id', 'no', 'attempt_number', 'part_no', 'nama', 'pic_names',
        'analisa_penyebab', 'tindakan_perbaikan', 'catatan_penggantian_sparepart',
        'sparepart_items', // ⬅️ baru
        'item', 'proses_grinding', 'shim_up', 'status_burry', 'standart_burry',
        'group_leader', 'operator', 'plan', 'actual', 'remark', 'judge',
        'tanggal_cek', 'lot_prod', 'awal', 'tengah', 'akhir', 'qty',
        'remark_monitoring', 'judge_monitoring', 'plan_permanen', 'actual_permanen',
        'rootcause', 'recovery', 'assy_trial_check', 'judge_permanen',
        'on_process_at', 'on_trial_at', 'ng_judged_at',
        'durasi_on_process_seconds', 'durasi_on_trial_seconds', 'judged_by',
        'total_paused_seconds', // ⬅️ baru — sebelumnya dikirim controller tapi gak ke-fillable, ke-drop diem-diem
        'cycle_number',         // ⬅️ baru — sama kasusnya
    ];

    protected $casts = [
        'tanggal_cek'              => 'date',
        'on_process_at'            => 'datetime',
        'on_trial_at'              => 'datetime',
        'ng_judged_at'             => 'datetime',
        'sparepart_items'          => 'array',   // ⬅️ baru
        'total_paused_seconds'     => 'integer', // ⬅️ baru
        'cycle_number'             => 'integer', // ⬅️ baru
        'durasi_on_process_seconds'=> 'integer',
        'durasi_on_trial_seconds'  => 'integer',
    ];

    public function requestRepair()
    {
        return $this->belongsTo(RequestRepair::class);
    }

    public function judgedBy()
    {
        return $this->belongsTo(User::class, 'judged_by');
    }
}