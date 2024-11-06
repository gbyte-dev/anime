<?php

// app/Models/Anime.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animes extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    	'mal_id',
    	'titles',
    	'slug',
    	'image_url'
    ];
}
