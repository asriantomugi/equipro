<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'aktivitas',
        'ip_address',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    /**
     * Relasi ke model User.
     * Setiap log aktivitas dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Otomatis isi created_by dan updated_by saat create/update.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            $userId = Auth::id();
            if (!$model->user_id) $model->user_id = $userId;
            if (!$model->created_by) $model->created_by = $userId;
            if (!$model->updated_by) $model->updated_by = $userId;
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }
}
