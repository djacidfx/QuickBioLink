<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogArticle;
use App\Models\BlogCategory;
use App\Models\Language;
use App\Models\User;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $params = $columns = $order = $totalRecords = $data = array();
            $params = $request;

            //define index of column
            $columns = array(
                'id',
                'title',
                'lang',
                '','','','',
                'created_at'
            );
            if ($request->has('lang')) {
                $language = Language::where('code', $request->lang)->firstOrFail();
            }else{
                $language = Language::where('code', env('DEFAULT_LANGUAGE'))->firstOrFail();
            }
            if(!empty($params['search']['value'])){
                $q = $params['search']['value'];
                $articles = BlogArticle::where('lang', $language->code)->with(['blogCategory', 'user', 'language'])
                    ->withCount('comments')
                    ->where('id', 'like', '%' . $q . '%')
                    ->OrWhere('title', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }else{
                $articles = BlogArticle::where('lang', $language->code)
                    ->with(['blogCategory', 'user', 'language'])
                    ->withCount('comments')
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = BlogArticle::count();
            foreach ($articles as $row) {

                $rows = array();
                $rows[] = '<td>'.$row->id.'</td>';
                $rows[] = '<td><span class="text-uppercase">'.$row->language->code.'</span></td>';
                $rows[] = '<td>
                                <a class="text-body" href="'.route('admin.articles.edit', $row->id).'">'.text_shorting($row->title, 30).'</a>
                            </td>';
                $rows[] = '<td><span class="badge bg-secondary">'.$row->blogCategory->name.'</span></td>';
                $rows[] = '<td><span class="badge bg-secondary">'.$row->comments_count.'</span></td>';
                $rows[] = '<td><span class="badge bg-secondary">'.$row->views.'</span></td>';
                $rows[] = '<td>'.date_formating($row->created_at).'</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <a href="'.route('blog.article', $row->slug).'" title="'.admin_lang('View').'" data-tippy-placement="top" class="btn btn-default btn-icon me-1" target="_blank"><i class="icon-feather-eye"></i></a>
                                    <a href="'.route('admin.comments.index') . '?article_id=' . $row->id.'" title="'.admin_lang('Comments').'" data-tippy-placement="top" class="btn btn-default btn-icon me-1"><i class="icon-feather-message-circle"></i></a>
                                    <a href="'.route('admin.articles.edit', $row->id).'" title="'.admin_lang('Edit').'" class="btn btn-default btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                                </div>
                            </td>';
                $rows[] = '<td>
                                <div class="checkbox">
                                <input type="checkbox" id="check_'.$row->id.'" value="'.$row->id.'" class="quick-check">
                                <label for="check_'.$row->id.'"><span class="checkbox-icon"></span></label>
                            </div>
                           </td>';
                $rows['DT_RowId'] = $row->id;
                $data[] = $rows;
            }

            $json_data = array(
                "draw"            => intval( $params['draw'] ),
                "recordsTotal"    => intval( $totalRecords ),
                "recordsFiltered" => intval($totalRecords),
                "data"            => $data   // total data array
            );
            return response()->json($json_data, 200);
        }

        if ($request->has('lang')) {
            $language = Language::where('code', $request->lang)->firstOrFail();
            return view('admin.blog.articles.index', [
                'active' => $language->name,
                'lang' => $request->lang
            ]);
        } else {
            return redirect(url()->current() . '?lang=' . env('DEFAULT_LANGUAGE'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = BlogCategory::get();
        return view('admin.blog.articles.create', ['categories' => $categories]);
    }

    /**
     * Get categories by lang
     *
     * @return \Illuminate\Http\Response as JSON
     */
    public function getCategories($lang)
    {
        $categories = BlogCategory::where('lang', $lang)->pluck("name", "id");
        if ($categories->count() > 0) {
            return response()->json($categories);
        } else {
            return response()->json(['info' => admin_lang('No categories on this language')]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lang' => ['required', 'string', 'max:3'],
            'title' => ['required', 'string', 'max:255', 'min:2'],
            'slug' => ['nullable', 'unique:blog_articles', 'alpha_dash'],
            'image' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'category' => ['required', 'numeric'],
            'tags' => ['nullable', 'string'],
            'content' => ['required'],
            'short_description' => ['required', 'string', 'max:200', 'min:2'],
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back()->withInput();
        }
        $category = BlogCategory::where('id', $request->category)->first();
        if ($category == null) {
            quick_alert_error(admin_lang('Something went wrong please try again'));
            return back();
        }
        $lang = Language::where('code', $request->lang)->first();
        if ($lang == null) {
            quick_alert_error(admin_lang('Language not exists'));
            return back();
        }
        if ($category->lang != $lang->code) {
            quick_alert_error(admin_lang('Category and article must be in the same language'));
            return back();
        }
        $image = image_upload($request->file('image'), 'storage/blog/articles/', '1280x720');
        if ($image) {
            $create = BlogArticle::create([
                'lang' => $lang->code,
                'user_id' => user_auth_info()->id,
                'title' => $request->title,
                'slug' => !empty($request->slug)
                            ? $request->slug
                            : SlugService::createSlug(BlogArticle::class, 'slug', $request->title),
                'image' => $image,
                'category_id' => $category->id,
                'tags' => Str::lower($request->tags),
                'content' => $request->content,
                'short_description' => $request->short_description,
            ]);
            if ($create) {
                quick_alert_success(admin_lang('Created Successfully'));
                return redirect(route('admin.articles.index') . '?lang=' . $create->lang);
            }
        } else {
            quick_alert_error(admin_lang('Image upload failed'));
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BlogArticle  $article
     * @return \Illuminate\Http\Response
     */
    public function show(BlogArticle $article)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BlogArticle  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(BlogArticle $article)
    {
        $categories = BlogCategory::where('lang', $article->lang)->orderbyDesc('id')->get();
        return view('admin.blog.articles.edit', ['article' => $article, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BlogArticle  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BlogArticle $article)
    {
        $validator = Validator::make($request->all(), [
            'lang' => ['required', 'string', 'max:3'],
            'title' => ['required', 'string', 'max:255', 'min:2'],
            'slug' => ['nullable', 'alpha_dash', 'unique:blog_articles,slug,' . $article->id],
            'image' => ['mimes:png,jpg,jpeg', 'max:2048'],
            'category' => ['required', 'numeric'],
            'tags' => ['nullable', 'string'],
            'content' => ['required'],
            'short_description' => ['required', 'string', 'max:200', 'min:2'],
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back();
        }
        $category = BlogCategory::where('id', $request->category)->first();
        if ($category == null) {
            quick_alert_error(admin_lang('Something went wrong please try again'));
            return back();
        }
        $lang = Language::where('code', $request->lang)->first();
        if ($lang == null) {
            quick_alert_error(admin_lang('Language not exists'));
            return back();
        }
        if ($category->lang != $lang->code) {
            quick_alert_error(admin_lang('Category and article must be in the same language'));
            return back();
        }
        if ($request->has('image')) {
            $image = image_upload($request->file('image'), 'storage/blog/articles/', '1280x720', null, $article->image);
        } else {
            $image = $article->image;
        }
        if ($image) {
            $update = $article->update([
                'lang' => $lang->code,
                'title' => $request->title,
                'slug' => !empty($request->slug)
                    ? $request->slug
                    : SlugService::createSlug(BlogArticle::class, 'slug', $request->title),
                'image' => $image,
                'category_id' => $category->id,
                'tags' => Str::lower($request->tags),
                'content' => $request->content,
                'short_description' => $request->short_description,
            ]);
            if ($update) {
                quick_alert_success(admin_lang('Updated Successfully'));
                return back();
            }
        } else {
            quick_alert_error(admin_lang('Image upload failed'));
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BlogArticle  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(BlogArticle $article)
    {
        remove_file('storage/blog/articles/'.$article->image);
        $article->delete();
        quick_alert_success(admin_lang('Deleted Successfully'));
        return back();
    }

    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        $articles = BlogArticle::whereIn('id',$ids)->get();
        foreach($articles as $article){
            remove_file('storage/blog/articles/'.$article->image);
        }
        BlogArticle::whereIn('id',$ids)->delete();
        $result = array('success' => true, 'message' => admin_lang('Deleted Successfully'));
        return response()->json($result, 200);
    }
}
