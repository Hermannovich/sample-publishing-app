<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\ArticleRepoInterface; 
use App\Repositories\ArticleRepo;
use App\Repositories\Interfaces\TokenRepoInterface;
use App\Repositories\TokenRepo;
use App\Repositories\UserRepo;
use App\Repositories\Interfaces\UserRepoInterface;

class RepositoriesServiceProvider extends ServiceProvider
{
    protected  $defer = true;
    

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ArticleRepoInterface::class, ArticleRepo::class);
        $this->app->bind(TokenRepoInterface::class, TokenRepo::class);
        $this->app->bind(UserRepoInterface::class, UserRepo::class);
    }
    
    public function provides() {
        return [
            ArticleRepoInterface::class,
            TokenRepoInterface::class,
            UserRepoInterface::class,
        ];
    }
}
