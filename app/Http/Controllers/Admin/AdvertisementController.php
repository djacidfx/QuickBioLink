<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $params = $columns = $order = $totalRecords = $data = array();
            $params = $request;

            //define index of column
            $columns = array(
                'id',
                'position',
                'size',
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
                    $status_badge = '<span class="badge bg-success">'.admin_lang('Enabled').'</span>';
                }else{
                    $status_badge = '<span class="badge bg-danger">'.admin_lang('Disabled').'</span>';
                }

                $rows = array();
                $rows[] = '<td>'.$row->id.'</td>';
                $rows[] = '<td>'.$row->position.'</td>';
                $rows[] = '<td>'.$row->size.'</td>';
                $rows[] = '<td>'.$status_badge.'</td>';
                $rows[] = '<td>'.date_formating($row->updated_at).'</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <a href="#" data-url="'.route('admin.advertisements.edit', $row->id).'" data-toggle="slidePanel" title="'.admin_lang('Edit').'" class="btn btn-default btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
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
        return view('admin.advertisements.index', [
            'headAd' => $headAd
        ]);
    }

    public function edit(Advertisement $advertisement)
    {
        return view('admin.advertisements.edit', ['advertisement' => $advertisement]);
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        if ($request->has('status') && is_null($request->code)) {
            $result = array('success' => false, 'message' => admin_lang('Advertisement code cannot be empty'));
            return response()->json($result, 200);
        }
        $update = $advertisement->update([
            'code' => $request->code,
            'status' => $request->status,
        ]);
        if ($update) {
            $result = array('success' => true, 'message' => admin_lang('Updated Successfully'));
            return response()->json($result, 200);
        }
    }
}
