<?php

namespace Soved\Laravel\Helpers\Traits;

use Illuminate\Support\Facades\Cache;

trait CachedRouteModelBinding
{
    /**
     * Get the value of the model's route cache key.
     *
     * @param mixed $routeKey
     */
    public function getRouteCacheKey($routeKey): string
    {
        return md5(vsprintf('%s.%s', [
            mb_strtolower($routeKey),
            $this->getTable(),
        ]));
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param mixed       $value
     * @param string|null $field
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $cacheKey = $this->getRouteCacheKey($value);

        $model = Cache::get($cacheKey);

        if (! is_null($model)) {
            return $model;
        }

        Cache::forever($cacheKey, $model = parent::resolveRouteBinding($value, $field));

        return $model;
    }

    public function forgetRouteCache(): bool
    {
        $cacheKey = $this->getRouteCacheKey($this->getRouteKey());

        return Cache::forget($cacheKey);
    }
}
