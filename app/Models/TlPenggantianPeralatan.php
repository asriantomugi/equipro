<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TlPenggantianPeralatan extends Model
{
    use HasFactory;

    protected $table = 'tl_penggantian_peralatan'; // nama tabel
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
     * Function untuk memanggil tl gangguan 
     */
    public function gangguan()
    {
        return $this->belongsTo(TlGangguanPeralatan::class, 'tl_gangguan_id');
    }


    /**
     * Function untuk memanggil laporan 
     */
    public function laporan()
    {
        return $this->belongsTo(Laporan::class);
    }

    /**
     * Function untuk memanggil layanan 
     */
    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    /**
     * Relasi ke peralatan lama
     */
    public function peralatanLama()
    {
        return $this->belongsTo(Peralatan::class, 'peralatan_lama_id');
    }

    /**
     * Relasi ke peralatan baru
     */
    public function peralatanBaru()
    {
        return $this->belongsTo(Peralatan::class, 'peralatan_baru_id');
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
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }

    /**
     * Function untuk memanggil updated_at dengan format tertentu.
     */
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }
}
