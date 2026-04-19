<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TlGangguanPeralatan extends Model
{
    use HasFactory;

    protected $table = 'tl_gangguan_peralatan'; // nama tabel
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
     * Function untuk memanggil gangguan peralatan dari tl gangguan peralatan.
     */
    public function GangguanPeralatan()
    {
        return $this->belongsTo(GangguanPeralatann::class);
    }


    /**
     * Function untuk memanggil laporan dari tl gangguan peralatan.
     */
    public function laporan()
    {
        return $this->belongsTo(Laporan::class);
    }

    /**
     * Function untuk memanggil layanan dari tl gangguan peralatan.
     */
    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    /**
     * Function untuk memanggil peralatan dari tl gangguan peralatan.
     */
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class);
    }

    /**
     * Relasi ke tindaklanjut gangguan peralatan
     */
    public function tlPenggantianPeralatan()
    {
        return $this->hasOne(TlPenggantianPeralatan::class, 'tl_gangguan_id');
    }

   /**
     * Function untuk mendapatkan waktu open dalam format yang diinginkan
     * function: waktu_mulai_formatted
     */
    public function getWaktuMulaiFormattedAttribute()
    {
        return $this->waktu_mulai ? Carbon::parse($this->waktu_mulai)->format('d/m/Y') : null;
    }

    /**
     * Function untuk mendapatkan waktu close dalam format yang diinginkan
     * unction: waktu_selesai_formatted
     */
    public function getWaktuSelesaiFormattedAttribute()
    {
        return $this->waktu_selesai ? Carbon::parse($this->waktu_selesai)->format('d/m/Y') : null;
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
