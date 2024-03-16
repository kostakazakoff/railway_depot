<?php

namespace App\Providers;

use App\Events\ArticleCRUD;
use App\Events\StoreCRUD;
use App\Events\UserCRUD;
use App\Listeners\CreateLog;
use App\Models\Article;
use App\Observers\ArticleObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ArticleCRUD::class => [
            CreateLog::class,
        ],
        StoreCRUD::class => [
            CreateLog::class,
        ],
        UserCRUD::class => [
            CreateLog::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Article::observe(ArticleObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
