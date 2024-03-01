<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Methods\ReCaptchaValidation;
use App\Models\BlogArticle;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    public function index()
    {
        $page_limit = settings('blog')->page_limit ?? 8;
        if (request()->has('search')) {
            $q = request('search');
            $blogArticles = BlogArticle::where([['title', 'like', '%' . $q . '%'], ['lang', get_lang()]])
                ->OrWhere([['slug', 'like', '%' . $q . '%'], ['lang', get_lang()]])
                ->OrWhere([['content', 'like', '%' . $q . '%'], ['lang', get_lang()]])
                ->OrWhere([['short_description', 'like', '%' . $q . '%'], ['lang', get_lang()]])
                ->OrWhere([['tags', 'like', '%' . $q . '%'], ['lang', get_lang()]])
                ->orderbyDesc('id')
                ->paginate($page_limit);
            $blogArticles->appends(['search' => $q]);
        } else {
            $blogArticles = BlogArticle::where('lang', get_lang())->orderbyDesc('id')->paginate($page_limit);
        }
        return view($this->activeTheme.'.blog.index', ['blogArticles' => $blogArticles]);
    }

    public function category($slug)
    {
        $page_limit = settings('blog')->page_limit ?? 8;
        $blogCategory = BlogCategory::where([['lang', get_lang()], ['slug', $slug]])->first();
        if ($blogCategory) {
            $blogCategory->increment('views');
            $blogArticles = BlogArticle::where('category_id', $blogCategory->id)->orderbyDesc('id')->paginate($page_limit);
            return view($this->activeTheme.'.blog.category', [
                'blogCategory' => $blogCategory,
                'blogArticles' => $blogArticles,
            ]);
        } else {
            return redirect()->route('blog.index');
        }
    }

    public function tag($slug)
    {
        $page_limit = settings('blog')->page_limit ?? 8;
        $blogArticles = BlogArticle::where([['tags', 'like', '%' . $slug . '%'], ['lang', get_lang()]])
            ->orderbyDesc('id')
            ->paginate($page_limit);
            return view($this->activeTheme.'.blog.tag', [
                'blogTag' => $slug,
                'blogArticles' => $blogArticles,
            ]);
    }

    public function articles()
    {
        $blogArticles = BlogArticle::where('lang', get_lang())->orderbyDesc('id')->paginate(9);
        return view($this->activeTheme.'.blog.articles', ['blogArticles' => $blogArticles]);
    }

    public function article($slug)
    {
        $blogArticle = BlogArticle::where([['lang', get_lang()], ['slug', $slug]])->with(['user', 'blogCategory'])->first();
        if ($blogArticle) {
            $next_record = BlogArticle::where('id', '>', $blogArticle->id)->orderBy('id')->first();
            $previous_record = BlogArticle::where('id', '<', $blogArticle->id)->orderBy('id','desc')->first();

            $blogArticle->increment('views');
            $blogArticleComments = BlogComment::where([['article_id', $blogArticle->id], ['status', 1]])->get();

            $blogArticle->tags = explode(',', $blogArticle->tags);

            return view($this->activeTheme.'.blog.article', [
                'blogArticle' => $blogArticle,
                'blogArticleComments' => $blogArticleComments,
                'next_record' => $next_record,
                'previous_record' => $previous_record,
            ]);
        } else {
            return redirect()->route('blog.index');
        }
    }

    public function comment(Request $request, $slug)
    {
        if (!Auth::check()) {
            quick_alert_error(lang('Login is required to post comments', 'blog'));
            return back();
        }
        if(@settings('blog')->commenting) {
            $blogArticle = BlogArticle::where('slug', $slug)->with('user')->firstOrFail();
            $validator = Validator::make($request->all(), [
                    'comment' => ['required', 'string'],
                ] + ReCaptchaValidation::validate());
            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                quick_alert_error(implode('<br>', $errors));
                return back()->withInput();
            }
            $comment = BlogComment::create([
                'user_id' => user_auth_info()->id,
                'article_id' => $blogArticle->id,
                'comment' => $request->comment,
            ]);
            if ($comment) {
                $title = admin_lang('New comment waiting review');
                $link = route('admin.comments.index');
                admin_notify($title, 'new_comment', $link);
                quick_alert_success(lang('Your comment is under review it will be published soon', 'blog'));
                return back();
            }
        } else {
            quick_alert_error(lang('Commenting is disabled.', 'blog'));
            return back();
        }
    }
}
