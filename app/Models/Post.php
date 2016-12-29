<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Post extends Model
{
    //
    use Sluggable;

    /**
     * Sluggable configuration.
     *
     * @var array
     * @return array
     */
    public function sluggable() {
        return [
            'slug' => [
                'source'    => 'title',
                'separator' => '-',
                'maxLength' => 180,
                'onUpdate'  => true,
            ]
        ];
    }

    protected $fillable = [
        'title','content', 'user_id','slug',
    ];

    public function comments()
    {
        return $this->hasMany('App\Models\Comment','post_id','id');
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
