<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Popular extends Model
{

    protected $table = 'popular';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'link', 'image', 'title', 'snippet'
    ];

}
