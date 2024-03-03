<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $params = $columns = $order = $totalRecords = $data = array();
            $params = $request;

            //define index of column
            $columns = array(
                'id',
                'position',
                'status',
                'updated_at'
            );

            if(!empty($params['search']['value'])){
                $q = $params['search']['value'];
                $advertisements = Advertisement::where('key','!=','head_code')
                    ->OrWhere('id', 'like', '%' . $q . '%')
                    ->OrWhere('position', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }else{
                $advertisements = Advertisement::where('key','!=','head_code')
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();

            }

            $totalRecords = Advertisement::count();
            foreach ($advertisements as $row) {

                if ($row->status){
                    $status_badge = '<span class="badge bg-success">'.lang('Enabled').'</span>';
                }else{
                    $status_badge = '<span class="badge bg-danger">'.lang('Disabled').'</span>';
                }

                $rows = array();
                $rows[] = '<td>'.$row->id.'</td>';
                $rows[] = '<td>'.$row->position.'</td>';
                $rows[] = '<td>'.$status_badge.'</td>';
                $rows[] = '<td>'.date_formating($row->updated_at).'</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <a href="#" data-url="'.route('admin.advertisements.edit', $row->id).'" data-toggle="slidePanel" title="'.lang('Edit').'" class="btn btn-default btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
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

        $headAd = Advertisement::where('key', 'head_code')->first();
        return view('admin.advertisements.index', compact('headAd'));
    }

    /**
     * Display edit form
     *
     * @param Advertisement $advertisement
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Advertisement $advertisement)
    {
        return view('admin.advertisements.edit', compact('advertisement'));
    }

    /**
     * Update a resource
     *
     * @param Request $request
     * @param Advertisement $advertisement
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function update(Request $request, Advertisement $advertisement)
    {
        $update = $advertisement->update([
            'status' => $request->status,
            'code' => $request->code,
        ]);
        if ($update) {
            $result = array('success' => true, 'message' => lang('Updated Successfully'));
            return response()->json($result, 200);
        }
    }
}
