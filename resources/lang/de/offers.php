<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Offers Language Lines
    |--------------------------------------------------------------------------
    */

    'general' => [
      'decline_reason' => 'Decline reason',
      'decline_reason_empty' => 'No reason',
      'decline_reason_offer_deleted' => 'Offer deleted.',
      'decline_reason_another_offer' => 'Accepted another offer.',
      'decline_reason_staff' => 'Closed by staff.',
      'report' => 'Report',
      'reported_by' => 'Reported by <strong>:Username</strong>',
      'report_closed' => 'Closed by :Username',
      'staff' => ':Page_name Staff',
      'revoked' => 'Revoked',
      'enter_message' => 'Enter your message...',
      'no_offers' => 'There are no offers available.',
      'chat_buy' => 'Hey! I want to buy your :Game_name (:Platform_name) for :Price.',
      'chat_trade' => 'Hey! I want to trade your :Game_name (:Platform_name) for :Trade_game (:Trade_platform).',
      'chat_sent' => 'Sent',
      'chat_read' => 'Read',
    ],

    'status_wait' => [
      'wait' => 'Waiting for acceptance',
      'accept' => 'Accept',
      'decline' => 'Decline',
    ],

    'status_rate' => [
      'rate_user' => 'Rate :Username',
      'rate_wait' => 'Waiting for rating from :Username',
    ],

    'status_complete' => [
      'rating_user' => 'Rating from :Username',
      'no_notice' => 'No notice',
    ],

    'modal_accept' => [
      'title' => 'Accept offer',
      'info' => "You can't undo this action. All other offers will be automatically declined.",
    ],

    'modal_decline' => [
      'title' => 'Decline offer',
      'info' => "You can't undo this action.",
      'reason_placeholder' => 'Decline reason (optional)',
    ],

    'modal_rating' => [
      'title_offer' => 'Close offer & rate :Username',
      'title_listing' => 'Close listing & rate :Username',
      'negative' => 'Negative',
      'neutral' => 'Neutral',
      'positive' => 'Positive',
      'reason_placeholder' => 'Reason for rating (optional)',
      'rate_button' => 'Rate :Username',
    ],

    'modal_report' => [
      'title' => 'Report offer',
      'describe_problem' => 'Describe your problem',
      /* Start new strings v1.2 */
      'info' => 'Please describe your problem with the offer. A member of our staff will join the conversation as soon as possible.',
      /* End new strings v1.2 */
    ],

    /* Alerts */
    'alert' => [
      'same_game' => 'Sorry, you cant suggest the same game!',
      'suggestion_disabled' => 'Sorry, you cant suggest games!',
      'deleted' => ':Game_name offer deleted!',
      /* Start new strings v1.2 */
      'reported' => 'Offer reported! A member of our staff will join the conversation as soon as possible.',
      /* End new strings v1.2 */
      'already_reported' => 'Offer already reported by :Username!',
      'missing_reason' => 'Please describe your problem to report this offer!',
      'own_offer' => "Sorry, you cant send an offer to your own listing!",
    ],

];
