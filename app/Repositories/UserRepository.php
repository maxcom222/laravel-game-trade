<?php
namespace App\Repositories;

use App\Models\User;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Hash;
use App\Models\SocialLogin;
use App\Models\User_Location;
use App\Events\Frontend\Auth\UserConfirmed;
use App\Notifications\Auth\UserNeedsConfirmation;

/**
 * Class UserRepository
 * @package App\Repositories\Frontend\User
 */
class UserRepository extends Repository
{
    /**
     * Associated Repository Model
     */
    const MODEL = User::class;


    /**
     * @param $email
     * @return bool
     */
    public function findByEmail($email)
    {
        return $this->query()->where('email', $email)->first();
    }

    /**
     * @param $provider, $provider_id
     * @return bool
     */
    public function findByProviderId($provider, $provider_id)
    {
        $user_id = DB::table('social_logins')->where('provider', $provider)->where('provider_id', $provider_id)->value('user_id');

        return $this->query()->where('id', $user_id)->first();
    }

    /**
     * @param $token
     * @return mixed
     * @throws GeneralException
     */
    public function findByToken($token)
    {
        return $this->query()->where('confirmation_code', $token)->first();
    }

    /**
     * @param $token
     * @return mixed
     * @throws GeneralException
     */
    public function getEmailForPasswordToken($token)
    {
        $rows = DB::table(config('auth.passwords.users.table'))->get();

        foreach ($rows as $row) {
            if (password_verify($token, $row->token)) {
                return $row->email;
            }
        }

        return redirect()->route('frontend.auth.login')->withError(trans('auth.unknown'));
    }

    /**
     * @param array $data
     * @param bool $provider
     * @return static
     */
    public function create(array $data, $provider = false)
    {
        $user = self::MODEL;
        $user = new $user;

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->confirmation_code = md5(uniqid(mt_rand(), true));
        $user->status = 1;
        $user->password = $provider ? null : bcrypt($data['password']);
        $user->confirmed = $provider ? 1 : (config('settings.user_confirmation') ? 0 : 1);

        DB::transaction(function () use ($user) {
            if (parent::save($user)) {
                /**
                 * Add the default site role to the new user
                 */
            }
        });

        /**
         * If users have to confirm their email and this is not a social account,
         * send the confirmation email
         *
         * If this is a social account they are confirmed through the social provider by default
         */
         if (config('settings.user_confirmation') && $provider === false) {
             $user->notify(new UserNeedsConfirmation($user->confirmation_code));
         }

        /**
         * Return the user object
         */
        return $user;
    }

    /**
     * @param array $data
     * @param bool $provider
     * @return static
     */
    public function createInstall(array $data, $provider = false)
    {
        $user = self::MODEL;
        $user = new $user;

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->confirmation_code = md5(uniqid(mt_rand(), true));
        $user->status = 1;
        $user->password = $provider ? null : bcrypt($data['password']);
        $user->confirmed = 1;

        DB::transaction(function () use ($user) {
            if (parent::save($user)) {
                /**
                 * Add the default site role to the new user
                 */
                $user->roles()->attach(1);
            }
        });


        /**
         * Return the user object
         */
        return $user;
    }

    /**
     * @param $data
     * @param $provider
     * @return UserRepository|bool
     */
    public function findOrCreateSocial($data, $provider)
    {
        /**
         * User email may not provided.
         */
        $user_email = $data->email ? : "{$data->id}@{$provider}.com";

        /**
         * Check to see if there is a user with this email first
         */
        $user = $this->findByEmail($user_email);


        /**
         * If there is no user with the provided email address, check if a user
         * already signed up with this provider id
         */
        if (! $user) {
            $user = $this->findByProviderId($provider, $data->id);
        }


        /**
         * If the user does not exist create them
         * The true flag indicate that it is a social account
         * Which triggers the script to use some default values in the create method
         */
        if (! $user) {
            $normalizeChars = array(
                'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
                'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ə'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
                'Ï'=>'I', 'İ'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
                'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
                'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ə'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
                'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
                'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
                'ă'=>'a', 'î'=>'i', 'ı'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
            );

            // Check if user with this name already exist
            if ($provider == 'steam') {
                $user_name_social = strtr(str_replace(' ', '.', $data->nickname), $normalizeChars);
            } else {
                $user_name_social = strtr(str_replace(' ', '.', $data->name), $normalizeChars);
            }

            $check_user_name = User::where('name','like', $user_name_social . '%')->get();

            if (isset($check_user_name)) {
                if (count($check_user_name) > 0) {
                    $user_name_social .= ($check_user_name->count() + 1);
                }
            }

            $user = $this->create([
                'name'  => $user_name_social,
                'email' => $user_email,
            ], true);
        }

        /**
         * See if the user has logged in with this social account before
         */
        if (! $user->hasProvider($provider)) {
            /**
             * Gather the provider data for saving and associate it with the user
             */
            $user->providers()->save(new SocialLogin([
                'provider'    => $provider,
                'provider_id' => $data->id,
                'token'       => $data->token,
                'avatar'      => $data->avatar,
            ]));
            /**
             * Upload avatar on first login
             */
            // Image Beta
            $extension = 'jpg';
            $newfilename = time().'-'.$user->id.'.'.$extension;
            $disk = 'local';
            $destination_path = 'public/users';

            $image_client = new \GuzzleHttp\Client();
            $image = $image_client->request('GET', $data->avatar);

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$newfilename, $image->getBody()->getContents());

            $user->avatar = $newfilename;

            parent::save($user);
        } else {
            /**
             * Update the users information, token and avatar can be updated.
             */
            $user->providers()->update([
                'token'       => $data->token,
                'avatar'      => $data->avatar,
            ]);
        }

        /**
         * Return the user object
         */
        return $user;
    }

    /**
     * @param $token
     * @return bool
     * @throws GeneralException
     */
    public function confirmAccount($token)
    {
        $user = $this->findByToken($token);

        // wrong token
        if (!$user) {
            return redirect()->route('frontend.auth.login')->withError(trans('auth.confirmation.mismatch'));
        }

        // User already confirmed
        if ($user->confirmed == 1) {
            return redirect()->route('frontend.auth.login')->withError(trans('auth.confirmation.already_confirmed'));
        }

        // confirm user
        if ($user->confirmation_code == $token) {
            $user->confirmed = 1;
            event(new UserConfirmed($user));
            parent::save($user);
            return redirect()->route('frontend.auth.login')->withSuccess(trans('auth.confirmation.success'));
        }

        return redirect()->route('frontend.auth.login')->withError(trans('auth.confirmation.mismatch'));
    }

    /**
     * @param $id
     * @param $input
     * @return mixed
     * @throws GeneralException
     */
    public function updateProfile($id, $request)
    {
        $user = parent::find($id);
        //$user->name = $request['name'];

        //Address is not current address
        if ($user->email != $request['email']) {
          //Emails have to be unique
          if ($this->findByEmail($request['email'])) {
              // show a success message
              \Alert::error('<i class="fa fa-save m-r-5"></i>' . trans('users.alert.email_taken'))->flash();

              return false;
          }

          $user->email = $request['email'];
        }

        if ($request->hasFile('avatar')) {

            // Image Beta
            $extension = 'jpg';
            $newfilename = time().'-'.$user->id.'.'.$extension;
            $destination_path = "public/users";


            $img = \Image::make($request->avatar->path());
            $disk = "local";

            \Storage::disk($disk)->put($destination_path.'/'.$newfilename, $img->stream());

            // Delete old image
            if (!is_null($user->avatar)) {
                \Storage::disk($disk)->delete('/public/users/' . $user->avatar);
            }

            $user->avatar = $newfilename;
        }

        // show a success message
        \Alert::success('<i class="fa fa-save m-r-5"></i>' . trans('users.alert.profile_saved'))->flash();

        return parent::save($user);
    }

    /**
     * @param $id
     * @param $input
     * @return mixed
     * @throws GeneralException
     */
    public function updateLocation($id, $request)
    {

        if (!$request->country && !$request->address_components) {
          return false;
        }

        $user = parent::find($id);

        $user_location = User_location::where('user_id', $user->id)->first();

        if (!$user_location) {
            $user_location = new User_location;
            $user_location->user_id = $user->id;
        }

        if (config('settings.location_api') == 'zippopotam') {
            $user_location->country = $request->country;
            $user_location->country_abbreviation = $request->country_abbreviation;
            $user_location->postal_code = $request->post_code;
            $user_location->place = $request->place;
            $user_location->longitude = $request->longitude;
            $user_location->latitude = $request->latitude;
        }

        if (config('settings.location_api') == 'openstreetmap') {
            $user_location->country = $request->country;
            $user_location->country_abbreviation = strtoupper($request->countryCode);
            $user_location->postal_code = $request->postcode ? $request->postcode : '';
            $user_location->place = $request->city ? $request->city : $request->name;
            $user_location->longitude = $request->latlng['lng'];
            $user_location->latitude = $request->latlng['lat'];
        }

        if (config('settings.location_api') == 'googlemaps') {
            // Get the infos we need from the address_components (city, country, state)
            foreach ($request->address_components as $addressPart) {
                // Get city
                if ((in_array('locality', $addressPart['types'])) && (in_array('political', $addressPart['types']))) {
                    $gcity = $addressPart['long_name'];
                // Get state
                } elseif ((in_array('administrative_area_level_1', $addressPart['types'])) && (in_array('political', $addressPart['types']))) {
                    $gstate = $addressPart['long_name'];
                // Get country
                } else if ((in_array('country', $addressPart['types'])) && (in_array('political', $addressPart['types']))) {
                    $gcountry = $addressPart['long_name'];
                    $gcountry_code = $addressPart['short_name'];
                } else if ((in_array('postal_code', $addressPart['types']))) {
                    $gpostal = $addressPart['long_name'];
                } else if ((in_array('route', $addressPart['types']))) {
                    $groute = $addressPart['long_name'];
                } else if ((in_array('street_number', $addressPart['types']))) {
                    $gstreet_number = $addressPart['long_name'];
                }
            }

            $user_location->country = $gcountry;
            $user_location->country_abbreviation = strtoupper($gcountry_code);
            $user_location->postal_code = isset($gpostal) ? $gpostal : '';
            $user_location->place = isset($gcity) ? $gcity : (isset($gstate) ? $gstate : '');
            $user_location->longitude = $request->lng;
            $user_location->latitude = $request->lat;
        }

        $user_location->save();

        return true;
    }

    /**
     * @param $input
     * @return mixed
     * @throws GeneralException
     */
    public function changePassword($input)
    {
        $user = parent::find(auth()->user()->id);

        if (Hash::check($input['old_password'], $user->password)) {
            // show a success message
            \Alert::success('<i class="fa fa-save m-r-5"></i>' . trans('users.alert.password_changed'))->flash();

            $user->password = bcrypt($input['password']);
            return parent::save($user);
        }

        return false;
    }
}
