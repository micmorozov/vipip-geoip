<?php
require_once '../vendor/autoload.php';
$config = require '../config/main.php';

use VipIP\GeoIP\{GeoIP, Logger};
use Psr\Log\LoggerInterface;

/** @var LoggerInterface $logger */
$logger = Logger::getInstance($config);
\Monolog\ErrorHandler::register($logger);

$ip = $_GET['ip'] ?? $_SERVER['REMOTE_ADDR'];

$geoIp = new GeoIP($config['maxmind']['path']);
//$record = $geoIp->getGeoInfoByIp($ip);
$record = $geoIp->getLegacyFormat($ip);

$logger->notice('Request Data', [
    'ip' => $ip,
    'response' => $record
]);

echo json_encode($record);