<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 
use Config;

class User_Rating extends Model
{
     

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'user_ratings';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['active'];
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

    public function user_from()
    {
        return $this->belongsTo('App\Models\User', 'user_id_from', 'id');
    }

    public function user_to()
    {
        return $this->belongsTo('App\Models\User', 'user_id_to', 'id');
    }

    public function offer()
    {
        return $this->belongsTo('App\Models\Offer')->withTrashed();
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
    | Get user for backend
    |
    */
    public function getUserAdmin()
    {
        if ($this->fresh()->user_from->isOnline()) {
            return '<div class="user-block">
					<img class="img-circle" src="' . $this->fresh()->user_from->avatar_square_tiny . '" alt="User Image">
					<span class="username"><a href="' . $this->fresh()->user_from->url .'" target="_blank">' . $this->fresh()->user_from->name . '</a></span>
					<span class="description"><i class="fa fa-circle text-success"></i> Online</span>
				</div>';
        } else {
            return '<div class="user-block">
						<img class="img-circle" src="' . $this->fresh()->user_from->avatar_square_tiny . '" alt="User Image">
						<span class="username"><a href="' . $this->fresh()->user_from->url .'" target="_blank">' . $this->fresh()->user_from->name . '</a></span>
						<span class="description"><i class="fa fa-circle text-danger"></i> Offline</span>
					</div>';
        }
    }

    /*
    |
    | Get user for backend
    |
    */
    public function getUserToAdmin()
    {
        if ($this->fresh()->user_to->isOnline()) {
            return '<div class="user-block">
					<img class="img-circle" src="' . $this->fresh()->user_to->avatar_square_tiny . '" alt="User Image">
					<span class="username"><a href="' . $this->fresh()->user_to->url .'" target="_blank">' . $this->fresh()->user_to->name . '</a></span>
					<span class="description"><i class="fa fa-circle text-success"></i> Online</span>
				</div>';
        } else {
            return '<div class="user-block">
						<img class="img-circle" src="' . $this->fresh()->user_to->avatar_square_tiny . '" alt="User Image">
						<span class="username"><a href="' . $this->fresh()->user_to->url .'" target="_blank">' . $this->fresh()->user_to->name . '</a></span>
						<span class="description"><i class="fa fa-circle text-danger"></i> Offline</span>
					</div>';
        }
    }

    /*
    |
    | Show rating in backend
    |
    */
    public function getRatingAdmin()
    {
        switch ($this->fresh()->rating) {
            case 0:
                return '<h3 style="margin: 0px !important;"><span class="label label-danger"><i class="fa fa-thumbs-down"></i></span></h3>';
            case 1:
                return '<h3 style="margin: 0px !important;"><span class="label label-default"><i class="fa fa-minus"></i></span></h3>';
            case 2:
                return '<h3 style="margin: 0px !important;"><span class="label label-success"><i class="fa fa-thumbs-up"></i></span></h3>';
        }
    }

    /*
    |
    | Get formatted creation date for backend
    |
    */
    public function getDateAdmin()
    {
        return '<strong>' . $this->fresh()->created_at->format(Config::get('settings.date_format')) . '</strong><br>' . $this->fresh()->created_at->format(Config::get('settings.time_format'));
    }

    /*
    |
    | Get html formatted status in admin panel
    |
    */
    public function getStatusAdmin()
    {
        switch ($this->fresh()->active) {
            case 0:
                return '<span class="label label-warning"><i class="fa fa-clock-o" aria-hidden="true"></i> Pending</span>';
            case 1:
                return '<span class="label label-success"><i class="fa fa-check" aria-hidden="true"></i> Active</span>';
        }
    }
}
