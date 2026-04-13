<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Peralatan extends Model
{
    use HasFactory;

    protected $table = 'peralatan'; // nama tabel
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
     * Function untuk memanggil nama jenis alat.
     */
    public function jenis()
    {
        return $this->hasOne(JenisAlat::class, 'id', 'jenis_id');
    }
    
    /**
     * Function untuk memanggil nama perusahaan.
     */
    public function perusahaan()
    {
        return $this->hasOne(Perusahaan::class, 'id', 'perusahaan_id');
    }

    /**
     * Function untuk memanggil nama sewa (ASET/SEWA).
     */
    public function sewa()
    {
        if ($this->sewa === 1) return 'SEWA';
        if ($this->sewa === 0) return 'ASET';
        return '-';
    }

    /**
     * Function untuk memanggil kondisi.
     */
    public function kondisi()
    {
        if ($this->kondisi === 0) return 'RUSAK';
        if ($this->kondisi === 1) return 'NORMAL';
        if ($this->kondisi === 2) return 'NORMAL SEBAGIAN';
        return '-';
    }

    /**
     * Function untuk memanggil object gangguan peralatan.
     */
    public function gangguanPeralatan()
    {
        return $this->hasMany(GangguanPeralatan::class, 'peralatan_id');
    }

    /**
     * Function untuk memanggil kondisi gangguan peralatan.
     */
    public function kondisiGangguan($laporan_id)
    {
        $gangguan = $this->gangguanPeralatan()
            ->where('laporan_id', $laporan_id)
            ->first();

        return $gangguan->kondisi ?? null;
    }

    /**
     * Function untuk memanggil object tindaklanjut gangguan peralatan.
     */
    public function tlGangguanPeralatan()
    {
        return $this->hasMany(TlGangguanPeralatan::class, 'peralatan_id');
    }

    /**
     * Function untuk memanggil kondisi tindaklanjut gangguan peralatan.
     */
    public function kondisiTlGangguan($laporan_id)
    {
        $tlGangguan = $this->tlGangguanPeralatan()
            ->where('laporan_id', $laporan_id)
            ->orderBy('created_at', 'desc') // urut berdasarkan created_at DESC
            ->first(); 

        return $tlGangguan->kondisi ?? null;
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
