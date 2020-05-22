<?php

namespace Tests\Feature;

use App;
use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Soved\Laravel\Helpers\Http\Middleware\SetPreferredLocale;

class SetPreferredLocaleMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('middleware-test', fn () => 'test')->middleware(SetPreferredLocale::class);
    }

    public function testDefaultLocale(): void
    {
        $defaultLocale = config('app.locale');

        App::setLocale('ru'); // ISO 639-1 language code for Russian

        $response = $this->get('middleware-test');

        $response->assertOk();

        $this->assertEquals($defaultLocale, App::getLocale());
    }

    public function testPreferredLocale(): void
    {
        $defaultLocale = config('app.locale');
        $preferredLocale = 'nl'; // ISO 639-1 language code for Dutch

        config(['app.fallback_locale' => $preferredLocale]);

        $this->assertEquals($defaultLocale, App::getLocale());

        $response = $this
            ->withHeaders([
                'Accept-Language' => 'nl,en-US;q=0.9,en-GB;q=0.8,en;q=0.7',
            ])
            ->get('middleware-test');

        $response->assertOk();

        $this->assertEquals($preferredLocale, App::getLocale());
    }
}
