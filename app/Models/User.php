<?php

namespace App\Models;

use Wiledia\Backport\Traits\AdminBuilder;
use Wiledia\Backport\Auth\Database\HasPermissions;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Notifications\Auth\UserNeedsPasswordReset;
use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

use Cache;

class User extends Model implements AuthenticatableContract
{
    use Notifiable;
    use Messagable;
    use SoftDeletes;


    use AdminBuilder, HasPermissions, Authenticatable;

    protected $dates = ['last_activity_at','created_at','deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','status','confirmed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserNeedsPasswordReset($token));
    }


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */


    public function listings()
    {
        return $this->hasMany('App\Models\Listing')->orderBy('last_offer_at', 'desc');
    }

    public function offers()
    {
        return $this->hasMany('App\Models\Offer')->orderBy('created_at', 'desc');
    }

    public function ratings()
    {
        return $this->hasMany('App\Models\User_Rating', 'user_id_to')->where('active','1')->orderBy('created_at', 'desc');
    }

    public function location()
    {
        return $this->hasOne('App\Models\User_Location');
    }

    public function providers()
    {
        return $this->hasMany('App\Models\SocialLogin');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    /*
    |
    | Get Square Avatar
    |
    */
    public function getAvatarSquareAttribute()
    {
        if (!is_null($this->avatar)) {
            return asset('images/avatar_square/' . $this->avatar);
        } else {
            return asset('images/avatar_square/no_avatar.jpg');
        }
    }

    /*
    |
    | Get Square (Tiny) Avatar
    |
    */
    public function getAvatarSquareTinyAttribute()
    {
        if (!is_null($this->avatar)) {
            return asset('images/avatar_square_tiny/' . $this->avatar);
        } else {
            return asset('images/avatar_square_tiny/no_avatar.jpg');
        }
    }

    /*
    |
    | Get positive ratings in percent
    |
    */
    public function getPositivePercentRatingsAttribute()
    {
        if ($this->ratings->count() > 0) {
            return round(($this->ratings->sum('rating') / ($this->ratings->count()*2)) * 100);
        } else {
            return null;
        }
    }

    /*
    |
    | Get count of positive ratings
    |
    */
    public function getPositiveRatingsAttribute()
    {
        return $this->ratings->where('rating', '2')->where('active','1')->count();
    }

    /*
    |
    | Get count of neutral ratings
    |
    */
    public function getNeutralRatingsAttribute()
    {
        return $this->ratings->where('rating', '1')->where('active','1')->count();
    }

    /*
    |
    | Get count of negative ratings
    |
    */
    public function getNegativeRatingsAttribute()
    {
        return $this->ratings->where('rating', '0')->where('active','1')->count();
    }

    /*
    |
    | Get url to profile
    |
    */
    public function getUrlAttribute()
    {
        return url('user/' . $this->name);
    }

    /*
    |
    | Check if user is Online
    |
    */
    public function isOnline()
    {
        return Cache::has('user-is-online-' . $this->id);
    }

    /*
    |
    | Save last acitivity (fire every 5 minutes)
    |
    */
    public function lastActivity($timestamp)
    {
        $this->last_activity_at = $timestamp;
        $this->timestamps = false;
        $this->save();
        $this->timestamps = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->status == 1;
    }

    /**
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->confirmed == 1;
    }

    /**
     * @param $provider
     * @return bool
     */
    public function hasProvider($provider)
    {
        foreach ($this->providers as $p) {
            if ($p->provider == $provider) {
                return true;
            }
        }

        return false;
    }

    /*
     |
     | Get users wishlist
     |
     */
     public function wishlists()
     {
         $wishlists = Cache::rememberForever('wishlist_' . $this->id, function () {
             return \App\Models\Wishlist::where('user_id', $this->id)->get(['game_id']);
         });

         return $wishlists;
     }

    /*
     |
     | Get user player ID's for OneSignal Push Notifications
     |
     */
     public function routeNotificationForOneSignal()
     {
          return \DB::table('user_player_ids')->where('user_id', $this->id)->pluck('player_id')->toArray();
     }

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
        if ($this->fresh()->isOnline()) {
            return '<div class="user-block">
					<img class="img-circle" src="' . $this->fresh()->avatar_square_tiny . '" alt="User Image">
					<span class="username"><a href="' . $this->fresh()->url .'" target="_blank">' . $this->fresh()->name . '</a></span>
					<span class="description"><i class="fa fa-circle text-success"></i> Online</span>
				</div>';
        } else {
            return '<div class="user-block">
						<img class="img-circle" src="' . $this->fresh()->avatar_square_tiny . '" alt="User Image">
						<span class="username"><a href="' . $this->fresh()->url .'" target="_blank">' . $this->fresh()->name . '</a></span>
						<span class="description"><i class="fa fa-circle text-danger"></i> Offline</span>
					</div>';
        }
    }



    /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles() : BelongsToMany
    {
        $pivotTable = config('backport.database.role_users_table');

        $relatedModel = config('backport.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id');
    }

    /**
     * A User has and belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions() : BelongsToMany
    {
        $pivotTable = config('backport.database.user_permissions_table');

        $relatedModel = config('backport.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'permission_id');
    }
}
