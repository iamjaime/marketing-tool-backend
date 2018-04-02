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
    'smi_pool_account_id' => 2, //The smi pool's account id.
    'job_limit_per_hour' => 24, //each job can be filled again by same account after X amount of hours have passed If job isn't complete yet.
    'job_fill_times_per_hour' => 2, //each job can be filled again by same account after X amount of hours have passed If job is set to multiple fill_times.
    'admin_account_id' => 1, //This is the administrator's id in the user's table (all commissions are sent here!)
    'admin_email' => env('MARKETING_TOOL_ADMIN_EMAIL', 'codehead@codelegends.com'), //This is the default admin email.
    'admin_password' => env('MARKETING_TOOL_ADMIN_PASSWORD', 'codelegends!$'), //This is the default admin email.
    'stripe_smi_credits_plan' => env('STRIPE_SMI_CREDITS_PLAN', 'plan')
];