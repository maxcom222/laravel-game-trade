<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 
use Config;

class Report extends Model
{
     

     /*
    	|--------------------------------------------------------------------------
    	| GLOBAL VARIABLES
    	|--------------------------------------------------------------------------
    	*/

    protected $table = 'offer_reports';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $dates = ['closed_at'];

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

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function offer()
    {
        return $this->belongsTo('App\Models\Offer');
    }

    public function staff()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_staff');
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
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ADMIN FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |
    | Get html formatted status in admin panel
    |
    */
    public function getStatusAdmin()
    {
        switch ($this->status) {
            case 0:
                return '<span class="label label-danger">Open</span>';
            case 1:
                return '<span class="label label-success">Closed</span>';
        }
    }

    /*
    |
    | Get user for backend
    |
    */
    public function getUserAdmin()
    {
        if ($this->user->isOnline()) {
            return '<div class="user-block">
					<img class="img-circle" src="' . $this->user->avatar_square_tiny . '" alt="User Image">
					<span class="username"><a href="' . $this->user->url .'" target="_blank">' . $this->user->name . '</a></span>
					<span class="description"><i class="fa fa-circle text-success"></i> Online</span>
				</div>';
        } else {
            return '<div class="user-block">
						<img class="img-circle" src="' . $this->user->avatar_square_tiny . '" alt="User Image">
						<span class="username"><a href="' . $this->user->url .'" target="_blank">' . $this->user->name . '</a></span>
						<span class="description"><i class="fa fa-circle text-danger"></i> Offline</span>
					</div>';
        }
    }

    /*
    |
    | Get formatted creation date for backend
    |
    */
    public function getDateAdmin()
    {
        return '<strong>' . $this->created_at->format(Config::get('settings.date_format')) . '</strong><br>' . $this->created_at->format(Config::get('settings.time_format'));
    }

}
