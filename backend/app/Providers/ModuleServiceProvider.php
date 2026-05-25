<?php

namespace App\Providers;

use App\Modules\Auth\Events\UserLoggedIn;
use App\Modules\Auth\Listeners\LogLoginActivity;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Repositories\UserRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/**
 * Registers all module routes, bindings, and event listeners.
 * Each module has its own routes.php file that gets auto-loaded here.
 */
class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Module directories to auto-load routes from.
     */
    protected array $modules = [
        'Auth',
        // 'Content',   — Fase 2
        // 'Media',     — Fase 2
        // 'Core',      — Fase 2
        // 'Analytics', — Fase 3
        // 'Notification', — Fase 3
    ];

    public function register(): void
    {
        // Repository bindings
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
        $this->loadModuleRoutes();
        $this->registerEvents();
    }

    /**
     * Auto-load routes.php from each registered module.
     */
    private function loadModuleRoutes(): void
    {
        foreach ($this->modules as $module) {
            $routeFile = app_path("Modules/{$module}/routes.php");

            if (file_exists($routeFile)) {
                $this->loadRoutesFrom($routeFile);
            }
        }
    }

    /**
     * Register module event → listener mappings.
     */
    private function registerEvents(): void
    {
        Event::listen(UserLoggedIn::class, LogLoginActivity::class);
    }
}
