<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // base de datos
        'db' => [
            'driver' => 'pgsql',
            'host' => '158.69.198.138',
            'database' => 'comedor',
            'username' => 'rlera',
            'password' => '44165746',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'port' => '5432'
        ],
        // swagger php
        'swagger' => [
          'path' => __DIR__ . '/../app/',
        ]
        /*
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'almacen',
            'username' => 'alma',
            'password' => 'alma',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]

        'db' => [
            'driver' => 'mysql',
            'host' => 'mysql.hostinger.com.ar',
        		'database' => 'u488125577_repo',
        		'username' => 'u488125577_root',
        		'password' => '44165746',
        		'charset' => 'utf8',
        		'collation' => 'utf8_unicode_ci',
        		'prefix' => '',
          ]
          */
    ],
];
