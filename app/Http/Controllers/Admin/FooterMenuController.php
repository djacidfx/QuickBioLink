<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\NavbarMenu;
use Illuminate\Http\Request;
use Validator;

class FooterMenuController extends Controller
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

            $links = NavbarMenu::where('type', 'footer')
                ->where('lang', $language->code)
                ->whereNull('parent_id')
                ->with(['children' => function ($query) {
                    $query->byOrder();
                }])->byOrder()->get();

            $current_language = $language->name;
            return view('admin.footerMenu.index', compact('links', 'current_language'));
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
        return view('admin.footerMenu.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'link' => ['required', 'string'],
            'lang' => ['required', 'string', 'max:3', 'exists:languages,code'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $count = NavbarMenu::all()->count();

        $menu = NavbarMenu::create([
            'name' => $request->name,
            'link' => $request->link,
            'order' => $count + 1,
            'type' => 'footer',
            'lang' => $request->lang,
        ]);

        if ($menu) {
            $result = array('success' => true, 'message' => lang('Created Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\NavbarMenu $footerMenu
     */
    public function show(NavbarMenu $footerMenu)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\NavbarMenu $footerMenu
     * @return \Illuminate\Http\Response
     */
    public function edit(NavbarMenu $footerMenu)
    {
        return view('admin.footerMenu.edit', compact('footerMenu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\NavbarMenu $footerMenu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NavbarMenu $footerMenu)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'link' => ['required', 'string'],
            'lang' => ['required', 'string', 'max:3', 'exists:languages,code'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $menu = $footerMenu->update([
            'name' => $request->name,
            'link' => $request->link,
            'lang' => $request->lang,
        ]);
        if ($menu) {
            $result = array('success' => true, 'message' => lang('Updated Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\NavbarMenu $footerMenu
     * @return \Illuminate\Http\Response
     */
    public function destroy(NavbarMenu $footerMenu)
    {
        $footerMenu->delete();
        quick_alert_success(lang('Deleted Successfully'));
        return back();
    }

    /**
     *  Update menu order
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function nestable(Request $request)
    {
        if ($request->has('ids') && !is_null($request->ids)) {

            $data = json_decode($request->ids, true);
            $i = 1;
            foreach ($data as $ids) {

                $menu = NavbarMenu::find($ids['id']);
                $menu->update([
                    'order' => $i,
                    'parent_id' => null,
                ]);

                if (isset($ids['children'])) {
                    $j = 1;
                    foreach ($ids['children'] as $children) {
                        $menu = NavbarMenu::find($children['id']);
                        $menu->update([
                            'order' => $j,
                            'parent_id' => $ids['id'],
                        ]);
                        $j++;
                    }
                }
                $i++;
            }
        }
        quick_alert_success(lang('Updated Successfully'));
        return back();
    }
}
