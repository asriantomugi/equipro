<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas'; // nama tabel
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
     * Function untuk memanggil layanan dari fasilitas tsb.
     */
    public function getLayanan()
    {
        return $this->hasMany(Layanan::class);
    }

    // Fungsi untuk memanggil jumlah layanan serviceable dari fasilitas tsb (status = TRUE dan kondisi = TRUE).
    public function getJlhLayananServ()
    {
        return $this->getLayanan()->where('status', TRUE)->where('kondisi', TRUE)->count();
    }

    
    // Fungsi untuk memanggil jumlah layanan unserviceable dari fasilitas tsb (status = TRUE dan kondisi = FALSE).
    public function getJlhLayananUnserv()
    {
        return $this->getLayanan()->where('status', TRUE)->where('kondisi', FALSE)->count();
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
