<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GangguanPeralatan extends Model
{
    use HasFactory;

    protected $table = 'gangguan_peralatan'; // nama tabel
    protected $primaryKey = 'id'; // primary key

    /**
     * The attributes that are guarded.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];


    /**
     * Function untuk memanggil laporan dari gangguan peralatan.
     */
    public function laporan()
    {
        return $this->belongsTo(Laporan::class);
    }

    /**
     * Function untuk memanggil layanan dari gangguan peralatan.
     */
    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    /**
     * Function untuk memanggil peralatan dari gangguan peralatan.
     */
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class);
    }

    /**
     * Function untuk memanggil kondisi peralatan saat gangguan.
     */
    public function kondisiText()
    {
        if ($this->kondisi === 0) return 'RUSAK';
        if ($this->kondisi === 1) return 'NORMAL';
        if ($this->kondisi === 2) return 'NORMAL SEBAGIAN';
        return '-';
    }

    /**
     * Function untuk memanggil kondisi peralatan sebelum gangguan.
     */
    public function kondisiAwalText()
    {
        if ($this->kondisi === 0) return 'RUSAK';
        if ($this->kondisi === 1) return 'NORMAL';
        if ($this->kondisi === 2) return 'NORMAL SEBAGIAN';
        return '-';
    }

    /**
     * Relasi ke tindaklanjut gangguan peralatan
     */
    public function tlGangguanPeralatan()
    {
        return $this->hasMany(TlGangguanPeralatan::class, 'gangguan_id');
    }

    /**
     * Relasi ke tindaklanjut penggantian peralatan
     */
    public function tlPenggantianPeralatan()
    {
        return $this->hasOne(TlGangguanPeralatan::class, 'gangguan_id');
    }

    /**
     * Function untuk memanggil waktu gangguan format tertentu.
     */
    public function getWaktuAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i');
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
     * Function untuk memanggil created_at dengan format tertentu.
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i');
    }

    /**
     * Function untuk memanggil updated_at dengan format tertentu.
     */
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i');
    }
}
