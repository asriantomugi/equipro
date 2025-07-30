<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';
    protected $guarded = ['id'];
}

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan';
    protected $guarded = ['id'];
}

class GangguanPeralatan extends Model
{
    use HasFactory;

    protected $table = 'gangguan_peralatan';
    protected $guarded = ['id'];
}

class GangguanNonPeralatan extends Model
{
    use HasFactory;

    protected $table = 'gangguan_non_peralatan';
    protected $guarded = ['id'];
}
