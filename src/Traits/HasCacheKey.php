<?php

namespace Soved\Laravel\Helpers\Traits;

trait HasCacheKey
{
    /**
     * Get the value of the model's cache key.
     */
    public function getCacheKey(): string
    {
        return md5(vsprintf('%s.%s.%s', [
            $this->getKey(),
            $this->getTable(),
            $this->getConnectionName(),
        ]));
    }
}
