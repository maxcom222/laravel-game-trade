<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Config;

class Transaction extends Model
{


     /*
	|--------------------------------------------------------------------------
	| GLOBAL VARIABLES
	|--------------------------------------------------------------------------
	*/

    protected $table = 'transactions';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $dates = ['created_at'];

    /*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

  public function item()
  {
      return $this->morphTo();
  }

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
      return $this->hasOne('App\Models\Offer', 'id', 'item_id');
  }

  public function withdrawal()
  {
      return $this->hasOne('App\Models\Withdrawal', 'id', 'item_id');
  }

  public function payer()
  {
      return $this->hasOne('App\Models\User', 'id', 'payer_id');
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
  | Show html formatted status in admin panel
  |
  */
  public function getStatusAdmin()
  {
      switch ($this->status) {
          case 0:
              return '<span class="label label-warning">Refunded</span>';
          case 1:
              return '<span class="label label-success">Complete</span>';
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
  | Show html formatted transaction type in admin panel
  |
  */
  public function getTypeAdmin()
  {
      switch ($this->type) {
          case 'sale':
              return '<span class="label label-success">Sale</span>';
          case 'withdrawal':
              return '<span class="label label-warning">Withdrawal</span>';
          case 'purchase':
              return '<span class="label label-primary">Purchase</span>';
          case 'refund':
              return '<span class="label label-info">Refund</span>';
          case 'fee':
              return '<span class="label label-danger">Fee</span>';
      }
  }

  /*
  |
  | Amount
  |
  */
  public function getAmountAdmin()
  {
      return '<div class="block">
            <strong class="' . ($this->fresh()->type == 'sale' ? 'text-success' : ($this->fresh()->type == 'purchase' ? 'text-primary' : ($this->fresh()->type == 'refund' ? 'text-info' : 'text-danger'))) . '" style="font-size: 18px;">' . number_format($this->fresh()->total,2) . ' '. $this->fresh()->currency .' </strong> <br />
            </strong>
      </div>';
  }

  /*
  |
  | Get transaction details for backend
  |
  */
  public function getItemAdmin()
  {
      if ($this->fresh()->type == 'sale' || $this->fresh()->type == 'fee' || $this->fresh()->type == 'refund' || $this->fresh()->type == 'purchase') {
          return '<div class="user-block">
              <img class="img-circle" src="' . $this->offer->listing->game->image_square_tiny . '" alt="User Image">
              <span class="username"><a href="' . $this->offer->url .'" target="_blank">' . $this->offer->listing->game->name . '</a></span>
              <span class="description"><span class="label" style="background-color: '. $this->offer->listing->game->platform->color . '; margin-right: 10px;">' . $this->offer->listing->game->platform->name .'</span><i class="fa fa-calendar"></i> ' . $this->offer->listing->game->release_date->format('Y') . '</span>
          </div>';
      }
      if ($this->fresh()->type == 'withdrawal') {
          // get withdrawal status
          $withdrawal_status;
          switch ($this->withdrawal->status) {
              case 0:
                  $withdrawal_status = '<span class="label label-danger">Declined</span>';
                  break;
              case 1:
                  $withdrawal_status = '<span class="label label-warning">Pending</span>';
                  break;
              case 2:
                  $withdrawal_status = '<span class="label label-success">Complete</span>';
                  break;
          }

          return '<div">
            Payment method:  <strong>' . ucfirst($this->withdrawal->payment_method) . '</strong> <br />
            Details:  <strong>' . $this->withdrawal->payment_details . '</strong> <br />
            ' . $withdrawal_status . '
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
