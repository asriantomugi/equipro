<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HistoriLayananPeralatan extends Model
{
    use HasFactory;

    protected $table = 'histori_layanan_peralatan'; // nama tabel
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
     * Function untuk memanggil layanan.
     */
    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    /**
     * Function untuk memanggil data peralatan.
     */
    public function peralatan()
    {
        return $this->hasOne(Peralatan::class, 'id', 'peralatan_id');
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
