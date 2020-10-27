<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
use Config;

class Offer extends Model
{
     use  SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'offers';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $dates = ['deleted_at'];

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

    public function listing()
    {
        return $this->belongsTo('App\Models\Listing')->withTrashed();
    }

    public function game()
    {
        return $this->hasOne('App\Models\Game', 'id', 'trade_game')->withTrashed();
    }

    public function thread()
    {
        return $this->hasOne('Cmgmyr\Messenger\Models\Thread');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function report()
    {
        return $this->hasOne('App\Models\Report');
    }

    public function payment()
    {
        return $this->hasOne('App\Models\Payment','item_id','id')->where('item_type', 'App\Models\Offer');
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
    |
    | Format price attribute to given currency from settings.
    |
    */
    public function getPriceOfferFormattedAttribute()
    {
        return money($this->price_offer ? $this->price_offer : '0', Config::get('settings.currency'));
    }

    /*
    |
    | Convert formatted price attribute to int before saving to database.
    |
    */
    public function setPriceOfferAttribute($value)
    {
        $this->attributes['price_offer'] = abs(filter_var($value, FILTER_SANITIZE_NUMBER_INT));
    }

    /*
    |
    | Get URL
    |
    */
    public function getUrlAttribute()
    {
        return url('offer/' . $this->id);
    }

    /*
    |
    | Get Report status
    |
    */
    public function getReportedAttribute()
    {
        return isset($this->report) ? $this->report->count() : null;
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |
    | Show html formatted status in admin panel
    |
    */
    public function getStatusAdmin()
    {
        if (!$this->fresh()->declined) {
            switch ($this->fresh()->status) {
                case 0:
                    return '<span class="label label-warning">Wait</span>';
                case 1:
                    return '<span class="label label-primary">Accepted</span>';
                case 2:
                    return '<span class="label label-default">Complete</span>';
            }
        } else {
            return '<span class="label label-danger">Declined</span>';
        }
    }

    /*
    |
    | Get user for backend
    |
    */
    public function getUserAdmin()
    {
        if ($this->fresh()->user->isOnline()) {
            return '<div class="user-block">
					<img class="img-circle" src="' . $this->fresh()->user->avatar_square_tiny . '" alt="User Image">
					<span class="username"><a href="' . $this->fresh()->user->url .'" target="_blank">' . $this->fresh()->user->name . '</a></span>
					<span class="description"><i class="fa fa-circle text-success"></i> Online</span>
				</div>';
        } else {
            return '<div class="user-block">
						<img class="img-circle" src="' . $this->fresh()->user->avatar_square_tiny . '" alt="User Image">
						<span class="username"><a href="' . $this->fresh()->user->url .'" target="_blank">' . $this->fresh()->user->name . '</a></span>
						<span class="description"><i class="fa fa-circle text-danger"></i> Offline</span>
					</div>';
        }
    }

    /*
    |
    | Get user to for backend
    |
    */
    public function getUserToAdmin()
    {
        if ($this->fresh()->listing->user->isOnline()) {
            return '<div class="user-block">
					<img class="img-circle" src="' . $this->fresh()->listing->user->avatar_square_tiny . '" alt="User Image">
					<span class="username"><a href="' . $this->fresh()->listing->user->url .'" target="_blank">' . $this->fresh()->listing->user->name . '</a></span>
					<span class="description"><i class="fa fa-circle text-success"></i> Online</span>
				</div>';
        } else {
            return '<div class="user-block">
						<img class="img-circle" src="' . $this->fresh()->listing->user->avatar_square_tiny . '" alt="User Image">
						<span class="username"><a href="' . $this->fresh()->listing->user->url .'" target="_blank">' . $this->fresh()->listing->user->name . '</a></span>
						<span class="description"><i class="fa fa-circle text-danger"></i> Offline</span>
					</div>';
        }
    }

    /*
    |
    | Get Name with cover and release year for backend
    |
    */
    public function getGameAdmin()
    {
        return '<div class="user-block">
					<img class="img-circle" src="' . $this->fresh()->listing->game->image_square_tiny . '" alt="User Image">
					<span class="username"><a href="' . $this->fresh()->listing->url_slug .'" target="_blank">' . $this->fresh()->listing->game->name . '</a></span>
					<span class="description"><span class="label" style="background-color: '. $this->fresh()->listing->game->platform->color . '; margin-right: 10px;">' . $this->fresh()->listing->game->platform->name .'</span><i class="fa fa-calendar"></i> ' . $this->fresh()->listing->game->release_date->format('Y') . '</span>
				</div>';
    }

    /*
    |
    | Get Name with cover and release year for backend
    |
    */
    public function getOfferAdmin()
    {
        if ($this->fresh()->game) {
            return '<div class="user-block">
						<img class="img-circle" src="' . $this->fresh()->game->image_square_tiny . '" alt="User Image">
						<span class="username"><a href="' . $this->fresh()->url_slug .'" target="_blank">' . $this->fresh()->game->name . '</a></span>
						<span class="description"><span class="label" style="background-color: '. $this->fresh()->game->platform->color . '; margin-right: 10px;">' . $this->fresh()->game->platform->name .'</span><i class="fa fa-calendar"></i> ' . $this->fresh()->game->release_date->format('Y') . '</span>
					</div>';
        } else {
            return '<h4 style="margin: 0px !important;"><span class="label label-success">' . $this->fresh()->getPriceOfferFormattedAttribute() .'</span></h4>';
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
}
