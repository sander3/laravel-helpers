<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\Models\Snapshot;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Middleware\SubstituteBindings;

class CachedRouteModelBindingTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpTheDatabaseEnvironment();

        Route::get('cache/{snapshot}', fn (Snapshot $snapshot) => $snapshot->url)->middleware(SubstituteBindings::class);
    }

    public function testCacheKey(): void
    {
        $snapshot = factory(Snapshot::class)->create([
            'title' => 'Test Cache Key',
        ]);

        $this->assertTrue($snapshot->exists);

        $cacheKey = $snapshot->getRouteCacheKey($snapshot->getRouteKey());

        $this->assertEquals('9ebc714728c1b8ab93061f026388d7c4', $cacheKey);
    }

    public function testCachedRouteModelBinding(): void
    {
        $snapshot = factory(Snapshot::class)->create();

        $cachedUrl = $snapshot->url;

        $response = $this->get("cache/{$snapshot->getRouteKey()}");

        $response
            ->assertOk()
            ->assertSeeText($snapshot->url);

        $snapshot->update(['url' => 'https://sander.sh']);

        $cachedResponse = $this->get("cache/{$snapshot->getRouteKey()}");

        $cachedResponse
            ->assertOk()
            ->assertSeeText($cachedUrl);
    }

    public function testForgetRouteCache(): void
    {
        $snapshot = factory(Snapshot::class)->create();

        $response = $this->get("cache/{$snapshot->getRouteKey()}");

        $response
            ->assertOk()
            ->assertSeeText($snapshot->url);

        $snapshot->update(['url' => 'https://sander.sh']);

        $snapshot->forgetRouteCache();

        $response = $this->get("cache/{$snapshot->getRouteKey()}");

        $response
            ->assertOk()
            ->assertSeeText('https://sander.sh');
    }
}
