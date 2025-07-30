<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'waktu_open' => 'datetime',
        'waktu_close' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // Auto fill created_by saat create
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
            }
        });

        // Auto fill updated_by saat update
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Function untuk memanggil layanan dari laporan.
     */
    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    /**
     * Relasi ke gangguan peralatan
     */
    public function gangguanPeralatan()
    {
        return $this->hasMany(GangguanPeralatan::class, 'laporan_id');
    }

    /**
     * Relasi ke gangguan non peralatan
     */
    public function gangguanNonPeralatan()
    {
        return $this->hasOne(GangguanNonPeralatan::class, 'laporan_id');
    }

    /**
     * Relasi ke tindak lanjut gangguan peralatan
     */
    public function tlGangguanPeralatan()
    {
        return $this->hasMany(TlGangguanPeralatan::class, 'laporan_id');
    }

    /**
     * Relasi ke tindak lanjut gangguan non peralatan
     */
    public function tlGangguanNonPeralatan()
    {
        return $this->hasMany(TlGangguanNonPeralatan::class, 'laporan_id');
    }

    /**
     * Relasi ke penggantian peralatan
     */
    public function tlPenggantianPeralatan()
    {
        return $this->hasMany(TlPenggantianPeralatan::class, 'laporan_id');
    }

    /**
     * Relasi ke data penggantian peralatan (alias untuk backward compatibility)
     */
    public function penggantian()
    {
        return $this->hasOne(TlPenggantianPeralatan::class, 'laporan_id');
    }

    public function getStatusLabelAttribute()
    {
        $status = $this->status;

        if ($status == config('constants.status_laporan.draft')) {
            return '<span class="badge bg-warning">DRAFT</span>';
        } elseif ($status == config('constants.status_laporan.open')) {
            return '<span class="badge bg-success">OPEN</span>';
        } elseif ($status == config('constants.status_laporan.closed')) {
            return '<span class="badge bg-secondary">CLOSED</span>';
        } else {
            return '<span class="badge bg-dark">UNKNOWN</span>';
        }
    }

    /**
     * Function untuk memanggil user created_by.
     */
    public function getCreatedName()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * Function untuk memanggil user updated_by.
     */
    public function getUpdatedName()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    /**
     * Function untuk mendapatkan waktu open dalam format yang diinginkan
     */
    public function getWaktuOpenFormattedAttribute()
    {
        return $this->waktu_open ? Carbon::parse($this->waktu_open)->format('d/m/Y') : null;
    }

    /**
     * Function untuk mendapatkan waktu close dalam format yang diinginkan
     */
    public function getWaktuCloseFormattedAttribute()
    {
        return $this->waktu_close ? Carbon::parse($this->waktu_close)->format('d/m/Y') : null;
    }

    /**
     * Function untuk mendapatkan created_at dalam format yang diinginkan untuk tampilan
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null;
    }

    /**
     * Function untuk mendapatkan updated_at dalam format yang diinginkan untuk tampilan
     */
    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null;
    }
}