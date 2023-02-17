<?php

namespace VipIP\GeoIP;

use Gelf\Publisher;
use Gelf\Transport\UdpTransport;
use Monolog\Formatter\GelfMessageFormatter;
use Monolog\Handler\FallbackGroupHandler;
use Monolog\Handler\GelfHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;

class Logger
{
    public static function getInstance(array $config): LoggerInterface
    {
        $logLevel = $config['graylog']['logLevel'] ?? \Monolog\Level::Debug;

        // Подключаем Graylog(Gelf) для отправки логов
        $transport = new UdpTransport(
            $config['graylog']['host'],
            $config['graylog']['port']
        );
        $gelfHandler = new GelfHandler(new Publisher($transport), $logLevel);
        $gelfHandler->setFormatter(new GelfMessageFormatter($config['graylog']['source'] ?? 'app'));
        //////////////

        return (new \Monolog\Logger('geoip'))
            ->pushProcessor(new PsrLogMessageProcessor())
            ->pushHandler(new FallbackGroupHandler([
                $gelfHandler,
                new StreamHandler($config['log']['path'] . '/graylog-connection-failed.log', $logLevel),
                new NullHandler()
            ]))
        ;
    }
}