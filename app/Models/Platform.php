<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 

class Platform extends Model
{
     

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'platforms';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name','color','description','acronym','cover_position'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function games()
    {
        return $this->hasMany('App\Models\Game');
    }

    public function gamesCount()
    {
        return $this->hasOne('App\Models\Game')
            ->selectRaw('platform_id, count(*) as aggregate')
            ->groupBy('platform_id');
    }

    public function digitals()
    {
        return $this->belongsToMany('App\Models\Digital');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |
    | Get URL
    |
    */
    public function getUrlAttribute()
    {
        return url('listings/' . str_slug($this->acronym));
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    /*
    |
    | Helper Class for count games
    |
    */
    public function getGamesCountAttribute()
    {
        // if relation is not loaded already, let's do it first
        if (! array_key_exists('gamesCount', $this->relations)) {
            $this->load('gamesCount');
        }

        $related = $this->getRelation('gamesCount');

        // then return the count directly
        return ($related) ? (int) $related->aggregate : 0;
    }

    /*
    |
    | Get Listings count and cheapest listing for backend
    |
    */
    public function getGamesAdmin()
    {
        if ($this->getGamesCountAttribute() > 0) {
            return '<span class="label label-success">' . $this->getGamesCountAttribute() .'</span>';
        } else {
            return '<span class="label label-danger">0</span>';
        }
    }
}
