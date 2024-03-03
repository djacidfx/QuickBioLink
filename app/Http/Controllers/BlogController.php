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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    /**
     * Display the page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $page_limit = settings('blog')->page_limit ?? 8;

        if (request()->has('search')) {
            $quary = request('search');
            $blogs = BlogArticle::where([['title', 'like', '%' . $quary . '%'], ['lang', get_lang()]])
                ->OrWhere([['content', 'like', '%' . $quary . '%'], ['lang', get_lang()]])
                ->OrWhere([['short_description', 'like', '%' . $quary . '%'], ['lang', get_lang()]])
                ->OrWhere([['tags', 'like', '%' . $quary . '%'], ['lang', get_lang()]])
                ->orderbyDesc('id')
                ->paginate($page_limit);

            $blogs->appends(['search' => $quary]);
        } else {
            $blogs = BlogArticle::where('lang', get_lang())->orderbyDesc('id')->paginate($page_limit);
        }

        return view($this->activeTheme . '.blog.index', compact('blogs'));
    }

    /**
     * Display single blog page
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function single($slug)
    {
        $blog = BlogArticle::where([['lang', get_lang()], ['slug', $slug]])->with(['user', 'blogCategory'])->first();
        if ($blog) {
            $next_record = BlogArticle::where('id', '>', $blog->id)->orderBy('id')->first();
            $previous_record = BlogArticle::where('id', '<', $blog->id)->orderBy('id', 'desc')->first();

            $blog->increment('views');

            $blogComments = BlogComment::where([['article_id', $blog->id], ['status', 1]])->get();

            $blog->tags = explode(',', $blog->tags);

            return view($this->activeTheme . '.blog.single', compact(
                'blog',
                'blogComments',
                'next_record',
                'previous_record',
            ));
        } else {
            abort(404);
        }
    }

    /**
     * Display the tag page
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function tag($slug)
    {
        $page_limit = settings('blog')->page_limit ?? 8;
        $tag = BlogCategory::where([['tags', 'like', '%' . $slug . '%'], ['lang', get_lang()]])->first();
        if ($tag) {
            $blogs = BlogArticle::where([['tags', 'like', '%' . $slug . '%'], ['lang', get_lang()]])
                ->orderbyDesc('id')
                ->paginate($page_limit);
            $blogTag = $slug;

            return view($this->activeTheme . '.blog.tag', compact(
                'blogTag',
                'blogs'
            ));
        } else {
            abort(404);
        }
    }

    /**
     * Display the category page
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function category($slug)
    {
        $page_limit = settings('blog')->page_limit ?? 8;
        $blogCategory = BlogCategory::where([['lang', get_lang()], ['slug', $slug]])->first();
        if ($blogCategory) {
            $blogCategory->increment('views');

            $blogs = BlogArticle::where('category_id', $blogCategory->id)->orderbyDesc('id')->paginate($page_limit);

            return view($this->activeTheme . '.blog.category', compact(
                'blogCategory',
                'blogs'
            ));
        } else {
            abort(404);
        }
    }

    /**
     * Handle comment
     *
     * @param Request $request
     * @param $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function comment(Request $request, $slug)
    {
        if (!Auth::check()) {
            quick_alert_error(lang('Please login to post a comment.'));
            return back();
        }

        /* Check if comment enabled */
        if (@settings('blog')->commenting) {

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

            $blog = BlogArticle::where('slug', $slug)->with('user')->firstOrFail();

            $comment = BlogComment::create([
                'article_id' => $blog->id,
                'user_id' => user_auth_info()->id,
                'comment' => $request->comment,
            ]);

            if ($comment) {

                /* add admin notification */
                $title = lang('New comment waiting for review');
                admin_notify($title, 'new_comment', route('admin.comments.index'));

                quick_alert_success(lang('Comment is posted, wait for the reviewer to approve.'));
                return back();
            }
        } else {
            quick_alert_error(lang('Unexpected error'));
            return back();
        }
    }
}
