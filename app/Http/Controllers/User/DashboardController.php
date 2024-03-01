<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    protected function user()
    {
        return user_auth_info();
    }

    public function index()
    {
        $posts = Post::where('user_id', user_auth_info()->id)->get();
        return view($this->activeTheme.'.user.dashboard', ['user' => $this->user(), 'posts' => $posts]);
    }
}
