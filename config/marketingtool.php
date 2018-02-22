<?php
/**
 * Our Marketing Tool App Global Configurations.
 *
 * Here is where we can set each user's net worth value etc.
 */
return [

    'net_worth' => 0.01, //this means each social media account will be paid 1 cent for every "friend"/"follower" that they have.
    'system_commission' => 0.01, //the system's commission per social media account's friend
    'smi_pool' => 0.01, //the pool's commission ( this is basically 1% of the credits in the admin's account)
    'admin_account_id' => 1, //This is the administrator's id in the user's table (all commissions are sent here!)
    'admin_email' => env('MARKETING_TOOL_ADMIN_EMAIL', 'codehead@codelegends.com'), //This is the default admin email.
    'admin_password' => env('MARKETING_TOOL_ADMIN_PASSWORD', 'codelegends!$'), //This is the default admin email.
];