<?php

/*
 * Copyright (c) 2024-2025. Encore Digital Group.
 * All Right Reserved.
 */

namespace PHPGenesis\Laravel\Providers;

use Illuminate\Foundation\Events\Terminating;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use PHPGenesis\Laravel\Disposable\Events\DisposeEvent;
use PHPGenesis\Laravel\Disposable\Events\Listeners\DisposableEventListener;

class PHPGenesisServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/phpgenesis.php", "phpgenesis");
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . "/../../config/phpgenesis.php" => config_path("phpgenesis.php"),
        ]);

        Event::listen(Terminating::class, DisposableEventListener::class);
        Event::listen(DisposeEvent::class, DisposableEventListener::class);

        Queue::after(function (JobProcessed $event): void {
            DisposeEvent::dispatch();
        });
    }
}
