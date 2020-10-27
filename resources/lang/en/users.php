<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Users Language Lines
    |--------------------------------------------------------------------------
    */

    'general' => [
      'profile' => 'Profile',
      'member_since' => 'Member since :time',
      'no_ratings' => 'No Ratings',
    ],

    'profile' => [
      'listings' => 'Listings',
      'ratings' => 'Ratings',
      'stats' => 'Stats',
      'rating_from' => 'Rating from :username',
      'is_online' => ':username is online',
      'last_seen' => 'last seen :date',
      'banned' => 'User banned',
    ],


    /* Add new game to database modal */
    'dash' => [
      'dashboard' => 'Dashboard',
      'quick_listing' => 'Add Listing',
      'quick_game' => 'Add Game',
      'show_all' => 'Show all',
      'show_listings' => 'Show :count more active listings',
      'show_offers' => 'Show :count more active offers',
      'active' => 'Active',
      'active_listings' => 'Active Listings',
      'active_offers' => 'Active Offers',
      'complete' => 'Complete',
      'deleted' => 'Deleted',
      'declined' => 'Declined',
      'stats' => [
          'stats' => 'Stats',
          'earned_money' => 'Earned money',
          'spend_money' => 'Spend money',
          'clicks_listings' => 'Clicks on listings',
          'created_listings' => 'Listings created',
          'made_offers' => 'Offers made',
          'membership' => 'Membership',
      ],
      /* & offers */
      'listings' => [
          'status_0' => 'Waiting',
          'status_1' => 'Rate :Username',
          'status_1_wait' => 'Waiting for rating',
          'no_offers' => 'Currently no offers.',
          'clicks' => 'Clicks',
      ],
      'settings' => [
          'settings' => 'Settings',
          'password' => 'Password',
          'password_heading' => 'Change password',
          'password_old' => 'Change password',
          'password_new' => 'New password',
          'password_new_confirm' => 'Confirm new password',
          'profile' => 'Profile',
          'profile_link' => 'Your profile link:',
          'username' => 'Username',
          'email' => 'eMail Address',
          'change_avatar' => 'Change profile image',
          'browse' => 'Browse',
          'location_change' => 'Change location',
          'location_set' => 'Set location',
          'location_no' => 'No location set',
      ],
    ],

    /* Alerts */
    'alert' => [
      'password_changed' => 'Your password has been changed successfully.',
      'profile_saved' => 'Your profile has been saved successfully.',
      'email_taken' => 'This email address is already taken.',
    ],

    /* Modal for delete listing in dashboard */
    'modal_delete_listing' => [
      'title' => 'Delete :Gamename listing',
      'info' => 'Are you sure you want to delete this listing? ',
      'delete_listing' => 'Delete listing',
    ],


    /* Modal for delete listing in dashboard */
    'modal_delete_offer' => [
      'title' => 'Delete :Gamename offer',
      'info' => 'Are you sure you want to delete this offer? ',
      'delete_listing' => 'Delete offer',
    ],

    /* Add new game to database modal */
    'modal_location' => [
      'title' => 'Set Location',
      'set_location' => 'Set Location',
      'info' => "Don't worry! In a few seconds you can add your first listing on GameTrade. But first we need your location. So please select your country and add your postal code to set your place.",
      'selected_location' => 'Selected location',
      'location_saved' => 'Location saved!',
      /* JS Counter between close_sec_1 and close_sec 2 */
      'close_sec_1' => 'This window will close in',
      'close_sec_2' => 'seconds automatically or',
      'close_now' => 'Close now',
      'error' => 'An error occurred, please try again.',
      'placeholder' => [
          'country' => 'Select your country',
          'postal_code' => 'Postal code',
          'postal_code_locality' => 'Type your postal code',
          'where_are_we_going' => 'Where are we going?',
      ],
      'status' => [
          'search_info' => ' Enter at least 3 characters to start searching.',
          'searching' => 'Searching for location...',
          'searching_place' => 'Search for places...',
          'location_found' => 'Location found!',
          'locations_found' => 'Locations found!',
          'no_location_found' => 'No location found',
      ],
    ],

];
