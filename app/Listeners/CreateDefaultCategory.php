<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Category;

class CreateDefaultCategory
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        Category::createDefault($event->user);
    }
}
