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
     * Atribut yang ditambahkan ke JSON.
     */
    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',
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
    public function lokasiTk1()
    {
        return $this->hasOne(LokasiTk1::class, 'id', 'lokasi_tk_1_id');
    }

    /**
     * Function untuk memanggil nama Lokasi Tingkat II.
     */
    public function lokasiTk2()
    {
        return $this->hasOne(LokasiTk2::class, 'id', 'lokasi_tk_2_id');
    }

    /**
     * Function untuk memanggil nama Lokasi Tingkat III.
     */
    public function lokasiTk3()
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
     * Function untuk memanggil kondisi.
     */
    public function kondisi()
    {
        if ($this->kondisi === 0) return 'UNSERVICEABLE';
        if ($this->kondisi === 1) return 'SERVICEABLE';
        return '-';
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
     * Function untuk mendapatkan created_at dalam format yang diinginkan untuk tampilan
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i') : null;
    }

    /**
     * Function untuk mendapatkan updated_at dalam format yang diinginkan untuk tampilan
     */
    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : null;
    }
}