<?php

namespace App\Services;

/**
 * Help to deal with *_delays config options.
 */
class SubsequentRequests
{
    /**
     * Return delay before next request.
     *
     * @param  string $configStr
     * @param  int    $lastAttemptNumber Number of attempt that
     *                                   has been tried.
     *
     * @return int
     */
    public function getDelay(string $configStr, int $lastAttemptNumber): int
    {
        $config = $this->parseConfigString($configStr);

        return data_get(
            $config,
            ($lastAttemptNumber - 1),
            data_get($config, (count($config) - 1))
        );
    }

    /**
     * Return number of attempts we will try.
     *
     * @param  string $configStr
     *
     * @return int
     */
    public function getAttemptsAmount(string $configStr): int
    {
        $config = $this->parseConfigString($configStr);
        return count($config);
    }

    /**
     * Return delays configuration.
     *
     * @param  string $config
     *
     * @return array
     */
    protected function parseConfigString(string $config): array
    {
        return array_map('intval', explode(' ', $config))
            ?: $this->getFallbackConfig();
    }

    /**
     * Return a fallback delays config.
     *
     * @return array
     */
    protected function getFallbackConfig(): array
    {
        return [5, 15];
    }
}
