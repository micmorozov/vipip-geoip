<?php
$graylogInit = require __DIR__ . '/../common/service/graylog.php';

return array_merge($graylogInit, [
    'host' => '',
    'logLevel' => \Monolog\Level::Info,
    'source' => 'geoip.backend'
]);