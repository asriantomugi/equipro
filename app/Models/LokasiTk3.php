<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LokasiTk3 extends Model
{
    use HasFactory;

    protected $table = 'lokasi_tk_3'; // nama tabel
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
     * Function untuk memanggil data lokasi tingkat I.
     */
    public function getLokasiTk1()
    {
        return $this->belongsTo(LokasiTk1::class, 'lokasi_tk_1_id', 'id');
    }

    /**
     * Function untuk memanggil data lokasi tingkat II.
     */
    public function getLokasiTk2()
    {
        return $this->belongsTo(LokasiTk2::class, 'lokasi_tk_2_id', 'id');
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
