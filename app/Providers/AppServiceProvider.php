<?php

namespace App\Providers;

use App\Models\AdminNotification;
use App\Models\BlogArticle;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\Language;
use App\Models\NavbarMenu;
use App\Models\SeoConfiguration;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Config;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $activeTheme = active_theme();

        if (env('APP_INSTALLED')) {

            Paginator::useBootstrapFive();

            if (settings('include_language_code')) {
                Config::set('laravellocalization.supportedLocales', get_active_languages());
            }

            view()->composer('*', function ($view) {
                $view->with([
                    'settings' => settings(),
                    'activeTheme' => active_theme(),
                    'activeThemeAssets' => active_theme(true)
                ]);
            });

            if (!is_admin_path()) {

                if (settings('enable_force_ssl')) {
                    $this->app['request']->server->set('HTTPS', true);
                }

                view()->composer('*', function ($view) {
                    $languages = Language::where('active',1)->orderBy('position', 'asc')->get();
                    $view->with('languages', $languages);
                });

                view()->composer($activeTheme.'layouts.includes.nav-menu', function ($view) {
                    $navbarMenuLinks = NavbarMenu::where('lang', get_lang())->where('type', 'header')->whereNull('parent_id')->with(['children' => function ($query) {
                        $query->byOrder();
                    }])->byOrder()->get();
                    $view->with('navbarMenuLinks', $navbarMenuLinks);
                });

                view()->composer($activeTheme.'blog.sidebar', function ($view) {
                    $blogCategories = BlogCategory::where('lang', get_lang())->get();
                    $popularBlogArticles = BlogArticle::where('lang', get_lang())->orderbyDesc('views')->limit(8)->get();

                    $tags = [];
                    $data = BlogArticle::where('lang', get_lang())->select('tags')->get();
                    foreach ($data as $value){
                        if(!empty($value->tags)) {
                            $tag = explode(',', $value->tags);
                            $tags = array_merge($tags, $tag);
                        }
                    }
                    $tags = array_unique($tags);

                    $view->with(['blogCategories' => $blogCategories, 'popularBlogArticles' => $popularBlogArticles,'blogTags' => $tags]);
                });

                view()->composer($activeTheme.'layouts.includes.footer', function ($view) {
                    $footerMenuLinks = NavbarMenu::where('lang', get_lang())->where('type', 'footer')->whereNull('parent_id')->with(['children' => function ($query) {
                        $query->byOrder();
                    }])->byOrder()->get();
                    $view->with('footerMenuLinks', $footerMenuLinks);
                });

            }

            if (is_admin_path()) {

                view()->composer('*', function ($view) {
                    $adminLanguages = Language::where('active',1)->orderBy('position', 'asc')->get();
                    $view->with('adminLanguages', $adminLanguages);
                });

                view()->composer('admin.includes.header', function ($view) {
                    $adminNotifications = AdminNotification::orderbyDesc('id')->limit(20)->get();
                    $unreadAdminNotifications = AdminNotification::where('status', 0)->get()->count();
                    $unreadAdminNotificationsAll = $unreadAdminNotifications;
                    if ($unreadAdminNotifications > 9) {
                        $unreadAdminNotifications = "9+";
                    }
                    $view->with([
                        'adminNotifications' => $adminNotifications,
                        'unreadAdminNotifications' => $unreadAdminNotifications,
                        'unreadAdminNotificationsAll' => $unreadAdminNotificationsAll,
                    ]);
                });

                view()->composer('admin.includes.sidebar', function ($view) {
                    $unviewedUsersCount = User::where('is_viewed', 0)->count();
                    $commentsNeedsAction = BlogComment::where('status', 0)->get()->count();
                    $unviewedSubscriptions = Subscription::where('is_viewed', 0)->count();
                    $unviewedTransactionsCount = Transaction::where('is_viewed', 0)->whereIn('status', [2, 3])->count();
                    $view->with([
                        'unviewedUsersCount' => ($unviewedUsersCount > 50) ? "50+" : $unviewedUsersCount,
                        'commentsNeedsAction' => ($commentsNeedsAction > 50) ? "50+" : $commentsNeedsAction,
                        'unviewedSubscriptions' => ($unviewedSubscriptions > 50) ? "50+" : $unviewedSubscriptions,
                        'unviewedTransactionsCount' => ($unviewedTransactionsCount > 50) ? "50+" : $unviewedTransactionsCount,
                    ]);
                });
            }
        }
    }
}
