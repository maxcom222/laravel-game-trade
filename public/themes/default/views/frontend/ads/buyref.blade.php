@php $link = str_replace('%game_name%', urlencode($game->name), config('settings.buy_button_ref_link')); @endphp

<a href="{{ $link }}" target="_blank" class="buy-button flex-center-space ad m-b-10 @if(!isset($game->metacritic) || (isset($game->metacritic) && !($game->metacritic->score || $game->metacritic->userscore))) m-t-10 @endif">
  <i class="icon fa fa-shopping-basket" aria-hidden="true"></i> <span class="text">{{ trans('general.ads.buy_ref', ['merchant' => config('settings.buy_button_ref_merchant')]) }}</span><span></span>
</a>
