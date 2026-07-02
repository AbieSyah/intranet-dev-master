<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $table = 'news_event';
    protected $fillable = ['judul','tanggal_news','detail','tumbnail','gambar','link_video','lampiran','status'];
    public $timestamps = true;
}
