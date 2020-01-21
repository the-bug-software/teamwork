<?php

namespace TheBugSoftware\Teamwork;

use Illuminate\Support\Facades\Facade;

class TeamworkFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'teamwork';
    }
}
