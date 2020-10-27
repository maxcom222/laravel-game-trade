<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Listings Language Lines
    |--------------------------------------------------------------------------
    */

    'general' => [
      'new' => 'New!',
      'newest_listings' => 'Newest Listings',
      'all_listings' => 'All Listings',
      'listings' => 'Listings',
      'sell' => 'Sell',
      'trade' => 'Trade',
      'pickup' => 'Pickup',
      'delivery' => 'Delivery',
      'condition' => 'Condition',
      'digital_download' => 'Digital Download',
      'no_listings' => 'There are no listings available.',
      'no_listings_add' => 'Add first listing',
      'sold' => 'Not available',
      /* Start new strings v1.4.1 */
      'show_all' => 'Show all listings',
      /* End new strings v1.4.1 */
      'deleted' => 'Listing deleted.',
      'deleted_game' => 'Game deleted from system.',
      'no_description' => 'No description',
      'open_google_maps' => 'Open in Google Maps',
      /* 5 Condition Levels - 1 is worst -> 5 is best */
      'conditions' => [
          '5' => 'Brand New',
          '4' => 'Like New',
          '3' => 'Good',
          '2' => 'Acceptable',
          '1' => 'Disc Only',
          '0' => 'Digital Download Code',
      ],
    ],

    'overview' => [
      'created' => 'Created',
      'trade_info' => 'Please select the game you want trade for :Game_name.',
      'subheader' => [
          'buy_now' => 'Buy Now',
          'go_gameoverview' => 'Go to Gameoverview',
          'details' => 'Details',
          'media' => 'Images & Videos',
      ],
    ],

    'form' => [
      'edit' => 'Edit Listing',
      'add' => 'Add Listing',
      'game' => [
          'select' => 'Select Game',
          'selected' => 'Selected Game',
          'add' => 'Add Game',
          'not_found' => 'Game not found?',
          'reselect' => 'Reselect Game',
          'reselect_info' => 'Warning: All Inputs will be cleared!',
      ],
      'details_title' => 'Details',
      'details' => [
          'digital' => 'Digital Download',
          'limited' => 'Limited Edition',
          'description' => 'Description',
          'delivery_info' => 'No input for free delivery.',
      ],
      /* Start new strings v1.4.0 */
      'image_upload' => [
          'images' => 'Images',
          'empty_message' => 'Drop image files here or click to upload.',
          'max_files_exceeded' => 'You can not upload any more files.',
          'already_exists' => 'A file with this name already exists in the queue.',
          'invalid_type' => 'You cannot upload files of this type.'
      ],
      /* Start new strings v1.4.0 */
      'sell_title' => 'Sell details',
      'sell' => [
          'avgprice' => 'Average selling price for :game_name: <strong>:avgprice</strong>',
          'price' => 'Price',
          'price_suggestions' => 'Price suggestions',
      ],
      'trade_title' => 'Trade details',
      'trade' => [
          'add_to_tradelist' => 'Add game to tradelist',
          'remove' => 'Remove',
          'additional_charge_partner' => 'Additional charge from trade partner',
          'additional_charge_self' => 'Additional charge from you',
          'trade_suggestions' => 'Trade suggestions',
      ],
      'placeholder' => [
          'sell_price_suggestion' => 'In :Currency_name...',
          'limited' => 'Name of limited edition',
          'description' => 'Describe your item (Optional)',
          'delivery' => 'Delivery costs',
          'sell_price' => 'Price in :Currency_name...',
          'additional_charge' => 'In :Currency_name...',
          'game_name' => 'Type your game name...',
      ],
      'validation' => [
          'trade_list' => 'You need to add at least one game to your trade list.',
          'delivery_pickup' => 'You need to select at least one option.',
          'price' => 'You need to enter a valid price.',
          'no_game_found' => 'Sorry, no game found.',
          'no_game_found_add' => 'Add new game to database.',
      ],
      'add_button' => 'Add Listing',
      'save_button' => 'Save Listing',
    ],

    /* General modal translations */
    'modal' => [
      'close' => 'Close',
    ],

    /* Buy modal on listings overview */
    'modal_buy' => [
      'buy' => 'Buy',
      'buy_game' => '<strong>Buy</strong> :Game_name',
      'total' => 'TOTAL',
      'delivery_free' => 'Free Shipping',
      'delivery_price' => '+ :price Shipping',
      'suggest_price' => 'Suggest a price',
    ],

    /* Buy modal on listings overview */
    'modal_trade' => [
      'trade_game' => '<strong>Trade</strong> :Game_name',
      'suggest' => 'Suggest a Game',
    ],

    /* Add new game to database modal */
    'modal_game' => [
      'title' => 'Add Game to Database',
      'more' => 'More',
      'search' => 'Search',
      'select_system' => 'Select System',
      'searching' => 'Searching for games...',
      'adding' => 'Adding game to :Pagename!',
      'wait' => 'Please wait',
      'placeholder' => [
          'value' => 'Enter title...',
      ],
    ],

    /* Alerts */
    'alert' => [
      'saved' => ':Game_name listing saved!',
      'deleted' => ':Game_name listing deleted!',
      'created' => ':Game_name listing created!',
    ],

];
