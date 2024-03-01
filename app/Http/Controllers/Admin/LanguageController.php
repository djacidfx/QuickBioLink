<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogArticle;
use App\Models\Feature;
use App\Models\Language;
use App\Models\MailTemplate;
use App\Models\PlanOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;

class LanguageController extends Controller
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
                'position',
                'name',
                'active'
            );

            if(!empty($params['search']['value'])){
                $q = $params['search']['value'];
                $admins = Language::where('name', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }else{
                $admins = Language::orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = Language::count();
            foreach ($admins as $row) {
                $rows = array();

                if($row->active == 1){
                    $status_badge = '<span class="badge bg-success">'.admin_lang('Active').'</span>';
                } else{
                    $status_badge = '<span class="badge bg-danger">'.admin_lang('Disabled').'</span>';
                }

                if ($row->code != env('DEFAULT_LANGUAGE')) {

                    $delete_button = '<form class="d-inline" action="'.route('admin.languages.destroy', $row->id).'" method="POST" onsubmit=\'return confirm("'.admin_lang('Are you sure?').'")\'>
                                    '.method_field('DELETE').'
                                    '.csrf_field().'
                                <button class="btn btn-icon btn-danger ms-1" title="'.admin_lang('Delete').'" data-tippy-placement="top"><i class="icon-feather-trash-2"></i ></button>
                            </form>';

                }else{
                    $delete_button = '';
                }

                $default = env('DEFAULT_LANGUAGE') == $row->code ? admin_lang('(Default)') : "";

                $rows[] = '<td><i class="icon-feather-menu quick-reorder-icon"
                                       title="' . admin_lang('Reorder') . '"></i> <span class="d-none">' . $row->id . '</span></td>';
                $rows[] = '<td><img class="flag" src="'.asset('storage/flags/'.$row->flag).'" alt="'.$row->name.'" width="25" height="25">
                            '.$row->name.'
                            <small class="text-muted">'.$default.'</small>
                            </td>';
                $rows[] = '<td>'.$status_badge.'</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                <a href="#" data-url="'.route('admin.languages.edit', $row->id).'" data-toggle="slidePanel" title="'.admin_lang('Edit').'" class="btn btn-default btn-icon me-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                                <a href="'.route('admin.languages.translates', $row->code).'"
                            class="btn btn-icon btn-info" title="'.admin_lang('Translate').'" data-tippy-placement="top"><i class="icon-feather-globe"></i></a>
                                    '.$delete_button.'
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

        return view('admin.languages.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PlanOption $planoption
     * @return \Illuminate\Http\Response
     */
    public function reorder(Request $request)
    {
        $position = $request->position;
        if (is_array($request->position)) {
            $count = 0;
            foreach($position as $id){
                $update = Language::where('id',$id)->update([
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

    public function create()
    {
        return view('admin.languages.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:150'],
            'flag' => ['required', 'image', 'mimes:png,jpg,jpeg'],
            'code' => ['required', 'string', 'max:10', 'min:2', 'unique:languages'],
            'direction' => ['required', 'integer', 'max:2'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $request->code = trim($request->code);

        if (!array_key_exists($request->code, languages())) {
            $result = array('success' => false, 'message' => admin_lang('Language code not supported'));
            return response()->json($result, 200);
        }
        $createNewLanguageFiles = $this->createNewLanguageFiles($request->code);
        if ($createNewLanguageFiles == "success") {
            $flag = image_upload($request->file('flag'), 'storage/flags/', null, $request->code);
            if ($flag) {
                $stortId = Language::get()->count() + 1;
                $language = Language::create([
                    'name' => $request->name,
                    'flag' => $flag,
                    'code' => $request->code,
                    'direction' => $request->direction,
                    'position' => $stortId,
                ]);
                if ($language) {
                    $mailTemplates = MailTemplate::where('lang', env('DEFAULT_LANGUAGE'))->get();
                    foreach ($mailTemplates as $mailTemplate) {
                        $newMailTemplate = new MailTemplate();
                        $newMailTemplate->lang = $language->code;
                        $newMailTemplate->key = $mailTemplate->key;
                        $newMailTemplate->name = $mailTemplate->name;
                        $newMailTemplate->subject = $mailTemplate->subject;
                        $newMailTemplate->body = $mailTemplate->body;
                        $newMailTemplate->shortcodes = $mailTemplate->shortcodes;
                        $newMailTemplate->status = $mailTemplate->status;
                        $newMailTemplate->save();
                    }
                    if ($request->get('is_default')) {
                        set_env('DEFAULT_LANGUAGE', $language->code);
                    }
                    $result = array('success' => true, 'message' => admin_lang('Created Successfully'));
                    return response()->json($result, 200);
                }
            }
        } else {
            quick_alert_error($createNewLanguageFiles);
            return back();
        }
    }

    public function translate(Request $request, $code, $group = null)
    {
        $language = Language::where('code', $code)->firstOrFail();
        $groups = array_map(function ($file) {
            return pathinfo($file)['filename'];
        }, File::files(base_path('lang/' . $language->code)));
        $active = $group ?? 'general';
        $translates = trans($active, [], $language->code);
        usort($groups, function ($a, $b) {
            if (strpos($a, 'general') !== false && strpos($b, 'general') === false) {
                return -1;
            } else if (strpos($a, 'general') === false && strpos($b, 'general') !== false) {
                return 1;
            } else {
                return 0;
            }
        });
        $defaultLanguage = trans($active, [], env('DEFAULT_LANGUAGE'));
        return view('admin.languages.translate', [
            'active' => $active,
            'groups' => $groups,
            'translates' => $translates,
            'language' => $language,
            'defaultLanguage' => $defaultLanguage,
        ]);
    }

    public function translateUpdate(Request $request, $id)
    {
        $language = Language::where('id', $id)->firstOrFail();
        $languageGroupFile = base_path('lang/' . $language->code . '/' . $request->group . '.php');
        if (!file_exists($languageGroupFile)) {
            quick_alert_error(admin_lang('Language group file not exists'));
            return back();
        }
        $translations = include $languageGroupFile;
        foreach ($request->translates as $key1 => $value1) {
            if (is_array($value1)) {
                foreach ($value1 as $key2 => $value2) {
                    if (!array_key_exists($key2, $value1)) {
                        quick_alert_error(admin_lang('Translations error'));
                        return back();
                    }
                }
            } else {
                if (!array_key_exists($key1, $translations)) {
                    quick_alert_error(admin_lang('Translations error ' . $key1));
                    return back();
                }
            }
        }
        $fileContent = "<?php \n return " . var_export($request->translates, true) . ";";
        File::put($languageGroupFile, $fileContent);
        quick_alert_success(admin_lang('Updated Successfully'));
        return back();
    }

    public function edit(Language $language)
    {
        return view('admin.languages.edit', ['language' => $language]);
    }

    public function update(Request $request, Language $language)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:150'],
            'flag' => ['nullable', 'image', 'mimes:png,jpg,jpeg'],
            'direction' => ['required', 'integer', 'max:2'],
            'active' => ['required', 'boolean'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }
        if (!$request->get('is_default') || !$request->get('active')) {
            if ($language->code == env('DEFAULT_LANGUAGE')) {
                $result = array('success' => false, 'message' => $language->name . ' ' . admin_lang('is the default language'));
                return response()->json($result, 200);
            }
        }
        if ($request->has('flag') && $request->flag != null) {
            $flag = image_upload($request->file('flag'), 'storage/flags/', null, $language->code, $language->flag);
        } else {
            $flag = $language->flag;
        }
        if ($flag) {
            $updateLanguage = $language->update([
                'name' => $request->name,
                'flag' => $flag,
                'direction' => $request->direction,
                'active' => $request->active,
            ]);
            if ($updateLanguage) {
                if ($request->get('is_default') && $request->get('active')) {
                    set_env('DEFAULT_LANGUAGE', $language->code);
                }
                $result = array('success' => true, 'message' => admin_lang('Updated Successfully'));
                return response()->json($result, 200);
            }
        }
    }

    public function destroy(Language $language)
    {
        if ($language->code == env('DEFAULT_LANGUAGE')) {
            quick_alert_error(admin_lang('Default language cannot be deleted'));
            return back();
        }
        $articles = BlogArticle::where('lang', $language->code)->get();
        if ($articles->count() > 0) {
            foreach ($articles as $article) {
                remove_file('storage/blog/articles/'.$article->image);
            }
        }

        $deleteLanguageFiles = File::deleteDirectory(base_path('lang/' . $language->code));
        if ($deleteLanguageFiles) {
            $language->delete();
            quick_alert_success(admin_lang('Deleted Successfully'));
            return back();
        }
    }

    public function export(Request $request, $code)
    {
        $language = Language::where('code', $code)->firstOrFail();
        if (!class_exists('ZipArchive')) {
            quick_alert_error(admin_lang('ZipArchive extension is not enabled'));
            return back();
        }
        $languagePath = base_path('lang/' . $language->code);
        if (!is_dir($languagePath)) {
            quick_alert_error(admin_lang('Language files not exists'));
            return back();
        }
        $zip = new \ZipArchive;
        $zipFile = $language->code . '_language.zip';
        if ($zip->open($zipFile, \ZipArchive::CREATE) === true) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($languagePath), \RecursiveIteratorIterator::LEAVES_ONLY);
            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($languagePath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
            return response()->download($zipFile)->deleteFileAfterSend(true);
        }
    }

    public function import(Request $request, $code)
    {
        $language = Language::where('code', $code)->firstOrFail();
        if (!class_exists('ZipArchive')) {
            quick_alert_error(admin_lang('ZipArchive extension is not enabled'));
            return back();
        }
        $file = $request->file('language_file');
        if ($file->getClientOriginalExtension() != "zip") {
            quick_alert_error(admin_lang('File type not allowed'));
            return back();
        }
        $zip = new \ZipArchive;
        $res = $zip->open($file->getRealPath());
        if ($res === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if (pathinfo($entry, PATHINFO_EXTENSION) != 'php') {
                    quick_alert_error(admin_lang('Invalid language files'));
                    return back();
                }
            }
            $langPath = base_path('lang/' . $language->code);
            remove_directory($langPath);
            make_directory($langPath);
            $zip->extractTo($langPath);
            $zip->close();
            quick_alert_success(admin_lang('Language imported successfully'));
            return back();
        } else {
            quick_alert_error(admin_lang('Failed to import language'));
            return back();
        }
    }

    protected function createNewLanguageFiles($newLanguageCode)
    {
        try {
            $defaultLanguage = env('DEFAULT_LANGUAGE');
            $langPath = base_path('lang/');
            if (!File::exists($langPath . $newLanguageCode)) {
                File::makeDirectory($langPath . $newLanguageCode);
                $defaultLanguageFiles = File::allFiles($langPath . $defaultLanguage);
                foreach ($defaultLanguageFiles as $file) {
                    $newFile = $langPath . $newLanguageCode . '/' . $file->getFilename();
                    if (!File::exists($newFile)) {
                        File::copy($file, $newFile);
                    }
                }
            }
            return "success";
        } catch (\Exception$e) {
            return $e->getMessage();
        }
    }
}
