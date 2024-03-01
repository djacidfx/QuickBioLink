<?php

namespace App\Http\Controllers\Admin\Navigation;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\NavbarMenu;
use Illuminate\Http\Request;
use Validator;

class NavbarMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('lang')) {
            $language = Language::where('code', $request->lang)->firstOrFail();
            $navbarMenuLinks = NavbarMenu::where('lang', $language->code)
                ->where('type', 'header')
                ->whereNull('parent_id')
                ->with(['children' => function ($query) {
                    $query->byOrder();
                }])->byOrder()->get();
            return view('admin.navigation.navbarMenu.index', [
                'navbarMenuLinks' => $navbarMenuLinks,
                'active' => $language->name,
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
        return view('admin.navigation.navbarMenu.create');
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
            'lang' => ['required', 'string', 'max:3', 'exists:languages,code'],
            'name' => ['required', 'string', 'max:100'],
            'link' => ['required', 'string'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }
        $countLinks = NavbarMenu::all()->count();
        $navbarMenu = NavbarMenu::create([
            'lang' => $request->lang,
            'name' => $request->name,
            'link' => $request->link,
            'order' => $countLinks + 1,
            'type' => 'header'
        ]);
        if ($navbarMenu) {
            $result = array('success' => true, 'message' => admin_lang('Created Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     *  nestable menu
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function nestable(Request $request)
    {
        if ($request->has('ids') && !is_null($request->ids)) {
            $data = json_decode($request->ids, true);
            $i = 1;
            foreach ($data as $arr) {
                $menu = NavbarMenu::find($arr['id']);
                $menu->update([
                    'order' => $i,
                    'parent_id' => null,
                ]);
                if (isset($arr['children'])) {
                    $sub_i = 1;
                    foreach ($arr['children'] as $children) {
                        $menu = NavbarMenu::find($children['id']);
                        $menu->update([
                            'order' => $sub_i,
                            'parent_id' => $arr['id'],
                        ]);
                        $sub_i++;
                    }
                }
                $i++;
            }
        }
        quick_alert_success(admin_lang('Updated Successfully'));
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NavbarMenu  $navbarMenu
     * @return \Illuminate\Http\Response
     */
    public function show(NavbarMenu $navbarMenu)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NavbarMenu  $navbarMenu
     * @return \Illuminate\Http\Response
     */
    public function edit(NavbarMenu $navbarMenu)
    {
        return view('admin.navigation.navbarMenu.edit', ['navbarMenu' => $navbarMenu]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NavbarMenu  $navbarMenu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NavbarMenu $navbarMenu)
    {
        $validator = Validator::make($request->all(), [
            'lang' => ['required', 'string', 'max:3', 'exists:languages,code'],
            'name' => ['required', 'string', 'max:100'],
            'link' => ['required', 'string'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }
        $updateMenu = $navbarMenu->update([
            'lang' => $request->lang,
            'name' => $request->name,
            'link' => $request->link,
        ]);
        if ($updateMenu) {
            $result = array('success' => true, 'message' => admin_lang('Updated Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NavbarMenu  $navbarMenu
     * @return \Illuminate\Http\Response
     */
    public function destroy(NavbarMenu $navbarMenu)
    {
        $navbarMenu->delete();
        quick_alert_success(admin_lang('Deleted Successfully'));
        return back();
    }
}
