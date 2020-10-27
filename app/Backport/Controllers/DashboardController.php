<?php

namespace App\Backport\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Wiledia\Backport\Controllers\Dashboard;
use Wiledia\Backport\Layout\Column;
use Wiledia\Backport\Layout\Content;
use Wiledia\Backport\Layout\Row;
use App\Models\Offer;
use App\Models\Listing;
use App\Models\User_Rating;
use App\Models\Game;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Payment;
use App\Charts\WidgetSmall;
use App\Charts\General;

class DashboardController extends Controller
{
    public function index(Content $content)
    {
        $listings = Listing::count();
        $offers = Offer::count();
        $games = Game::count();
        $users = User::count();

        $last_7_days  = collect([]);
        for ($days = 6; $days >= 0; $days--) {
            $last_7_days->push(\Carbon\Carbon::now()->subDay($days)->toDateString());
        }

        $listings_last_7_days = collect([]);
        for ($days = 6; $days >= 0; $days--) {
            $listings_last_7_days->push(Listing::whereBetween('created_at', [\Carbon\Carbon::today()->startOfDay()->subDays($days)->toDateTimeString(), \Carbon\Carbon::today()->endOfDay()->subDays($days)->toDateTimeString()])->count());
        }

        $offers_last_7_days = collect([]);
        for ($days = 6; $days >= 0; $days--) {
            $offers_last_7_days->push(Offer::whereBetween('created_at', [\Carbon\Carbon::today()->startOfDay()->subDays($days)->toDateTimeString(), \Carbon\Carbon::today()->endOfDay()->subDays($days)->toDateTimeString()])->count());
        }

        $games_last_7_days = collect([]);
        for ($days = 6; $days >= 0; $days--) {
            $games_last_7_days->push(Game::whereBetween('created_at', [\Carbon\Carbon::today()->startOfDay()->subDays($days)->toDateTimeString(), \Carbon\Carbon::today()->endOfDay()->subDays($days)->toDateTimeString()])->count());
        }

        $users_last_7_days = collect([]);
        for ($days = 6; $days >= 0; $days--) {
            $users_last_7_days->push(User::whereBetween('created_at', [\Carbon\Carbon::today()->startOfDay()->subDays($days)->toDateTimeString(), \Carbon\Carbon::today()->endOfDay()->subDays($days)->toDateTimeString()])->count());
        }

        $this->data['listings_top'] = new WidgetSmall;
        $this->data['listings_top']->labels($last_7_days);
        $this->data['listings_top']->dataset('Listings', 'line', $listings_last_7_days)->options([
            'backgroundColor' => 'rgba(255,255,255,0.1)',
            'borderColor' => 'rgba(255,255,255,0.5)',
            'label' => 'Listings'
        ]);

        $this->data['offers_top'] = new WidgetSmall;
        $this->data['offers_top']->labels($last_7_days);
        $this->data['offers_top']->dataset('Offers', 'line', $offers_last_7_days)->options([
            'backgroundColor' => 'rgba(255,255,255,0.1)',
            'borderColor' => 'rgba(255,255,255,0.5)',
            'label' => 'Offers'
        ]);

        $this->data['games_top'] = new WidgetSmall;
        $this->data['games_top']->labels($last_7_days);
        $this->data['games_top']->dataset('Games', 'line', $games_last_7_days)->options([
            'backgroundColor' => 'rgba(255,255,255,0.1)',
            'borderColor' => 'rgba(255,255,255,0.5)',
            'label' => 'Games'
        ]);

        $this->data['users_top'] = new WidgetSmall;
        $this->data['users_top']->labels($last_7_days);
        $this->data['users_top']->dataset('Users', 'line', $users_last_7_days)->options([
            'backgroundColor' => 'rgba(255,255,255,0.1)',
            'borderColor' => 'rgba(255,255,255,0.5)',
            'label' => 'Users'
        ]);

        $this->data['general_stats'] = new General;
        $this->data['general_stats']->labels($last_7_days);
        $this->data['general_stats']->loader(false);
        $this->data['general_stats']->dataset('Listings', 'line', $listings_last_7_days)->options([
            'backgroundColor' => 'rgba(127, 184, 0, 0.1)',
            'borderColor' => 'rgba(127, 184, 0, 0.8)',
            'label' => 'Listings'
        ]);
        $this->data['general_stats']->dataset('Offers', 'line', $offers_last_7_days)->options([
            'backgroundColor' => 'rgba(57, 153, 253, 0.1)',
            'borderColor' => 'rgba(57, 153, 253, 0.8)',
            'label' => 'Offers'
        ]);


        $this->data['users'] = $users; // get users
        $this->data['users_last'] = User::orderBy('created_at', 'desc')->take(10)->get(); // get users
        $this->data['listings'] = $listings; // get listings
        $this->data['listings_last'] = Listing::orderBy('created_at', 'desc')->take(5)->get(); // get listings
        $this->data['offers'] = $offers; // get offers
        $this->data['games'] = $games; // get games
        $this->data['transactions'] = Transaction::where('type','fee')->sum('total'); // get transactions
        $this->data['transactions_last'] = Transaction::where('created_at', '>=', \Carbon\Carbon::now()->subWeek())->where('type','fee')->sum('total'); // get transactions from the last 7 days
        $this->data['payments'] = Payment::where('status','1')->count(); // get payments
        $this->data['payments_last'] = Payment::where('status','1')->where('created_at', '>=', \Carbon\Carbon::now()->subWeek())->count(); // get payments from the last 7 days
        $this->data['payments_sum'] = Payment::where('status','1')->sum('total'); // get payments
        $this->data['payments_last_sum'] = Payment::where('status','1')->where('created_at', '>=', \Carbon\Carbon::now()->subWeek())->sum('total'); // get payments from the last 7 days
        $this->data['payments_sum_fee'] = Payment::where('status','1')->sum('transaction_fee'); // get payments
        $this->data['payments_last_sum_fee'] = Payment::where('status','1')->where('created_at', '>=', \Carbon\Carbon::now()->subWeek())->sum('transaction_fee'); // get payments from the last 7 days

        // Install security check
        $this->data['security'] = substr(sprintf('%o', fileperms(base_path('.env'))), -4) >= '0755' || substr(sprintf('%o', fileperms(base_path('config/app.php'))), -4) >= '0755';
        // Check if giantbomb id is set
        $this->data['giantbomb'] = strlen(config('settings.giantbomb_key')) <= 0;

        return $content
            ->header('Dashboard')
            ->body(view('backend.dashboard', $this->data));
    }

    public function checkUpdate(Request $request)
    {
        if (!$request->ajax()) {
            abort('404');
        }
        $check_version = array('ip' => isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '', 'hostname' => isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : ''  ,'domain' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '', 'email' => auth()->user()->email);

        $this->data['version_response'] = self::checkVersion($check_version, 'https://www.wiledia.com/gameport/version');

        return view('backend.dashboard_update', $this->data);
    }

    /**
     * checkVersion()
     *
     * @param mixed $_p
     * @param mixed $remote_url
     * @return
     */
    public function checkVersion($_p, $remote_url)
    {
    	$remote_url = trim($remote_url);

    	$is_https = (substr($remote_url, 0, 5) == 'https');

    	$fields_string = http_build_query($_p);

    	if(function_exists('curl_init')) {

    		$ch = curl_init();

    		curl_setopt($ch, CURLOPT_URL, $remote_url);

    		if($is_https && extension_loaded('openssl')) {
    			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    		}

    		curl_setopt($ch, CURLOPT_POST, 1);
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    		curl_setopt($ch, CURLOPT_HEADER, false);

    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    		$response = curl_exec($ch);

            return $response;

    		curl_close($ch);

    	} else {

    		$context_options = array (
    			'http' => array (
    				'method' => 'POST',
    				'header' => "Content-type: application/x-www-form-urlencoded\r\n".
    							"Content-Length: ".strlen($fields_string)."\r\n",
    				'content' => $fields_string
    			 )
    		 );


            try {

                $context = stream_context_create($context_options);
                $fp = fopen($remote_url, 'r', false, $context);

         		$response = @stream_get_contents($fp);

            } catch(\Exception $e) {
                return false;
            }

    	}
    	return $response;
    }
}
