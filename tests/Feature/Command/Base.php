<?php


namespace GitBalocco\LaravelUiViewComposer\Test\Feature\Command;

use Orchestra\Testbench\TestCase;

class Base extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [\GitBalocco\LaravelUiViewComposer\ServiceProvider::class];
    }

}