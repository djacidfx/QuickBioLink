<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class DashboardController extends Controller
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
        $user = user_auth_info();
        $posts = Post::where('user_id', $user->id)->get();

        return view($this->activeTheme.'.user.dashboard', compact('user', 'posts'));
    }
}
