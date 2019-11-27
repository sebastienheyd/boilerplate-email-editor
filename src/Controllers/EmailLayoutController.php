<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Mail;
use Sebastienheyd\BoilerplateEmailEditor\Facades\Blade;
use Sebastienheyd\BoilerplateEmailEditor\Mail\Preview;
use Sebastienheyd\BoilerplateEmailEditor\Models\EmailLayout;

class EmailLayoutController extends Controller
{
    /**
     * EmailLayoutController constructor.
     */
    public function __construct()
    {
        $this->middleware('ability:admin,emaileditor_layout_crud');
    }

    /**
     * Display a listing of email layouts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('boilerplate-email-editor::layout.index');
    }

    /**
     * Get listing of email layouts for datatable.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function datatable()
    {
        return Datatables::of(EmailLayout::select('*'))
            ->rawColumns(['actions'])
            ->editColumn('actions', function ($layout) {
                $b = '<a href="'.route('emaileditor.layout.show', $layout->id).
                    '" class="btn btn-default btn-sm mrs" target="_blank"><i class="fa fa-eye"></i></a>';
                $b .= '<a href="'.route('emaileditor.layout.edit', $layout->id).
                    '" class="btn btn-primary btn-sm mrs"><i class="fa fa-pencil"></i></a>';
                $b .= '<a href="'.route('emaileditor.layout.destroy', $layout->id).
                    '" class="btn btn-danger btn-sm destroy"><i class="fa fa-trash"></i></a>';

                return $b;
            })->make(true);
    }

    /**
     * Show the form for creating a new layout.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $defaultContent = file_get_contents(view('boilerplate-email-editor::layout.default')->getPath());
        $userEmail = $request->user()->email;

        return view('boilerplate-email-editor::layout.create', compact('defaultContent', 'userEmail'));
    }

    /**
     * Store a newly created layout in storage.
     *
     * @param Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'label'   => 'required',
            'content' => 'required',
        ], [], [
            'label'   => __('boilerplate-email-editor::layout.label'),
            'content' => __('boilerplate-email-editor::layout.content'),
        ]);

        $emailLayout = EmailLayout::create($request->all());

        return redirect()->route('emaileditor.layout.edit', $emailLayout)
            ->with('growl', [__('boilerplate-email-editor::layout.savesuccess'), 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return EmailLayout::findOrFail($id)->render([], false)->getContent();
    }

    /**
     * Get content for TinyMCE.
     *
     * @param Request $request
     *
     * @return array|string|null
     */
    public function getMce(Request $request)
    {
        if (class_exists('Debugbar')) {
            \Debugbar::disable();
        }

        $content = $request->input('content', '');
        $layout = EmailLayout::find($request->input('id'));

        if ($layout !== null) {
            $content = [
                'sender_email' => $request->input('sender_email') ?? config('mail.from.address'),
                'sender_name'  => $request->input('sender_name') ?? config('mail.from.name'),
                'content'      => '<div id="mceEditableContent" contenteditable="true">'.$content.'</div>',
            ];

            return $layout->render($content, false)->getContent();
        }

        return $content;
    }

    /**
     * Show the form for editing email layout.
     *
     * @param int     $id
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id, Request $request)
    {
        $emailLayout = EmailLayout::findOrFail($id);
        $userEmail = $request->user()->email;

        return view('boilerplate-email-editor::layout.edit', compact('emailLayout', 'userEmail'));
    }

    /**
     * Update email layout in database.
     *
     * @param Request $request
     * @param int     $id
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'label'   => 'required',
            'content' => 'required',
        ], [], [
            'label'   => __('boilerplate-email-editor::layout.label'),
            'content' => __('boilerplate-email-editor::layout.content'),
        ]);

        $emailLayout = EmailLayout::findOrFail($id);
        $emailLayout->update($request->all());

        return redirect()->route('emaileditor.layout.edit', $emailLayout)
            ->with('growl', [__('boilerplate-email-editor::layout.updatesuccess'), 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     */
    public function destroy($id)
    {
        EmailLayout::destroy($id);
    }

    /**
     * Send layout preview by mail.
     *
     * @param Request $request
     *
     * @throws \Exception
     */
    public function previewEmail(Request $request)
    {
        $to = $request->user();
        if (!empty($request->input('email', ''))) {
            $to = $request->input('email');
        }

        $content = Blade::get($request->input('content'), [], false);

        $mail = new Preview($content);
        $mail->subject($request->input('label'));

        Mail::to($to)->send($mail);
    }

    /**
     * Save preview in session before redirect with js to preview.
     *
     * @param Request $request
     */
    public function previewPost(Request $request)
    {
        $request->session()->flash('email_body', $request->input('content'));
    }

    /**
     * Preview layout in browser.
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function preview(Request $request)
    {
        $content = $request->session()->get('email_body');

        $content = Blade::get($content, [], false);

        if (empty($content)) {
            abort(404);
        }

        return response($content, 200)->header('Content-Type', 'text/html');
    }
}
