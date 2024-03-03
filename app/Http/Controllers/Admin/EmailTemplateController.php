<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\MailTemplate;
use Illuminate\Http\Request;
use Validator;

class EmailTemplateController extends Controller
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

            $mailTemplates = MailTemplate::where('lang', $language->code)->with('language')->get();

            $current_language = $language->name;
            return view('admin.mailtemplates.index', compact('mailTemplates', 'current_language'));
        } else {
            return redirect(url()->current() . '?lang=' . env('DEFAULT_LANGUAGE'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param MailTemplate $mailTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, MailTemplate $mailTemplate)
    {
        return view('admin.mailtemplates.edit', compact('mailTemplate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param MailTemplate $mailTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MailTemplate $mailTemplate)
    {
        $validator = Validator::make($request->all(), [
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        if (!$mailTemplate->undisable()) {
            $request->status = $request->get('status');
        } else {
            $request->status = 1;
        }

        $update = $mailTemplate->update([
            'subject' => $request->subject,
            'status' => $request->status,
            'body' => $request->body,
        ]);
        if ($update) {
            $result = array('success' => true, 'message' => lang('Updated Successfully'));
            return response()->json($result, 200);
        }
    }
}
