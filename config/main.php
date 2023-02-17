<?php
$graylog = require __DIR__ . '/service/graylog.php';

return [
    'maxmind' => [
        'path' => __DIR__ . '/db/GeoLite2-City.mmdb'
    ],
    'graylog' => $graylog,
    'log' => [
        'path' => __DIR__ . '/../var/log'
    ]
];