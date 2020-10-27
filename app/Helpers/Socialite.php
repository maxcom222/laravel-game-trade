<?php

namespace App\Helpers;

/**
 * Class Socialite
 * @package App\Helpers\Frontend\Auth
 */
class Socialite {


	/**
	 * List of the accepted third party provider types to login with
	 *
	 * @return array
	 */
	public function getAcceptedProviders() {
		return [
			'facebook',
			'google',
			'twitter',
      'twitch',
      'steam',
      'battlenet',
		];
	}
}
