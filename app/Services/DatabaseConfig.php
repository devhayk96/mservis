<?php

namespace App\Services;

use App\Models\ConfigValue;

/**
 * Handle database config values.
 */
class DatabaseConfig
{
    /**
     * Keeps requested earlier values.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $values;

    /**
     * Show if data from database has been requested.
     *
     * @var bool
     */
    protected $databaseRequestHasBeenSent = false;


    /**
     * Return config value.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        if (!$this->databaseRequestHasBeenSent) {
            $this->values = ConfigValue::all()->pluck('value', 'key');
            $this->databaseRequestHasBeenSent = true;
        }

        $value = $this->values->get($key, config($key));

        return $this->prepareValue($value);
    }

    /**
     * Prepare value.
     *
     * @param  mixed $value
     *
     * @return mixed
     */
    protected function prepareValue($value)
    {
        if ($value === 'false') {
            return false;
        } elseif ($value === 'true') {
            return true;
        }

        return $value;
    }
}
