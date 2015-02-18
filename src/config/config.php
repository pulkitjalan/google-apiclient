<?php

return [
    /*
    |----------------------------------------------------------------------------
    | Google application name
    |----------------------------------------------------------------------------
    */
    'application_name' => '',

    /*
    |----------------------------------------------------------------------------
    | Google OAuth 2.0 access
    |----------------------------------------------------------------------------
    |
    | Keys for OAuth 2.0 access, see the API console at
    | https://developers.google.com/console
    |
    */
    'client_id'       => '',
    'client_secret'   => '',
    'redirect_uri'    => '',
    'scopes'          => [],
    'access_type'     => 'online',
    'approval_prompt' => 'auto',

    /*
    |----------------------------------------------------------------------------
    | Google developer key
    |----------------------------------------------------------------------------
    |
    | Simple API access key, also from the API console. Ensure you get
    | a Server key, and not a Browser key.
    |
    */
    'developer_key' => '',

    /*
    |----------------------------------------------------------------------------
    | Google service account
    |----------------------------------------------------------------------------
    |
    | Set the information below to use assert credentials
    | Leave blank to use app engine or compute engine.
    |
    */
    'service' => [
        /*
        | Example xxx@developer.gserviceaccount.com
        */
        'account' => '',

        /*
        | Example ['https://www.googleapis.com/auth/cloud-platform']
        */
        'scopes' => [],

        /*
        | Path to key file
        | Example storage_path().'/key/google.p12'
        */
        'key' => '',
    ],
];
