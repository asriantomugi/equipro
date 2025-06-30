<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan'; // nama tabel
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
     * Function untuk memanggil fasilitas dari layanan.
     */
    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class);
    }

    /**
     * Function untuk memanggil nama Lokasi Tingkat I.
     */
    public function LokasiTk1()
    {
        return $this->hasOne(LokasiTk1::class, 'id', 'lokasi_tk_1_id');
    }

    /**
     * Function untuk memanggil nama Lokasi Tingkat II.
     */
    public function LokasiTk2()
    {
        return $this->hasOne(LokasiTk2::class, 'id', 'lokasi_tk_2_id');
    }

    /**
     * Function untuk memanggil nama Lokasi Tingkat III.
     */
    public function LokasiTk3()
    {
        return $this->hasOne(LokasiTk3::class, 'id', 'lokasi_tk_3_id');
    }

    /**
     * Function untuk memanggil data daftar peralatan layanan.
     */
    public function daftarPeralatanLayanan()
    {
        return $this->hasMany(DaftarPeralatanLayanan::class, 'layanan_id');
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
