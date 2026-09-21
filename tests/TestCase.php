<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // SPA shell views call @vite(); CI has neither public/hot nor a built
        // manifest, and tests must not depend on a frontend build step.
        $this->withoutVite();
    }
}
