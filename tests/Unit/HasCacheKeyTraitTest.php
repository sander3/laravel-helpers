<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\Models\Snapshot;

class HasCacheKeyTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpTheDatabaseEnvironment();
    }

    public function testCacheKey(): void
    {
        $snapshot = factory(Snapshot::class)->create([
            'title' => 'Test Cache Key',
        ]);

        $this->assertTrue($snapshot->exists);

        $snapshot = $snapshot->fresh(); // We want to make sure the same cache key gets generated every time!
        $this->assertFalse($snapshot->wasRecentlyCreated);

        $cacheKey = $snapshot->getCacheKey();

        $expectedCacheKey = '42ee3a67c0bd0204f72ee52c909effa5';

        $this->assertEquals($expectedCacheKey, $cacheKey);
    }
}
