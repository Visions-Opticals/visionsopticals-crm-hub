<?php
return [
    'edition' => env('DORCAS_EDITION','business'),
    'standard_hosts' => explode('//', env('APP_STANDARD_HOSTS','dorcas.io//dorcas.ng')),
    'transtrak_token' => env('TRANSTRAK_TOKEN', '+WopX4Y4vcGzxW/aeLlBQPdMNmDXidKtfhPjWylkpCc='),
    
    'integrations' => [
        [
            'type' => 'payment',
            'image_url' => cdn('/images/integrations/paystack.png'),
            'name' => 'paystack',
            'display_name' => 'Paystack',
            'description' => 'Integrate Paystack in your invoicing to allow your customers pay directly from their email',
            'configurations' => [
                ['name' => 'public_key', 'label' => 'Public Key', 'value' => ''],
                ['name' => 'private_key', 'label' => 'Secret Key', 'value' => ''],
            ],
            'type_integration' => 'keys'
        ],
        [
            'type' => 'payment',
            'image_url' => cdn('/images/integrations/rave.jpg'),
            'name' => 'rave',
            'display_name' => 'Dorcas Pay',
            'description' => 'Integrate Dorcas Pay in your store to allow your customers checkout by paying online',
            'includes' => true,
            'configurations' => [
                ['name' => 'public_key', 'label' => 'Public Key', 'value' => ''],
                ['name' => 'private_key', 'label' => 'Secret Key', 'value' => ''],
            ],
            'type_integration' => 'keys'
        ],
        [
            'type' => 'External Process',
            'image_url' => cdn('/images/integrations/vision-opticals.png'),
            'name' => 'External Processor',
            'display_name' => 'Visions Enhancing  Opticals Backend Processor',
            'description' => 'Integrate CRM to connect with the external backend processor',
            'includes' => true,
            'configurations' => [
                //this token configuration is gotten from the external app we'll like to connect with
                //only the app can decrypt this value to get the expected result
                ['name' => 'auth_token',  'token_value' => "eyJpdiI6ImJzcTB6RFpBU3hReWZnNC9YVy9PeFE9PSIsInZhbHVlIjoiSW0vR0RvY012a0N2NkR5emFTeVYzYzl0L3RXVlRRTko4Y3RRUVN1dDhLcz0iLCJtYWMiOiI4NDIwYTA4YjEyNGMxMGNlN2NjNzRkZDZmMjU1MWU1MWYxZTQzMTc5MzZiZDliY2UyNWNhOTdiNzliMWY2NGM5IiwidGFnIjoiIn0= "],
            ],
            'type_integration' => 'app_token'
        ],
        [
            'type' => 'email',
            'image_url' => cdn('/images/integrations/hubspot.png'),
            'name' => 'hubspot',
            'display_name' => 'Hubspot',
            'includes' => true,
            'description' => 'Integrate Hubspot to supercharge your Customer Operations',
            'configurations' => [
                ['name' => 'oauth_url', 'label' => 'OAuth URL', 'value' => config('modules-integrations.config.hubspot.oauth_url',"https://app.hubspot.com/oauth/authorize?scope=contacts%20oauth%20tickets&redirect_uri=".env('APP_URL', 'https://hub.dorcas.io')."/mit/integrations-oauth-callback/dorcas-hubspot&client_id=dfbc9611-be13-4dfb-8ec6-f981c4cf5710")],
                ['name' => 'oauth_callback_key', 'label' => 'OAuth Call Back Key', 'value' => ''],
                ['name' => 'oauth_refresh_token', 'label' => 'OAuth Refresh Token', 'value' => ''],
                ['name' => 'portal_id', 'label' => 'Portal ID', 'value' => ''],
            ],
            'type_integration' => 'oauth2'
        ]
    ],
    'plans' => [
        'starter' => [
            'config' => [
                'type' => 'free',
                'partner' => '',
                'cycle' => 'free',
                'duration' => '1-months',
                'title' => 'Starter'
            ],
            'description' => [
                'short' => 'FREE',
                'long' => 'Get Started'
            ],
            'features' => [
                'Basic Apps',
                'Dorcas Branding',
                'Basic Support'
            ],
            'footnote' => 'The Starter Package is suitable for starting businesses just trying out the all-in-one business software suite either as a Business, Professional or Vendor'
        ],
        'classic' => [
            'config' => [
                'type' => 'standard',
                'partner' => '',
                'cycle' => 'monthly',
                'duration' => '1-months',
                'title' => 'Classic'
            ],
            'description' => [
                'short' => 'per month',
                'long' => 'Go Classic'
            ],
            'features' => [
                'Full Apps',
                'Custom Branding/URL',
                'Phone, Chat & Email Support'
            ],
            'footnote' => 'The Classic Package is suitable for small businesses. It will remove branding and unlock powerful tools and features. It will also allow you to publish your Professional Service & Online Store to the Marketplace'
        ],
        'access_advantage' => [
            'config' => [
                'type' => 'partner',
                'partner' => '',
                'cycle' => 'package',
                'duration' => '6-months',
                'title' => 'Access Advantage'
            ],
            'description' => [
                'short' => 'for 6 months!',
                'long' => 'The Access Bank Advantage'
            ],
            'features' => [
                'Full Apps',
                'Custom Branding/URL',
                'Phone, Chat & Email Support'
            ],
            'footnote' => 'Take advantage of the Access Bank offer and lock in the benefits and power of the Hub for 6 whole months at a give-away price'
        ]
    ],
    "path_messages" => [
        "home" => [
            "upgrade" => "Thanks for using the (FREE) Starter Version of the [PartnerAppName] software. To take your
            business to the next level, consider upgrading to the Classic version for more power"
        ],
        "apps/crm/customers" => [
            "upgrade" => "Hope you are enjoying the Customers Module. Consider upgrading to the Classic version to
            manage more than customers. It will also unlock the Deals Funnel feature"
        ],
        "apps/ecommerce" => [
            "upgrade" => "Thanks for using the eCommerce Module. Consider upgrading to the Classic version to have your
            own custom FREE domain name (e.g. mybusinessabc.com.ng). It will also unlock the full power of the Website
            & Online Store builders."
        ],
        "apps/finance" => [
            "upgrade" => "Thanks for using the Finance Module. Consider upgrading to the Classic version to enable full
            accounting automation. It will unlock TransTrak feature, syncing your banking transactions with your
            accounting system."
        ],
        "apps/people" => [
            "upgrade" => "Thanks for using the People Module. Consider upgrading to the Classic Version to manage more
            employees. It will also unlock the Payroll feature."
        ],
        "apps/inventory" => [
            "upgrade" => "Thanks for using the Sales Module. Consider upgrading to the Classic Version to remove the
            @[[PartnerAppName]] from your invoices. It will unlock the Deals Funnel feature"
        ],
        "directory" => [
            "upgrade" => "Thanks for using the @[[PartnerAppName]] software. Consider upgrading to the Classic Version
            to be able to multi-task and switch between Business, Vendor or Professional modes"
        ],
        "subscription" => [
            "standard" => "If you start a subscription, you will be billed that amount monthly. You can always cancel
            the subscription at ANY TIME to return to the Starter (FREE) version"
        ]
    ]
];