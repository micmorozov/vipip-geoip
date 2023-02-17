<?php

namespace VipIP\GeoIP;

use GeoIp2\Database\Reader;
use GeoIp2\Model\City;
use MaxMind\Db\Reader\InvalidDatabaseException;

class GeoIP
{
    private Reader $reader;

    public function __construct(string $dbPath)
    {
        $this->reader = new Reader($dbPath);
    }

    /**
     * @throws InvalidDatabaseException
     */
    public function getReader(): Reader
    {
        return $this->reader;
    }

    /**
     * Возвращает гео данные по ип адресу.
     *
     * @param string $ip
     * @return bool | \GeoIp2\Model\City
     * @throws InvalidDatabaseException
     */
    public function getGeoInfoByIp(string $ip): ?City
    {
        $requestIp = $this->_filterIp($ip);

        if ($requestIp) {
            return $this->getReader()->city($requestIp);
        }

        return null;
    }

    /**
     * Фильтрует ип адрес от нежелательных - локальных и зарезервированных.
     * @param $ip
     * @return mixed
     */
    private function _filterIp($ip)
    {
        return filter_var(trim($ip), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * @param null $ip
     * @return array|void
     * @throws InvalidDatabaseException
     * @deprecated используйте getGeoInfoByIp
     */
    public function getLegacyFormat($ip)
    {
        $info = $this->getGeoInfoByIp($ip);
        if (!$info) {
            return [
                'country' => 0,
                'region' => 0,
                'city' => 0
            ];
        }

        $countryCode = strtoupper($info->country->isoCode ?: 0);
        if (!in_array($countryCode, ['RU', 'UA'])) {
            return [
                'country' => $countryCode,
                'region' => 0,
                'city' => 0
            ];
        }

        $regionIsoCode = $info->mostSpecificSubdivision->isoCode ?? 0;
        // Крым перемещаем в RU
        if ($countryCode == 'UA' && $regionIsoCode == 30) {
            $regionIsoCode = 32;
            $countryCode = 'RU';
        }

        $region = Regions::getRegionIdByISO($regionIsoCode) ?? 0;
        return [
            'country' => $countryCode,
            'region' => $region,
            'city' => $info->city->geonameId ?: 0
        ];
    }
}