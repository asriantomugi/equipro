<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan'; // nama tabel
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
     * Function untuk memanggil layanan dari laporan.
     */
    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    /**
     * Relasi ke data penggantian peralatan (jika ada)
     */
    public function penggantian()
    {
        return $this->hasOne(TlPenggantianPeralatan::class, 'laporan_id');
    }

    public function gangguanPeralatan()
    {
        return $this->hasMany(TlGangguanPeralatan::class, 'laporan_id');
    }

    public function gangguanNonPeralatan()
    {
        return $this->hasOne(TlGangguanNonPeralatan::class, 'laporan_id');
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
