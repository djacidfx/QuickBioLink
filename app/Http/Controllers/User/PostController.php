<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostLink;
use App\Models\PostOption;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class PostController extends Controller
{
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(subscription()){
            $biopage_limit = subscription()->plan->settings->biopage_limit;
        }else{
            $biopage_limit = 0;
        }
        $biopage_count = Post::where('user_id', user_auth_info()->id)->count();
        if($biopage_limit != 999 && $biopage_count >= $biopage_limit){
            quick_alert_error(lang("Bio page limit exceed upgrade your plan."));
            return back();
        }else{
            return view($this->activeTheme.'.user.posts.create');
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
        if(subscription()){
            $biopage_limit = subscription()->plan->settings->biopage_limit;
        }else{
            $biopage_limit = 0;
        }
        $biopage_count = Post::where('user_id', user_auth_info()->id)->count();
        if($biopage_limit != 999 && $biopage_count >= $biopage_limit){
            quick_alert_error(lang("Bio page limit exceed upgrade your plan."));
            return back();
        }
        $validator = Validator::make($request->all(), [
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'name' => ['required', 'string', 'max:50'],
            'bio' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'unique:posts', 'alpha_dash'],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                quick_alert_error($error);
            }
            return back()->withInput();
        }

        if ($request->has('logo')) {
            $avatar = image_upload($request->file('logo'), 'storage/post/logo/');
        }

        $create = Post::create([
            'user_id' => user_auth_info()->id,
            'title' => $request->name,
            'content' => $request->bio,
            'slug' => !empty($request->slug)
                ? $request->slug
                : SlugService::createSlug(Post::class, 'slug', $request->name),
            'image' => $avatar,
        ]);
        if ($create) {
            /*Adding Default Post Options*/
            PostOption::updatePostOption($create->id, 'biotheme', 'basic');
            PostOption::updatePostOption($create->id, 'bio_credit', 1);
            PostOption::updatePostOption($create->id, 'bio_share', 1);
            PostOption::updatePostOption($create->id, 'seo_title', $request->name);
            PostOption::updatePostOption($create->id, 'seo_desc', $request->bio);

            quick_alert_success(lang('Created Successfully'));
            return redirect()->route('biolinks.edit', $create->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function publicView($slug)
    {
        $post = Post::where('slug', $slug)->first();
        if ($post) {
            if(subscription($post->user)) {

                $post->increment('views');
                $postOptions = post_options($post->id);
                if (empty($postOptions->biotheme)) {
                    $postOptions->biotheme = 'basic';
                }

                $PostLink = PostLink::where([['post_id', $post->id], ['active', 1]])->orderBy('position', 'ASC')->get();

                $renderer = new ImageRenderer(
                    new RendererStyle(200),
                    new SvgImageBackEnd()
                );
                $writer = new Writer($renderer);
                $qr_image = $writer->writeString(url()->current());

                return view('post_templates.' . $postOptions->biotheme . '.index', [
                    'post' => $post,
                    'bioLinks' => $PostLink,
                    'theme' => $postOptions->biotheme,
                    'postOptions' => $postOptions,
                    'qr_image' => $qr_image
                ]);
            }
        }

        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post $Post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $biolink)
    {
        if($biolink->user_id == user_auth_info()->id){
            $postOptions = post_options($biolink->id);
            $PostLink = PostLink::where('post_id', $biolink->id)->orderBy('position', 'ASC')->get();

            $templates = [];
            $temPaths = array_filter(glob(base_path().'/resources/views/post_templates/*'), 'is_dir');
            foreach ($temPaths as $key => $temp) {
                $arr = explode('/', $temp);
                $tempname = end($arr);
                $templates[$key]['name'] = $tempname;
                $templates[$key]['image'] = asset('post_templates/'.$tempname.'/preview.png');
            }

            return view($this->activeTheme.'.user.posts.edit', [
                'user' => user_auth_info(),
                'post' => $biolink,
                'bioLinks' => $PostLink,
                'postOptions' => $postOptions,
                'templates' => $templates
            ]);
        }else{
            return abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $biolink)
    {
        if($biolink->user_id == user_auth_info()->id){
            if($request->has('type')){
                if($request->type == 'settings'){
                    $validator = Validator::make($request->all(), [
                        'cover' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
                        'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
                        'name' => ['required', 'string', 'max:50'],
                        'bio' => ['required', 'string', 'max:255'],
                        'slug' => ['nullable', 'alpha_dash', 'unique:posts,slug,' . $biolink->id],
                    ]);
                    $errors = [];
                    if ($validator->fails()) {
                        foreach ($validator->errors()->all() as $error) {
                            $errors[] = $error;
                        }
                        $result = array('success' => false, 'message' => implode('<br>', $errors));
                        return response()->json($result, 200);
                    }
                    $postOptions = post_options($biolink->id);
                    $coverImage = @$postOptions->cover_image;

                    if ($request->has('cover') && !empty($request->cover)) {
                        $coverImage = image_upload($request->file('cover'), 'storage/post/logo/', null, null, $coverImage);
                    }

                    if ($request->has('logo') && !empty($request->logo)) {
                        $avatar = image_upload($request->file('logo'), 'storage/post/logo/', null, null, $biolink->image);
                    }else{
                        $avatar = $biolink->image;
                    }

                    $slug = !empty($request->slug) ? $request->slug : $biolink->slug;
                    $update = $biolink->update([
                        'title' => $request->name,
                        'content' => $request->bio,
                        'slug' => $slug,
                        'image' => $avatar,
                    ]);
                    if($update){
                        PostOption::updatePostOption($biolink->id, 'cover_image', $coverImage);
                        PostOption::updatePostOption($biolink->id, 'seo_title', $request->seo_title);
                        PostOption::updatePostOption($biolink->id, 'seo_desc', $request->seo_desc);

                        $result = array('success' => true, 'message' => lang('Updated Successfully'));
                        return response()->json($result, 200);
                    }else{
                        $result = array('success' => false, 'message' => lang('Something went wrong please try again'));
                        return response()->json($result, 200);
                    }
                }
                elseif($request->type == 'design'){
                    $bio_credit = ($request->has('bio_credit'))? 1 : 0;
                    $bio_share = ($request->has('bio_share'))? 1 : 0;

                    PostOption::updatePostOption($biolink->id, 'biotheme', $request->biotheme);
                    PostOption::updatePostOption($biolink->id, 'bio_credit', $bio_credit);
                    PostOption::updatePostOption($biolink->id, 'bio_share', $bio_share);

                    $result = array('success' => true, 'message' => lang('Updated Successfully'));
                    return response()->json($result, 200);
                }
            }
        }

        $result = array('error' => true, 'message' => lang('Unexpected Error'));
        return response()->json($result, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $biolink)
    {
        if($biolink->user_id == user_auth_info()->id){
            $links = PostLink::where('post_id', $biolink->id)->get();
            foreach($links as $link){
                if($link->type == "link"){
                    if ($link->settings->logo != '') {
                        remove_file('storage/post/biolink/'.$link->settings->logo);
                    }
                }
                $link->delete();
            }

            remove_file('storage/post/logo/'.$biolink->image);
            $biolink->delete();
        }
        quick_alert_success(lang('Deleted Successfully'));
        return redirect()->route('biolinks.index', $biolink->id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Plan $plan
     * @return \Illuminate\Http\Response
     */
    public function reorder(Request $request,Post $post)
    {
        $position = $request->position;
        if (is_array($request->position)) {
            $count = 0;
            foreach($position as $id){
                $update = PostLink::where('id',$id)->update([
                    'position' => $count,
                ]);

                $count++;
            }
            if ($update) {
                $result = array('success' => true, 'message' => admin_lang('Updated Successfully'));
                return response()->json($result, 200);
            }
        }

        $result = array('success' => true, 'message' => admin_lang('Updated Successfully'));
        return response()->json($result, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addheader(Request $request,Post $post)
    {
        if($post->user_id == user_auth_info()->id){
            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:255']
            ]);
            $errors = [];
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $settings = array();
            $settings['title'] = $request->title;

            $create = PostLink::create([
                'post_id' => $post->id,
                'type' => "header",
                'settings' => $settings
            ]);
            if ($create) {
                $result = array(
                    'success' => true,
                    'message' => lang('Added Successfully'),
                    'id' => $create->id,
                    'type' => "header",
                    'settings' => $settings,
                );
                return response()->json($result, 200);
            }
        }else{
            $result = array('success' => false, 'message' => lang('Unexpected Error'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function editHeader(Request $request,Post $post)
    {
        if($post->user_id == user_auth_info()->id){

            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:255']
            ]);
            $errors = [];
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $settings = array();
            $settings['title'] = $request->title;
            $active = ($request->has('active'))? 0 : 1;
            $update = PostLink::where([['id', $request->id], ['post_id', $post->id]])->update(['settings' => $settings, 'active' => $active]);
            if($update){
                $result = array(
                    'success' => true,
                    'message' => lang('Updated Successfully'),
                    'type' => "header",
                    'id' => $request->id,
                    'active' => $active,
                    'settings' => $settings,
                );
                return response()->json($result, 200);
            }else{
                $result = array('success' => false, 'message' => lang('Something went wrong please try again'));
                return response()->json($result, 200);
            }
        }else{
            $result = array('success' => false, 'message' => lang('Unexpected Error'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addlink(Request $request, Post $post)
    {
        if($post->user_id == user_auth_info()->id) {

            if(subscription()){
                $biolink_limit = subscription()->plan->settings->biolink_limit;
            }else{
                $biolink_limit = 0;
            }
            $biolink_count = PostLink::where([['type', 'link'], ['post_id', $post->id]])->count();
            if($biolink_limit != 999 && $biolink_count >= $biolink_limit){
                $result = array('success' => false, 'message' => lang("Add link limit exceed upgrade your plan."));
                return response()->json($result, 200);
            }

            $validator = Validator::make($request->all(), [
                'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
                'title' => ['required', 'string', 'max:255'],
                'url' => ['required', 'string', 'max:255']
            ]);
            $errors = [];
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $avatar = "";
            if ($request->has('logo') && !empty($request->logo)) {
                $avatar = image_upload($request->file('logo'), 'storage/post/biolink/');
            }

            $settings = array();
            $settings['title'] = $request->title;
            $settings['url'] = $request->url;
            $settings['logo'] = $avatar;
            $settings['highlight'] = ($request->has('highlight') ? 1 : 0);

            $create = PostLink::create([
                'post_id' => $post->id,
                'type' => "link",
                'settings' => $settings
            ]);
            if ($create) {
                $result = array(
                    'success' => true,
                    'message' => lang('Added Successfully'),
                    'id' => $create->id,
                    'type' => "link",
                    'settings' => $settings,
                );
                return response()->json($result, 200);
            }
        }else{
            $result = array('success' => false, 'message' => lang('Unexpected Error'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function editLink(Request $request,Post $post)
    {
        if($post->user_id == user_auth_info()->id){
            $validator = Validator::make($request->all(), [
                'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
                'title' => ['required', 'string', 'max:255'],
                'url' => ['required', 'string', 'max:255']
            ]);
            $errors = [];
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $postlink = PostLink::where([['id', $request->id], ['post_id', $post->id]])->first();
            if ($request->has('logo') && !empty($request->logo)) {
                $avatar = image_upload($request->file('logo'), 'storage/post/biolink/', null, null, $postlink->settings->logo);
            }else{
                $avatar = $postlink->settings->logo;
            }

            $settings = array();
            $settings['title'] = $request->title;
            $settings['url'] = $request->url;
            $settings['logo'] = $avatar;
            $settings['highlight'] = ($request->has('highlight') ? 1 : 0);
            $active = ($request->has('active')) ? 0 : 1;

            $update = $postlink->update(['settings' => $settings, 'active' => $active]);
            if($update){
                $result = array(
                    'success' => true,
                    'message' => lang('Updated Successfully'),
                    'type' => "link",
                    'id' => $request->id,
                    'active' => $active,
                    'settings' => $settings,
                );
                return response()->json($result, 200);
            }else{
                $result = array('success' => false, 'message' => lang('Something went wrong please try again'));
                return response()->json($result, 200);
            }
        }else{
            $result = array('success' => false, 'message' => lang('Unexpected Error'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addsocial(Request $request, Post $post)
    {
        if($post->user_id == user_auth_info()->id) {
            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:10'],
                'url' => ['required', 'string', 'max:255']
            ]);
            $errors = [];
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $settings = array();
            $settings['title'] = $request->title;
            $settings['url'] = $request->url;

            $create = PostLink::create([
                'post_id' => $post->id,
                'type' => "social",
                'settings' => $settings
            ]);
            if ($create) {
                $result = array(
                    'success' => true,
                    'message' => lang('Added Successfully'),
                    'id' => $create->id,
                    'type' => "social",
                    'settings' => $settings,
                );
                return response()->json($result, 200);
            }
        }else{
            $result = array('success' => false, 'message' => lang('Unexpected Error'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function editSocial(Request $request,Post $post)
    {
        if($post->user_id == user_auth_info()->id){
            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:10'],
                'url' => ['required', 'string', 'max:255']
            ]);
            $errors = [];
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $settings = array();
            $settings['title'] = $request->title;
            $settings['url'] = $request->url;
            $active = ($request->has('active'))? 0 : 1;
            $update = PostLink::where([['id', $request->id], ['post_id', $post->id]])->update(['settings' => $settings, 'active' => $active]);
            if($update){
                $result = array(
                    'success' => true,
                    'message' => lang('Updated Successfully'),
                    'type' => "social",
                    'id' => $request->id,
                    'active' => $active,
                    'settings' => $settings,
                );
                return response()->json($result, 200);
            }else{
                $result = array('success' => false, 'message' => lang('Something went wrong please try again'));
                return response()->json($result, 200);
            }
        }else{
            $result = array('success' => false, 'message' => lang('Unexpected Error'));
            return response()->json($result, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteLink(Request $request,Post $post)
    {
        if($post->user_id == user_auth_info()->id){
            $link = PostLink::where([['id', $request->id], ['post_id', $post->id]])->first();
            if($link->type == "link"){
                if ($link->settings->logo != '') {
                    remove_file('storage/post/biolink/'.$link->settings->logo);
                }
            }
            $link->delete();
            $result = array('success' => true, 'message' => lang('Deleted Successfully'));
            return response()->json($result, 200);
        }

    }
}
