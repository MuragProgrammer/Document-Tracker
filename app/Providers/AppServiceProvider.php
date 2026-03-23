<?php

namespace App\Providers;
use App\Models\Department;
use App\Models\Section;
use App\Observers\DepartmentObserver;
use App\Observers\SectionObserver;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Department::observe(DepartmentObserver::class);
        Section::observe(SectionObserver::class);
    }
}
