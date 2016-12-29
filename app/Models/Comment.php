<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    protected $fillable = [
        'comment','user_id', 'post_id','parent_id',
    ];

    public function post()
    {
        return $this->belongsTo('App\Models\Post', 'post_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

}
