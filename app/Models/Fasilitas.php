<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas'; // Nama tabel
    protected $primaryKey = 'id';   // Primary key

    /**
     * Atribut yang tidak boleh diisi secara massal.
     */
    protected $guarded = [
        'id'
    ];

    /**
     * Relasi: Fasilitas memiliki banyak layanan.
     */
    public function getLayanan()
    {
        return $this->hasMany(Layanan::class);
    }

    /**
     * Menghitung jumlah layanan serviceable (status TRUE, kondisi TRUE).
     */
    public function getJlhLayananServ()
    {
        return $this->getLayanan()
                    ->where('status', true)
                    ->where('kondisi', true)
                    ->count();
    }

    /**
     * Menghitung jumlah layanan unserviceable (status TRUE, kondisi FALSE).
     */
    public function getJlhLayananUnserv()
    {
        return $this->getLayanan()
                    ->where('status', true)
                    ->where('kondisi', false)
                    ->count();
    }

    /**
     * Relasi: User yang membuat fasilitas.
     */
    public function getCreatedName()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * Relasi: User yang mengupdate fasilitas.
     */
    public function getUpdatedName()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    /**
     * Format custom untuk created_at.
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }

    /**
     * Format custom untuk updated_at.
     */
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }

    /**
     * ✅ Relasi: Fasilitas memiliki banyak laporan.
     */
   public function laporan()
{
    return $this->hasMany(Laporan::class, 'layanan_id', 'id');
}

}
