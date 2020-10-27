<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Language Lines
    |--------------------------------------------------------------------------
    */

    'sold_by' => 'Sold by :Username (:Country, :Place)',
    'paypal_payment' => 'PayPal Payment',
    'secure_payment' => 'Secure Payment',
    'unsecure_payment' => 'Unsecure Payment',
    'cash_payment' => 'Cash on pickup',
    'balance' => 'Balance',
    'available_balance' => 'Available Balance',
    'remaining_balance' => 'Remaining Balance',
    'total' => 'Total',
    'hold_info' => 'We will put this transaction on hold and release it after you have received :Gamename from :Username.',
    'transactions' => 'Transactions',
    'no_transactions' => 'No Transactions',
    'sales' => 'Sales',

    /* Alerts */
    'alert' => [
      'canceled' => 'Payment canceled!',
      'refunded' => 'Payment successfully refunded!',
      'successful' => 'Payment successful!',
      'already_paid' => 'This item is already paid!',
    ],

    /* Form */
    'form' => [
      'delivery_info' => 'Only available with enabled delivery option',
      'youll_get' => "You'll get",
      'fees' => 'Fees',
      'secure' => 'Secure',
      'fast' => 'Fast',
      'easy' => 'Easy',
    ],

    /* Offer */
    'offer' => [
      'pay_now' => 'Pay :total',
      'protected_payment' => 'Protected payment',
      'status' => 'Status',
      'unpaid' => 'Unpaid',
      'paid' => 'Paid',
      'refunded' => 'Refunded',
      'money_received' => 'Money received from :Username',
      'awaiting_payment' => 'Awaiting payment',
      'pending' => 'Pending',
      'rating_warning' => "After this rating we'll send your money to :Username. If you have any problems, please report the offer <strong>before</strong> you send your rating."
    ],

    /* Offer */
    'transaction' => [
      'pay_now' => 'Pay :total',
      'type' => [
        'type' => 'type',
        'fee' => 'Fee',
        'sale' => 'Sale',
        'withdrawal' => 'Withdrawal',
        'purchase' => 'Purchase',
        'refund' => 'Refund',
      ],
    ],

    /* Withdrawal */
    'withdrawal' => [
      /* Start new strings v1.4.2 */
      'bank_transfer' => 'Bank Transfer',
      'bank' => [
          'holder_name' => 'Bank account holder name',
          'iban' => 'IBAN number',
          'bic' => 'Swift (BIC) code',
          'bank_name' => 'Bank name',
      ],
      /* End new strings v1.4.2 */
      'withdrawal' => 'Withdrawal',
      'withdrawal_details' => 'Withdrawal Details',
      'submit_request' => 'Submit Request',
      'amount' => 'Amount',
      'paypal_email' => 'PayPal Email address',
      'payment_method' => 'Payment method',
      'details' => 'Details',
      'status' => 'Status',
      'alert' => [
        'no_balance' => 'No available balance!',
        'successfully' => 'Your withdrawal request has been successfully submitted!',
        /* Start new strings v1.4.2 */
        'failed' => 'Your withdrawal request could not be submitted! Please try again!'
        /* End new strings v1.4.2 */
      ],
    ],

];
