<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default LDAP Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the LDAP connections below you wish
    | to use as your default connection for all LDAP operations. Of
    | course you may add as many connections you'd like below.
    |
    */

    'default' => env('LDAP_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | LDAP Connections
    |--------------------------------------------------------------------------
    |
    | Below you may configure each LDAP connection your application requires
    | access to. Be sure to include a valid base DN - otherwise you may
    | not receive any results when performing LDAP search operations.
    |
    */

    'connections' => [

        'default' => [
            'hosts' => [env('LDAP_HOST', '127.0.0.1')],
            'username' => env('LDAP_USERNAME', 'cn=user,dc=local,dc=com'),
            'password' => env('LDAP_PASSWORD', 'secret'),
            'port' => env('LDAP_PORT', 389),
            'base_dn' => env('LDAP_BASE_DN', 'dc=local,dc=com'),
            'timeout' => env('LDAP_TIMEOUT', 5),
            'use_ssl' => env('LDAP_SSL', false),
            'use_tls' => env('LDAP_TLS', false),
            'use_sasl' => env('LDAP_SASL', false),
            'sasl_options' => [
                // 'mech' => 'GSSAPI',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | LDAP Logging
    |--------------------------------------------------------------------------
    |
    | When LDAP logging is enabled, all LDAP search and authentication
    | operations are logged using the default application logging
    | driver. This can assist in debugging issues and more.
    |
    */

    'logging' => [
        'enabled' => env('LDAP_LOGGING', true),
        'channel' => env('LOG_CHANNEL', 'stack'),
        'level' => env('LOG_LEVEL', 'info'),
    ],

    /*
    |--------------------------------------------------------------------------
    | LDAP Cache
    |--------------------------------------------------------------------------
    |
    | LDAP caching enables the ability of caching search results using the
    | query builder. This is great for running expensive operations that
    | may take many seconds to complete, such as a pagination request.
    |
    */

    'cache' => [
        'enabled' => env('LDAP_CACHE', false),
        'driver' => env('CACHE_DRIVER', 'file'),
    ],

    /*
    |--------------------------------------------------------------------------
    | LDAP Role Mapping
    |--------------------------------------------------------------------------
    |
    | This configuration maps LDAP groups to application roles.
    | Users will be assigned roles based on their LDAP group membership.
    |
    */

    'role_mapping' => [
        'groups' => [
            'Admin' => [
                'CN=DMS_Admins,OU=Groups,DC=example,DC=com',
                'CN=IT_Admins,OU=Groups,DC=example,DC=com',
            ],
            'Manager' => [
                'CN=DMS_Managers,OU=Groups,DC=example,DC=com',
                'CN=Department_Heads,OU=Groups,DC=example,DC=com',
            ],
            'Compliance Officer' => [
                'CN=DMS_Compliance,OU=Groups,DC=example,DC=com',
                'CN=Compliance_Team,OU=Groups,DC=example,DC=com',
            ],
            'Auditor' => [
                'CN=DMS_Auditors,OU=Groups,DC=example,DC=com',
                'CN=External_Auditors,OU=Groups,DC=example,DC=com',
            ],
        ],
        'manager_groups' => [
            'CN=DMS_Managers,OU=Groups,DC=example,DC=com',
            'CN=Department_Heads,OU=Groups,DC=example,DC=com',
        ],
        'default_role' => 'General User',
    ],

    /*
    |--------------------------------------------------------------------------
    | LDAP User Attributes
    |--------------------------------------------------------------------------
    |
    | These are the LDAP attributes that will be used to map user data
    | to the local database fields.
    |
    */

    'user_attributes' => [
        'username' => env('LDAP_USER_ATTRIBUTE', 'samaccountname'),
        'email' => env('LDAP_EMAIL_ATTRIBUTE', 'mail'),
        'name' => env('LDAP_NAME_ATTRIBUTE', 'displayname'),
        'department' => env('LDAP_DEPARTMENT_ATTRIBUTE', 'department'),
        'title' => env('LDAP_TITLE_ATTRIBUTE', 'title'),
    ],

];
