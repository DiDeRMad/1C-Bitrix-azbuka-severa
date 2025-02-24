<?php
return array (
    'session' => [
        'value' => [
            'mode' => 'default',
            'handlers' => [
                'general' => [
                    'type' => 'database',
                ]
            ],
        ]
    ],
    'utf_mode' =>
        array (
            'value' => true,
            'readonly' => true,
        ),
    'cache_flags' =>
        array (
            'value' =>
                array (
                    'config_options' => 3600.0,
                    'site_domain' => 3600.0,
                ),
            'readonly' => false,
        ),
    'cookies' =>
        array (
            'value' =>
                array (
                    'secure' => false,
                    'http_only' => true,
                ),
            'readonly' => false,
        ),
'smtp' =>
    array (
        'value' =>
        array(
            'enabled' => true,
            'debug' => true, //optional
            'log_file' => '/var/mailer.log', //optional
        ),
    ),
    'exception_handling' =>
        array (
            'value' =>
                array (
                    'debug' => false,
                    'handled_errors_types' => 4437,
                    'exception_errors_types' => 4437,
                    'ignore_silence' => false,
                    'assertion_throws_exception' => true,
                    'assertion_error_type' => 256,
                    'log' => NULL,
                ),
            'readonly' => false,
        ),
    'connections' =>
        array (
            'value' =>
                array (
                    'default' =>
                        array (
                            'className' => '\\Bitrix\\Main\\DB\\MysqliConnection',
                            'host' => 'localhost',
                            'database' => 'azb_sev_db',
                            'login' => 'azb_sev_user',
                            'password' => 'NqhY!TCKp%GqKwBxqjDWNRuo',
                            'options' => 2.0,
                        ),
                ),
            'readonly' => true,
        ),
    'crypto' =>
        array (
            'value' =>
                array (
                    'crypto_key' => 'a89865e5a35d094b9e220fafd259c733',
                ),
            'readonly' => true,
        ),
);
