<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Validator;
use Illuminate\Http\Request;

// use Illuminate\Support\Facades\Validator as FacadesValidator;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $params = $columns = $order = $totalRecords = $data = array();
            $params = $request;

            //define index of column
            $columns = array(
                'id',
                'name',
                'created_at'
            );

            if (!empty($params['search']['value'])) {
                $q = $params['search']['value'];
                $testimonial = Testimonial::where('name', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            } else {
                $testimonial = Testimonial::orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = Testimonial::count();
            foreach ($testimonial as $row) {
                $rows = array();
                $rows[] = '<td>
                    <div class="d-flex align-items-center">
                        <img class="rounded-circle me-4" width="60" src="' . asset('storage/testimonials/'.$row->image) . '" />
                        <div>
                            <h6 class="mb-1 fw-bold">' . $row->name . '</h6>
                            <p class="text-muted mb-0">' . $row->designation . '</p>
                        </div>
                    </div>
                </td>';
                $rows[] = '<td>
                                <p>' . $row->content . '</p>
                            </td>';
                $rows[] = '<td>
                            <div class="d-flex">
                                <button data-url=" ' . route('admin.testimonials.edit', $row->id) . '" data-toggle="slidePanel" title="' . admin_lang('Edit') . '" class="btn btn-default btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></button>
                            </div>
                            </td>';
                $rows[] = '<td>
                                <div class="checkbox">
                                <input type="checkbox" id="check_' . $row->id . '" value="' . $row->id . '" class="quick-check">
                                <label for="check_' . $row->id . '"><span class="checkbox-icon"></span></label>
                            </div>
                           </td>';
                $rows['DT_RowId'] = $row->id;
                $data[] = $rows;
            }

            $json_data = array(
                "draw" => intval($params['draw']),
                "recordsTotal" => intval($totalRecords),
                "recordsFiltered" => intval($totalRecords),
                "data" => $data // total data array
            );
            return response()->json($json_data, 200);
        }
        $admins = Testimonial::where('name', '!=');
        return view('admin.testimonials.index', ['admins' => $admins]);
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => ['image', 'mimes:png,jpg,jpeg'],
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:255'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }
        if ($request->has('image') && !empty($request->image)) {
            $uploadAvatar = image_upload($request->file('image'), 'storage/testimonials/', '110x110');
        } else {
            $uploadAvatar = 'default.png';
        }
        $create = Testimonial::create([
            'name' => $request->name,
            'content' => $request->content,
            'designation' => $request->designation,
            'image' => $uploadAvatar,
            'translations' => $request->translations,
        ]);
        if ($create) {
            $result = array('success' => true, 'message' => admin_lang('Created Successfully'));
            return response()->json($result, 200);
        }
    }

    public function edit(testimonial $testimonial)
    {
        return view('admin.testimonials.edit')->with('testimonial', $testimonial);
    }

    public function update(Request $request, testimonial $testimonial)
    {
        // For validation
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:255'],

        ]);

        if ($request->has('image') && $request->image != null) {
            $validator = Validator::make($request->all(), [
                'image' => ['image', 'mimes:png,jpg,jpeg'],
            ]);
            if ($testimonial->image == 'default.png') {
                $uploadAvatar = image_upload($request->file('image'), 'storage/testimonials/', '110x110');
            } else {
                $uploadAvatar = image_upload($request->file('image'), 'storage/testimonials/', '110x110', null, $testimonial->image);
            }
        } else {
            $uploadAvatar = $testimonial->image;
        }

        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $update = $testimonial->update([
            'name' => $request->name,
            'designation' => $request->designation,
            'content' => $request->content,
            'image' => $uploadAvatar,
            'translations' => $request->translations,

        ]);
        if ($update) {
            $result = array('success' => true, 'message' => admin_lang('Updated Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\testimonial  $testm
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        $admins = Testimonial::whereIn('id', $ids)->get();
        foreach ($admins as $admin) {
            if ($admin->image != "default.png") {
                remove_file('storage/testimonials/'.$admin->image);
            }
        }
        Testimonial::whereIn('id', $ids)->delete();
        $result = array('success' => true, 'message' => admin_lang('Deleted Successfully'));
        return response()->json($result, 200);
    }
}
