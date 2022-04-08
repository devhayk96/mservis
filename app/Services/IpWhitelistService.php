<?php

namespace App\Services;

use App\Models\AllowedIp;

class IpWhitelistService
{
    /**
     * Localhost IP.
     */
    protected const LOCALHOST_IP = '127.0.0.1';

    /**
     * Check if a given IP is in the whitelist.
     *
     * @return bool
     */
    public function ipInWhitelist(string $ip): bool
    {
        return in_array($ip, $this->getIpList());
    }

    /**
     * Return a list of the valid IPs.
     *
     * @return array
     */
    protected function getIpList(): array
    {
        $ips = $this->getIpFromDatabase();
        $ips[] = self::LOCALHOST_IP;

        return $ips;
    }

    /**
     * Return a list of IPs from the database.
     *
     * @return array
     */
    protected function getIpFromDatabase(): array
    {
        return AllowedIp::pluck('ip')
            ->toArray();
    }
}
