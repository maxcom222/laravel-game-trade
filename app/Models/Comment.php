<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
use Config;

class Comment extends Model
{
     use  SoftDeletes;

     /*
	|--------------------------------------------------------------------------
	| GLOBAL VARIABLES
	|--------------------------------------------------------------------------
	*/

    protected $table = 'comments';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $dates = ['deleted_at','created_at'];

    /*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

  /**
   * Get all of the owning commentable models.
   */
  public function commentable()
  {
      return $this->morphTo();
  }

    /*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/

  public function dblikes()
  {
      return $this->hasMany('App\Models\CommentLike');
  }

  public function user()
  {
      return $this->belongsTo('App\Models\User');
  }

  public function game()
  {
      return $this->hasOne('App\Models\Game', 'id', 'commentable_id')->withTrashed();
  }

  public function listing()
  {
      return $this->hasOne('App\Models\Listing', 'id', 'commentable_id')->withTrashed();
  }

  public function article()
  {
      return $this->hasOne('App\Models\Article', 'id', 'commentable_id');
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
  | Get type name for link (used for ajax request)
  |
  */
  public function getTypeAttribute()
  {
      switch ($this->commentable_type) {
          case 'App\Models\Game':
              return 'game';
          case 'App\Models\Listing':
              return 'listing';
          case 'App\Models\Article':
              return 'article';
      }
  }

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
      if ($this->user->isOnline()) {
          return '<div class="user-block">
        <img class="img-circle" src="' . $this->user->avatar_square_tiny . '" alt="User Image">
        <span class="username"><a href="' . $this->user->url .'" target="_blank">' . $this->user->name . '</a></span>
        <span class="description"><i class="fa fa-circle text-success"></i> User ID: <strong>' . $this->user->id . '</strong></span>
      </div>';
      } else {
          return '<div class="user-block">
          <img class="img-circle" src="' . $this->user->avatar_square_tiny . '" alt="User Image">
          <span class="username"><a href="' . $this->user->url .'" target="_blank">' . $this->user->name . '</a></span>
          <span class="description inline-block"><i class="fa fa-circle text-danger"></i> User ID: <strong>' . $this->user->id . '</strong></span>
        </div>';
      }
  }

  /*
  |
  | Get transaction details for backend
  |
  */
  public function getItemAdmin()
  {
      if ($this->fresh()->type == 'game' ) {
          if (isset($this->fresh()->game)) {
              return '<div class="user-block">
                  <img class="img-circle" src="' . $this->fresh()->game->image_square_tiny . '" alt="User Image">
                  <span class="username"><i class="fa fa-gamepad"></i> <a href="' . $this->fresh()->game->url_slug .'#!comments" target="_blank">' . $this->fresh()->game->name . '</a></span>
                  <span class="description"><span class="label" style="background-color: '. $this->fresh()->game->platform->color . '; margin-right: 10px;">' . $this->fresh()->game->platform->name .'</span><i class="fa fa-calendar"></i> ' . $this->fresh()->game->release_date->format('Y') . '</span>
              </div>';
          } else {
              return '<div class="user-block text-danger text-bold">
                  <i class="fa fa-ban"></i> Removed
              </div>';
          }
      }
      if ($this->fresh()->type == 'listing' ) {
          if (isset($this->fresh()->listing)) {
              return '<div class="user-block">
                  <img class="img-circle" src="' . $this->fresh()->listing->game->image_square_tiny . '" alt="User Image">
                  <span class="username"><i class="fa fa-tag"></i> <a href="' . $this->fresh()->listing->url_slug .'#!comments" target="_blank">' . $this->fresh()->listing->game->name . '</a></span>
                  <span class="description"><span class="label" style="background-color: '. $this->fresh()->listing->game->platform->color . '; margin-right: 10px;">' . $this->fresh()->listing->game->platform->name .'</span><i class="fa fa-calendar"></i> ' . $this->fresh()->listing->game->release_date->format('Y') . '</span>
              </div>';
          } else {
              return '<div class="user-block text-danger text-bold">
                  <i class="fa fa-ban"></i> Removed
              </div>';
          }
      }
      if ($this->fresh()->type == 'article' ) {
          if (isset($this->fresh()->article)) {
              return '<div class="user-block">
                  <img class="img-circle" src="' . $this->fresh()->article->image_square_tiny . '" alt="User Image">
                  <span class="username"><i class="fa fa-newspaper-o"></i> <a href="' . $this->fresh()->article->url_slug .'#!comments" target="_blank">' . $this->fresh()->article->title . '</a></span>
                  <span class="description"><i class="fa fa-calendar"></i> ' . $this->fresh()->article->created_at->format(Config::get('settings.date_format')) . '</span>
              </div>';
          } else {
              return '<div class="user-block text-danger text-bold">
                  <i class="fa fa-ban"></i> Removed
              </div>';
          }
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
