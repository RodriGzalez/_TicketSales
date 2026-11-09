<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'action',
        'module',
        'description',
        'changes',
        'ip_address',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function admin()
    {
        // 👈 Cambiamos $table por $this
        return $this->belongsTo(Admin::class);
    }
}