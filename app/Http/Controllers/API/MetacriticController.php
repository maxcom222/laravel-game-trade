<?php
namespace App\Http\Controllers\API;

use Request;
use Validator;
use App\Exceptions\BadRequest;
use App\Exceptions\BadResponseException;

class MetacriticController
{
    /**
		 * Clean a string of markup tabs, newlines, multiple spaces...
		 *
		 * @param string $string
		 * @return string
		 */
		protected function clean($string)
		{
		    return trim(preg_replace(["/\n/", "/([[:blank:]]+)/"], ['', ' '], $string));
		}

    protected function loadMarkup($url, $params = null, $retry_count = 4, $ch = null)
    {
        if ($retry_count > 4) {
            $retry_count = 4;
        }

        if (stripos($url, '%-')) {
            $url = str_replace('%-', '-', $url);
        }

        if (is_array($params) and count($params) > 0) {
            // Build the query (only once)
            $url .= (stripos($url, '?') > 0 ? '&' : '?') . http_build_query($params);
        }

        if (! $ch) {
            $ch = curl_init($url);

            libxml_use_internal_errors(true);

            curl_setopt_array($ch, array(
                //CURLOPT_FAILONERROR => TRUE,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FRESH_CONNECT => true,
                //CURLOPT_HEADER => TRUE,
                CURLOPT_HTTPHEADER => array('Cache-Control: no-cache'),
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12',
                CURLOPT_FOLLOWLOCATION => true
            ));
        }

        $markup = curl_exec($ch);

        $error = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));

		    // Check for flood control protection
		    if ($error === 429) {
		        if ($retry_count > 0) {
		            Log::info("Got 429 for $url, re-trying...");

		            sleep(5 - $retry_count);

		            return $this->loadMarkup($url, null, --$retry_count, $ch);
		        } else {
		            curl_close($ch);

		            Log::info("Giving up with 429 on $url.");

		            throw new BadResponseException('metacritic.com bot protection in effect. Please slow down (add a pause between requests)!', 504);
		        }
		    }

        if ($markup) {
            \phpQuery::newDocumentHTML($markup);

            if (count(pq('.error_type'))) {
                throw new BadResponseException('Metacritic Error: '. pq('.error_code')->text() .' - '. pq('.error_type')->text(), pq('.error_code')->text());
            }

            curl_close($ch);

            return;
        }

        // Empty response... probably an error...
        $redirected_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        Log::info("Got $error for $url" . ($redirected_url ? " (redirect: $redirected_url)" : ''));

        curl_close($ch);

        throw new BadResponseException('metacritic.com request failed! HTTP Error Code: '. $error, 502);
    }


    private $_game_platforms = array(

        'pc'        => [3,        'PC'],
        'ios'        => [9,        'iPhone/iPad'],
        'dreamcast' => [15,        'Dreamcast'],

        // Playstation
        'ps'        => [10,        'PlayStation'],
        'ps2'        => [6,        'PlayStation 2'],
        'ps3'        => [1,        'PlayStation 3'],
        'ps4'        => [72496,    'PlayStation 4'],
        'psp'        => [7,        'PSP'],
        'vita'        => [67365,    'PlayStation Vita'],

        // Microsoft
        'xbox'        => [12,        'Xbox'],
        'xbox360'    => [2,        'Xbox 360'],
        'xboxone'    => [80000,    'Xbox One'],


        // Nintendo
        'gba'        => [11,        'Game Boy Advance'],
        'ds'        => [4,        'DS'],
        '3ds'        => [16,        '3DS'],
        'gamecube'    => [13,        'GameCube'],
        'n64'        => [14,        'Nintendo 64'],
        'wii'        => [8,        'Wii'],
        'wii-u'        => [68410,    'Wii U'],
        'switch'        => [268409,    'Switch']
    );


    public function search($type)
    {
        $validator = Validator::make(
            array(
                'type'        => $type,
                'title'        => Request::get('title'),
                'platform'    => Request::get('platform'),
                'year_from'    => Request::get('year_from'),
                'year_to'    => Request::get('year_to'),
                'max_pages'    => Request::get('max_pages'),
                'retry'        => Request::get('retry'),
            ),
            array(
                'type'        => 'required|in:game,movie,album,tv',
                'title'        => 'required',
                'platform'    => 'in:'. implode(',', array_keys($this->_game_platforms)),
                'year_from'    => 'integer|nullable|min:1800|max:3000',
                'year_to'    => 'integer|nullable|min:1800|max:3000',
                'max_pages'    => 'integer|nullable|min:1|max:5',
                'retry'        => 'integer|nullable|min:0|max:4',
            )
        );

        if ($validator->fails()) {
            $msg = implode(' ', $validator->messages()->all());

            throw new BadRequest($msg);
        }

        $url = ("http://www.metacritic.com/search/$type/". $this->safeTitle(Request::get('title')) .'/results');

        $params = array();

        if (Request::get('platform')) {
            $params['plats['. $this->_game_platforms[Request::get('platform')][0] .']'] = 1;
        }

        if (Request::get('year_from')) {
            $params['date_range_from'] = '01-01-'. intval(Request::get('year_from'));
        }

        if (Request::get('year_to')) {
            $params['date_range_to'] = '12-31-'. intval(Request::get('year_to'));
        }

        if (count($params) > 0) {
            $params['search_type'] = 'advanced';
        }

        $this->loadMarkup($url, $params, Request::get('retry', 4));

        $lis = pq('ul.search_results li.result');

        $results = $this->extractSearchResults($lis, $type);

        $max_pages = Request::get('max_pages', 1);

        // Did we request additional pages?
        if ($max_pages > 1) {
            $pagelinks = pq('ul.pages a.page_num');

            $cnt = 1; // We already got one page
            foreach ($pagelinks as $a) {
                // Have we had enough?
                if ($cnt == $max_pages) {
                    break;
                }

                $cnt++;

                $href = $this->assertHostname(pq($a)->attr('href'));

                if ($href) {
                    $this->loadMarkup($href, $params, Request::get('retry', 4));

                    $lis = pq('ul.search_results li.result');

                    $results = array_merge($results, $this->extractSearchResults($lis, $type));
                }
            }
        }

        $response = array(
            'max_pages' => $max_pages,
            'count'        => count($results),
            'results'    => $results
        );

        return response()->json($response);
    }

    private function extractSearchResults($lis, $type = 'game')
    {
        $results = array();

        foreach ($lis as $li) {
            $item = array();

            // Essential data
            // -----------------------------------------------------------------
            $item['name'] = $this->clean(pq('h3.product_title a', $li)->text());

            // URL
            // -----------------------------------------------------------------
            $item['url'] = $this->assertHostname(pq('h3.product_title a', $li)->attr('href'));

            // Release date
            // -----------------------------------------------------------------
            $date = $this->clean(pq('.more_stats .release_date .data', $li)->text());

            if ($date) {
                if (preg_match('/[a-z]{3} \d{1,2}, \d{4}/i', $date)) {
                    $item['rlsdate'] = $date ? date('Y-m-d', strtotime($date)) : null;
                } elseif (preg_match('/\d{4}/', $date)) {
                    $item['rlsdate'] = intval($date) .'-01-01';
                }
            }

            // Age rating
            // -----------------------------------------------------------------
            $rating = pq('.more_stats .maturity_rating .data', $li)->text();

            if ($rating) {
                $item['rating'] = $rating;
            }

            // Publisher
            // -----------------------------------------------------------------
            $publisher = pq('.more_stats .publisher .data', $li)->text();

            if ($publisher) {
                $item['publisher'] = $publisher;
            }

            switch ($type) {

                // Games
                // -------------------------------------------------------------
                case 'game':
                    $item['score'] = pq('span.metascore_w', $li)->text();
                    $item['summary'] = $this->clean(pq('p.deck', $li)->text());
                    $item['platform'] = pq('span.platform', $li)->text();
                    break;

                case 'game_list':
                    $item['score'] = pq('span.metascore_w', $li)->text();
                    $thumb = pq('img', $li)->attr('src');

                    if (stripos($thumb, '/53w-game.jpg') === false) {
                        $item['thumbnail'] = $thumb;
                    }

                    break;

                // Movies
                // -------------------------------------------------------------
                case 'movie':
                    $item['score'] = pq('span.metascore_w', $li)->text();
                    $item['summary'] = $this->clean(pq('p.deck', $li)->text());
                    $item['rating'] = trim(pq('.stat.rating .data', $li)->text());
                    $item['cast'] = $this->clean(pq('.stat.cast .data', $li)->text());
                    $item['genre'] = $this->clean(pq('.stat.genre .data', $li)->text());
                    $item['avguserscore'] = trim(pq('.stat.product_avguserscore .data', $li)->text());
                    $item['runtime'] = trim(pq('.stat.runtime .data', $li)->text());
                    break;

                case 'movie_list':
                    $item['score'] = pq('span.metascore_w', $li)->text();
                    $item['rating'] = trim(pq('.stat.rating .data', $li)->text());
                    $item['cast'] = $this->clean(pq('.stat.cast .data', $li)->text());
                    $item['genre'] = $this->clean(pq('.stat.genre .data', $li)->text());
                    $item['avguserscore'] = trim(pq('.stat.product_avguserscore .data', $li)->text());
                    $item['runtime'] = trim(pq('.stat.runtime .data', $li)->text());

                    $thumb = pq('img', $li)->attr('src');

                    if (stripos($thumb, '/53w-game.jpg') === false) {
                        $item['thumbnail'] = $thumb;
                    }
                    break;

                // Music albums
                // -------------------------------------------------------------
                case 'album':
                    $item['score'] = pq('span.metascore_w', $li)->text();
                    $item['summary'] = $this->clean(pq('p.deck', $li)->text());
                    break;

                // TV
                // -------------------------------------------------------------
                case 'tv':
                    $item['genre'] = $this->clean(pq('.stat.genre .data', $li)->text());
            }

            $results[] = $item;
        }

        return $results;
    }

    public function find($type)
    {
        $rules = array(
            'title'        => 'required',
            'type'        => 'required|in:game,movie,album,tv',
            'retry'        => 'integer|nullable|min:0|max:4'
        );

        // Additional type rules
        switch ($type) {
            case 'game':
                $rules['platform'] = 'required|in:'. implode(',', array_keys($this->_game_platforms));
                break;

            case 'album':
                $rules['artist'] = 'required';
        }

        $validator = Validator::make(
            array(
                'title'        => Request::get('title'),
                'type'        => $type,
                'retry'        => Request::get('retry'),
                'platform'    => Request::get('platform'),
                'artist'    => Request::get('artist')
            ),
            $rules
        );

        if ($validator->fails()) {
            $msg = implode(' ', $validator->messages()->all());

            throw new BadRequest($msg);
        }

        $url = ("http://www.metacritic.com/search/$type/". $this->safeTitle(Request::get('title')) .'/results');

        $query_params = array();

        if (Request::get('platform')) {
            $query_params['plats['. $this->_game_platforms[Request::get('platform')][0] .']'] = 1;
            $query_params['search_type'] = 'advanced';
        }

        $this->loadMarkup($url, $query_params, Request::get('retry', 4));

        $lis = pq('ul.search_results li.result');

        $mc_url = $this->findResult($lis, Request::get('title') . (Request::get('artist') ? ' - '. Request::get('artist')  : ''));

        // No luck on the first page?
        if (! $mc_url) {
            $pagelinks = pq('ul.pages a.page_num');

            $cnt = 1; // We already processed one page
            $max_pages = 2; // How many should we process in total?

            foreach ($pagelinks as $a) {

                // Have we had enough?
                if ($cnt == $max_pages) {
                    break;
                }

                $cnt++;

                $href = pq($a)->attr('href');

                if (mb_substr($href, 0, 1) === '/') {
                    $href = 'http://www.metacritic.com'. $href;
                }

                if ($href) {
                    $this->loadMarkup($href, null, Request::get('retry', 4));

                    $lis = pq('ul.search_results li.result');

                    $mc_url = $this->findResult($lis, Request::get('title'));

                    if ($mc_url) {
                        // We found it
                        break;
                    }
                }
            }
        }

        if ($mc_url) {
            $response['result'] = $this->extractDetails($mc_url);
        } else {
            $response['result'] = false;
        }

        return response()->json($response);
    }

    /**
     * Go through each results line item (li) and compare the title.
     * If a match is found, return it's URL.
     *
     * @param \phpQuery $lis
     * @param string $title
     * @return boolean
     */
    private function findResult($lis, $title)
    {
        $title = $this->normalizeTitle($title);

        foreach ($lis as $li) {
            $a_title = $this->normalizeTitle(pq('h3.product_title a', $li)->text());

            if (strcmp($title, $a_title) === 0) {
                //if ($a_title == $title) {
                // We found a match
                return pq('h3.product_title a', $li)->attr('href');
            }
        }

        return false;
    }

    private function extractDetails($url)
    {
        if (preg_match('/\/movie\//', $url)) {
            $type = 'movie';
        } elseif (preg_match('/\/music\//', $url)) {
            $type = 'album';
        } elseif (preg_match('/\/tv\//', $url)) {
            $type = 'tv';
        } else {
            // Default
            $type = 'game';
        }

        $url = $this->assertHostname($url);

        $this->loadMarkup($url, null, Request::get('retry', 4));

        $details['name'] = $this->clean(trim(pq('.product_title > a')->text()));
        $details['score'] = trim(pq('.product_data_summary .metascore_w span[itemprop="ratingValue"]')->text());

        // Genre
        foreach (pq('.summary_detail.product_genre .data') as $g) {
            $details['genre'][] = $this->clean(pq($g)->text());
        }

        $details['thumbnail'] = trim(pq('img.product_image.large_image')->attr('src'));
        $userscore = trim(pq('.feature_userscore .metascore_anchor')->text());
        $details['userscore'] = is_numeric($userscore) ? floatval($userscore) : null;
        $details['summary'] = $this->clean(pq('.summary_detail.product_summary .blurb.blurb_expanded')->text());

        switch ($type) {
            case 'game':
                $details['platform'] = trim(pq('.product_title .platform')->text());
                $details['publisher'] = trim(pq('.summary_detail.publisher a')->text());
                $details['developer'] = trim(pq('.summary_detail.developer .data')->text());
                $details['rating'] = trim(pq('.summary_detail.product_rating:first .data')->text());
                // Release date
                $date = pq('.summary_detail.release_data .data')->text();
                $details['rlsdate'] = $date ? date('Y-m-d', strtotime($date)) : null;
                break;

            case 'movie':
                // Runtime can be fetched, but it seems like an error in their markup, so this might break some day
                $details['runtime'] = trim(pq('.summary_detail.product_rating:last .data')->text());
                $details['director'] = $this->clean(pq('*[itemprop=director] *[itemprop=name]')->text());
                $details['cast'] = $this->clean(pq('.summary_detail.product_credits .data')->text());
                $details['rating'] = trim(pq('.summary_detail.product_rating:first .data')->text());
                // Release date
                $date = pq('.product_data *[itemprop=datePublished]')->text();
                $details['rlsdate'] = $date ? date('Y-m-d', strtotime($date)) : null;
                break;

            case 'album':
                $details['name'] = $this->clean(trim(pq('.product_title *[itemprop=name]')->text()));
                $details['artist'] = $this->clean(pq('*[itemprop=byArtist] *[itemprop=name]')->text());
                $details['rating'] = trim(pq('.summary_detail.product_rating:first .data')->text());
                // Release date
                $date = pq('.product_data *[itemprop=datePublished]')->text();
                $details['rlsdate'] = $date ? date('Y-m-d', strtotime($date)) : null;
                break;

            case 'tv':
                $details['series_name'] = $this->clean(trim(pq('*[itemprop=partOfTVSeries] *[itemprop=name]')->text()));
                // Series premiere date
                $series_date = pq('.content_head .product_data .release_data .data')->text();
                $details['series_premiere_date'] = $series_date ? date('Y-m-d', strtotime($series_date)) : null;

                // Season premiere date
                $season_date = pq('*[itemprop=startDate]')->text();
                $details['season_premiere_date'] = $season_date ? date('Y-m-d', strtotime($season_date)) : null;

                foreach (pq('*[itemprop=creator] *[itemprop=name]') as $c) {
                    $details['creator'][] = $this->clean(pq($c)->text());
                }

                $details['season_count'] = count(pq('.product_seasons a')) + 1; // Add 1 for the one currently being viewed
        }

        $details['url'] = 'http://www.metacritic.com'. pq('.product_title a')->attr('href');

        return $details;
    }

    private function normalizeTitle($title)
    {
        return trim(preg_replace(array("/[^[:alnum:][:space:]]/ui", "/[[:blank:]]+/"), array('', ' '), mb_strtolower($title)));
    }

    /**
     * Make a title (keywords) safe for search (as a query parameter).
     *
     * @param string $title
     * @return string
     */
    private function safeTitle($title)
    {
        // Remove any dashes or colons that are not followed by a space
        $str = preg_replace('/(-|:(!?\b)|\?)/', ' ', $title);

        // Replace any / characters with an empty space
        $str = str_replace('/', ' ', $title);

        return urlencode($str);
    }

    private function assertHostname($url)
    {
        if (stripos($url, 'http://www.metacritic.com/') !== 0) {
            $url = 'http://www.metacritic.com/'. trim($url, ' /');
        }

        return $url;
    }

    public function details()
    {
        if (! Request::input('url')) {
            throw new BadRequest('Missing URL parameter');
        }

        $response['result'] = $this->extractDetails(Request::input('url'));

        return response()->json($response);
    }

    public function reviews()
    {
        $validator = Validator::make(
            array(
                'url'        => Request::get('url'),
                'order_by'    => Request::get('order_by'),
                'retry'        => Request::get('retry')
            ),
            array(
                'url'        => 'required|url',
                'order_by'    => 'in:critics-score,most-active,publication,most-clicked',
                'retry'        => 'integer|min:1|max:4'
            )
        );

        if ($validator->fails()) {
            $msg = implode(' ', $validator->messages()->all());

            throw new BadRequest($msg);
        }

        $params = array();

        if (Request::get('order_by') and Request::get('order_by') !== 'critics-score') {
            $params['sort-by'] = Request::get('order_by');
        }

        $media_url = $this->assertHostname(Request::get('url'));
        $url =  $media_url .'/critic-reviews';

        try {
            $this->loadMarkup($url, $params, Request::get('retry', 4));

            $response['results'] = $this->extractReviews($url);
        } catch (BadResponseException $ex) {
            if ($ex->getCode() === 404) {
                // Try fixing the URL - this will follow redirects
                try {
                    $details = $this->extractDetails($media_url);

                    // Was there a redirect
                    if ($details['url'] !== $media_url) {
                        $this->loadMarkup($details['url'] .'/critic-reviews', $params, Request::get('retry', 4));

                        $response['redirected_to'] = $details['url'];
                        $response['results'] = $this->extractReviews($url);
                    }
                } catch (\Exception $ex) {
                    throw new BadRequest('Error while fetching reviews from URL - bad URL?');
                }
            } else {
                throw new BadRequest('Error while fetching reviews from URL - bad URL?');
            }
        }

        $response['count'] = count($response['results']);

        return response()->json($response);
    }

    public function userReviews()
    {
        $validator = Validator::make(
            array(
                'url'        => Request::get('url'),
                'order_by'    => Request::get('order_by'),
                'retry'        => Request::get('retry')
            ),
            array(
                'url'        => 'required|url',
                'order_by'    => 'in:score,most-active,date,most-helpful',
                'retry'        => 'integer|min:1|max:4'
            )
        );

        if ($validator->fails()) {
            $msg = implode(' ', $validator->messages()->all());

            throw new BadRequest($msg);
        }

        $url = $this->assertHostname(Request::get('url')) .'/user-reviews';

        $rq_page_count = Request::get('page_count', 1);

        if (is_numeric($rq_page_count)) {
            $rq_page_count = intval($rq_page_count);

            if ($rq_page_count < 1 or $rq_page_count > 100) {
                $rq_page_count = 1;
            }
        } elseif ($rq_page_count === 'all') {
            $rq_page_count = 100;
        }

        $params = array(
            'sort-by' => Request::get('order_by', 'most-helpful')
        );

        $this->loadMarkup($url, $params, Request::get('retry', 4));

        // Scrape the totals
        $response['ratings_total'] = intval(pq('.score_summary .count strong')->text());

        $response['ratings_positive'] = intval(str_replace(',', '', pq('ol.score_counts li:nth-child(1) .count')->text()));
        $response['ratings_mixed'] = intval(str_replace(',', '', pq('ol.score_counts li:nth-child(2) .count')->text()));
        $response['ratings_negative'] = intval(str_replace(',', '', pq('ol.score_counts li:nth-child(3) .count')->text()));

        $response['results'] = $this->extractUserReviews();

        if (count(pq('.page_nav')) === 1) {
            $pages = intval(pq('.page_nav ul.pages li.last_page a')->text());

            // Should we scrape additional pages?
            if ($rq_page_count >= 2 and $pages >= 2) {
                for ($i = 2; $i <= $rq_page_count and $i <= $pages; $i++) {
                    $params['page'] = $i - 1;

                    $this->loadMarkup($url, $params, Request::get('retry', 4));

                    $response['results'] = array_merge($response['results'], $this->extractUserReviews());

                    if ($i >= 5) {
                        sleep(2);
                    }
                }
            }
        }

        $response['count'] = count($response['results']);

        return response()->json($response);
    }

    public function gameList($platform, $type)
    {
        $validator = Validator::make(
            array(
                'platform'    => $platform,
                'type'        => $type,
                'page'        => Request::get('page'),
                'order_by'    => Request::get('order_by'),
                'retry'        => Request::get('retry'),
            ),
            array(
                'platform'    => 'in:'. implode(',', array_keys($this->_game_platforms)),
                'type'        => 'required|in:coming-soon,new-releases,all',
                'page'        => 'integer|min:1',
                'order_by'    => 'in:date,metascore,name'. ($type === 'coming-soon' ? '' : ',userscore'),
                'retry'        => 'integer|min:0|max:4',
            )
        );

        if ($validator->fails()) {
            throw new BadRequest(implode(' ', $validator->messages()->all()));
        }

        $rq_page = (int)Request::get('page', 1);

        if ($rq_page > 1) {
            $params['page'] = $rq_page - 1;
        }

        $params['view'] = 'detailed';
        $order_by = Request::get('order_by', 'date');

        $url = ('http://www.metacritic.com/browse/games/release-date/'. ($type == 'all' ? 'available' : $type) .'/'. $platform .'/'. $order_by);

        $this->loadMarkup($url, $params, Request::get('retry', 4));

        $response = array();

        $this->parsePager($response);

        switch ($type) {
            case 'coming-soon':
            case 'new-releases':
            case 'all':
                $lis = pq('#main ol.list_products li.product');

                $response['results'] = $this->extractSearchResults($lis, 'game_list');
        }

        return response()->json($response);
    }

    public function movieList($type)
    {
        $validator = Validator::make(
            array(
                'type'        => $type,
                'order_by'    => Request::get('order_by'),
                'retry'        => Request::get('retry')
            ),
            array(
                'type'    => 'required|in:coming-soon,new-releases',
                'order_by'    => 'in:date,metascore,name,userscore',
                'retry' => 'integer|min:0|max:4'
            )
        );

        if ($validator->fails()) {
            throw new BadRequest(implode(' ', $validator->messages()->all()));
        }

        if ($type === 'new-releases') {
            $type = 'theaters';
        }

        switch ($type) {
            case 'theaters':

                $url = 'http://www.metacritic.com/browse/movies/release-date/'. $type .'/'. Request::get('order_by', 'date') .'?view=detailed';

                $this->loadMarkup($url, null, Request::get('retry', 4));

                $lis = pq('#main ol.list_products li.product');

                $response['results'] = $this->extractSearchResults($lis, 'movie_list');
                break;

            case 'coming-soon':

                $response['results'] = array();

                $url = 'http://www.metacritic.com/browse/movies/release-date/'. $type .'/date?view=detailed';

                $this->loadMarkup($url, null, Request::get('retry', 4));

                $this->extractUpcomingMovies($response['results']);

                $next = pq('a.action[rel=next]');
                $i = 0;

                while ($next && $i++ < 5) {
                    $url = $this->assertHostname($next->attr('href'));

                    $this->loadMarkup($url, null, Request::get('retry', 4));

                    $this->extractUpcomingMovies($response['results']);

                    $next = pq('a.action[rel=next]');
                }
        }

        return response()->json($response);
    }

    private function extractUpcomingMovies(&$result)
    {
        foreach (pq('.date_group_module') as $group) {
            $date = strip_tags(pq('.module_title', $group)->html());// .' '. date('Y');
            $rlsdate = date('Y-m-d', strtotime($date));
            $lis = pq('.list_products .product', $group);

            foreach ($lis as $li) {
                $item = array();

                $item['name'] = trim(pq('.product_title a', $li)->text());
                $item['score'] = trim(pq('span.metascore_w', $li)->text());
                $item['url'] = $this->assertHostname(pq('.product_title a', $li)->attr('href'));
                $item['rlsdate'] = $rlsdate;
                $item['summary'] = $this->clean(preg_replace('/(<a.*<\/a>)/i', '', pq('.deck', $li)->html()));
                $item['rating'] = trim(pq('.rating .data', $li)->text());
                $item['cast'] = $this->clean(pq('.stat.cast .data', $li)->text());
                $item['thumbnail'] = trim(pq('.product_image img', $li)->attr('src'));

                $result[] = $item;
            }
        }
    }

    public function albumList($type)
    {
        $validator = Validator::make(
            array(
                'type'        => $type,
                'order_by'    => Request::get('order_by'),
                'retry'        => Request::get('retry')
            ),
            array(
                'type'        => 'in:coming-soon,new-releases',
                'order_by'    => 'in:date,metascore,name,userscore',
                'retry'        => 'integer|min:0|max:4'
            )
        );

        if ($validator->fails()) {
            throw new BadRequest(implode(' ', $validator->messages()->all()));
        }

        $response['results'] = array();

        switch ($type) {
            case 'new-releases':

                $order_by = Request::get('order_by', 'date');

                $url = 'http://www.metacritic.com/browse/albums/release-date/'. $type .'/'. $order_by .'?view=detailed';

                $this->loadMarkup($url, null, Request::get('retry', 4));

                $lis = pq('#main ol.list_products li.product');

                foreach ($lis as $li) {
                    $item['author'] = $this->clean(trim(pq('h3.product_title .product_artist', $li)->text(), ' -'));
                    $item['name'] = pq('h3.product_title a', $li)->text();
                    $item['url'] = $this->assertHostname(pq('h3.product_title a', $li)->attr('href'));

                    $date = $this->clean(pq('.more_stats .release_date .data', $li)->text());

                    if ($date) {
                        if (preg_match('/[a-z]{3} \d{1,2}, \d{4}/i', $date)) {
                            $item['rlsdate'] = $date ? date('Y-m-d', strtotime($date)) : null;
                        } elseif (preg_match('/\d{4}/', $date)) {
                            $item['rlsdate'] = intval($date) .'-01-01';
                        }
                    }

                    if ($order_by === 'userscore') { // Special layout is used for this order_by option
                        $item['score'] = pq('span.textscore', $li)->text();
                        $item['avguserscore'] = pq('span.metascore_w', $li)->text();
                        $item['genre'] = $this->clean(pq('.stat.genre .data', $li)->text());
                    } else {
                        $item['score'] = pq('span.metascore_w', $li)->text();
                        $item['avguserscore'] = trim(pq('.stat.product_avguserscore .data', $li)->text());
                        $item['genre'] = $this->clean(pq('.stat.genre .data', $li)->text());
                    }

                    $item['thumbnail'] = trim(pq('.product_image img', $li)->attr('src'));
                    //$item['image'] = str_replace('-53.jpg', '.jpg', $item['thumbnail']);

                    $response['results'][] = $item;
                }
                break;

            case 'coming-soon':

                $url = 'http://www.metacritic.com/browse/albums/release-date/coming-soon/date';

                $this->loadMarkup($url, null, Request::get('retry', 4));

                foreach (pq('.musicTable tr') as $row) {
                    if (pq('th', $row)->length() === 1) {
                        $date = date('Y-m-d', strtotime($this->clean(pq('th', $row)->text())));
                        continue;
                    }

                    $item['rlsdate'] = $date;
                    $item['author'] = $this->clean(pq('.artistName', $row)->text());
                    $item['name'] = $this->clean(pq('.albumTitle', $row)->text());
                    $item['comment'] = trim($this->clean(pq('.dataComment', $row)->text()), ' []');

                    $response['results'][] = $item;
                }
        }

        return response()->json($response);
    }

    private function extractReviews()
    {
        $results = array();

        foreach (pq('.critic_review') as $src_review) {
            $review = array(
                'critic' => $this->clean(pq('.review_critic .source', $src_review)->text()),
                'score'    => trim(pq('.review_grade', $src_review)->text()),
                'excerpt' => $this->clean(pq('.review_body', $src_review)->text())
            );

            $date = trim(pq('.review_critic .date', $src_review)->text());

            if ($date) {
                $review['date'] = date('Y-m-d', strtotime($date));
            }

            $link = trim(pq('.review_actions .full_review a', $src_review)->attr('href'));

            if ($link) {
                $review['link'] = $link;
            }

            $author = $this->clean(pq('.review_critic .author a', $src_review)->text());

            if ($author) {
                $review['author'] = $author;
            }

            $results[] = $review;
        }

        return $results;
    }

    private function extractUserReviews()
    {
        $result = array();

        foreach (pq('.user_review') as $src_review) {
            $review = array(
                'name' => $this->clean(pq('.review_critic .name', $src_review)->text()),
                'active' => pq('.review_critic .name a', $src_review)->length === 1,
                'score'    => trim(pq('.review_grade', $src_review)->text())
            );

            $date = trim(pq('.review_critic .date', $src_review)->text());

            if ($date) {
                $review['date'] = date('Y-m-d', strtotime($date));
            }

            $review['total_ups'] = pq('.total_ups', $src_review)->text();
            $review['total_thumbs'] = pq('.total_thumbs', $src_review)->text();

            if (count(pq('.blurb', $src_review)) > 0) {
                $review['review'] = $this->clean(pq('.blurb_collapsed', $src_review)->text() . pq('.blurb_expanded', $src_review)->text());
            } else {
                $review['review'] = $this->clean(pq('.review_body', $src_review)->text());
            }

            $result[] = $review;
        }

        return $result;
    }

    public function typeDescription($type)
    {
        $validator = Validator::make(
            array(
                'type'    => $type,
                'retry' => Request::input('retry')
            ),
            array(
                'type'    => 'required|in:game,movie,album,tv',
                'retry' => 'integer|min:0|max:4'
            )
        );

        if ($validator->fails()) {
            throw new BadRequest(implode(' ', $validator->messages()->all()));
        }

        $url = ('http://www.metacritic.com/advanced-search/'. $type);

        $this->loadMarkup($url, null, Request::get('retry', 4));

        $cbs = pq('.search_genres_checkbox');

        foreach ($cbs as $cb) {
            $name = pq('input', $cb)->attr('name');
            $matches = array();
            preg_match('/\[([a-z\-0-9]*)\]/i', $name, $matches);
            $response['genres'][end($matches)] = pq('.label_text', $cb)->text();
        }

        switch ($type) {
            case 'game':

                $response['platforms'] = array();

                foreach ($this->_game_platforms as $key => $arr) {
                    $response['platforms'][$key] = $arr[1];
                }
                break;

            case 'tv':
                $cbs = pq('.search_show_types_checkbox');

                foreach ($cbs as $cb) {
                    $name = pq('input', $cb)->attr('name');
                    $matches = array();
                    preg_match('/\[([a-z\-0-9]*)\]/i', $name, $matches);
                    $response['show_type'][end($matches)] = pq('.label_text', $cb)->text();
                }
        }

        return response()->json($response);
    }

    public function userDetails($username)
    {
        $validator = Validator::make(
            array(
                'username'    => $username,
                'retry'        => Request::get('retry')
            ),
            array(
                'username'    => 'required',
                'retry'        => 'integer|min:1|max:4'
            )
        );

        if ($validator->fails()) {
            $msg = implode(' ', $validator->messages()->all());

            throw new BadRequest($msg);
        }

        $this->loadMarkup('http://www.metacritic.com/user/'. $username, null, Request::get('retry'));

        $data = array();

        foreach (pq('.user_totals .total_summary') as $total) {
            $label = strtolower(pq('.label', $total)->text());
            $value = (int)pq('.data', $total)->text();

            if ($label === 'reviews') {
                $data['reviews']['total'] = $value;
            } else {
                $data[$label] = $value;
            }
        }

        foreach (pq('.head_type_1 .tabs .tab') as $tab) {
            $txt = $this->clean(pq($tab)->text());
            $matches = array();

            if (preg_match('/^([a-z]*) \((\d*)\)$/i', $txt, $matches)) {
                $data['reviews'][strtolower($matches[1])] = (int)$matches[2];
            }
        }

        return response()->json(array(
            'result' => $data
        ));
    }

    public function userReviewList($username, $type)
    {
        $validator = Validator::make(
            array(
                'username'    => $username,
                'type'        => $type,
                'order_by'    => Request::get('order_by'),
                'page'        => Request::get('page'),
                'retry'        => Request::get('retry')
            ),
            array(
                'username'    => 'required',
                'type'        => 'required|in:movie,tv,album,game',
                'order_by'    => 'in:date,helpful,score,metascore,userscore',
                'page'        => 'integer|min:1',
                'retry'        => 'integer|min:1|max:4'
            )
        );

        if ($validator->fails()) {
            $msg = implode(' ', $validator->messages()->all());

            throw new BadRequest($msg);
        }

        $params = array();

        switch ($type) {
            case 'tv':
                $params['myscore-filter'] = 'TvShow';
                break;
            default:
                $params['myscore-filter'] = ucfirst($type);
        }

        if (Request::get('order_by')) {
            $params['myreview-sort'] = Request::get('order_by');
        }

        $rq_page = (int)Request::get('page', 1);

        if ($rq_page > 1) {
            $params['page'] = $rq_page - 1;
        }

        $this->loadMarkup('http://www.metacritic.com/user/'. $username, $params, Request::get('retry'));

        $response = array();

        if (count(pq('.page_nav ul.pages')) === 1) {
            if (count(pq('.page_nav .last_page a')) === 1) {
                $response['total_pages'] = (int)$this->clean(pq('.page_nav .last_page a')->text());
                $response['next_page'] = $rq_page + 1;
            } elseif (count(pq('.page_nav .last_page.active_page')) === 1) {
                $response['total_pages'] = (int)$this->clean(pq('.page_nav .last_page span')->text());
            }
        } else {
            $response['total_pages'] = 1;
        }

        if ($rq_page === 1) {
            $response['distribution'] = array(
                'positive' => (int)pq('.score_distribution ol .count:first')->text(),
                'mixed' => (int)pq('.score_distribution ol li:nth-child(2) .count')->text(),
                'negative' => (int)pq('.score_distribution ol .count:last')->text()
            );
            $response['average'] = (float)pq('.review_average .summary_data')->text();
        }

        $response['results'] = array();

        foreach (pq('.user_reviews .user_review') as $r) {
            $pq_blurb = pq('.blurb_expanded', $r);

            if (count($pq_blurb) === 0) {
                $pq_blurb = pq('.review_body span', $r);
            }

            $response['results'][] = array(
                'title' => $this->clean(pq('.product_title', $r)->text()),
                'score' => (int)pq('.metascore_w', $r)->text(),
                'date' => $this->convertDate(pq('.date', $r)->text()),
                'text' => $this->clean($pq_blurb->text())
            );
        }

        return response()->json($response);
    }

    private function convertDate($date_string)
    {
        $date = $this->clean($date_string);

        if (preg_match('/[a-z]{3} \d{1,2}, \d{4}/i', $date)) {
            return date('Y-m-d', strtotime($date));
        } elseif (preg_match('/\d{4}/', $date)) {
            return intval($date) .'-01-01';
        }

        return null;
    }

    private function parsePager(&$response)
    {
        if (count(pq('.page_nav ul.pages')) === 1) {
            if (count(pq('.page_nav .last_page a')) === 1) {
                $response['total_pages'] = (int)$this->clean(pq('.page_nav .last_page a')->text());
                $response['next_page'] = (int)$this->clean(pq('.page_nav .active_page .page_num')->text()) + 1;
            } elseif (count(pq('.page_nav .last_page.active_page')) === 1) {
                $response['total_pages'] = (int)$this->clean(pq('.page_nav .last_page span')->text());
            }
        } else {
            $response['total_pages'] = 1;
        }
    }
}
