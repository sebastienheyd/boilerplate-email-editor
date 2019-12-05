<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;
use Sebastienheyd\BoilerplateEmailEditor\Facades\Blade;
use Sebastienheyd\BoilerplateEmailEditor\Mail\Preview;
use Sebastienheyd\BoilerplateEmailEditor\Models\Email;
use Sebastienheyd\BoilerplateEmailEditor\Models\EmailLayout;

class EmailController extends Controller
{
    /**
     * EmailController constructor.
     */
    public function __construct()
    {
        $this->middleware('ability:admin,emaileditor_email_edition,emaileditor_email_dev');
    }

    /**
     * Display a listing of emails layouts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('boilerplate-email-editor::email.index');
    }

    /**
     * Get listing of emails for datatable.
     *
     * @return mixed
     * @throws \Exception
     *
     */
    public function datatable()
    {
        return Datatables::of(Email::select('*'))
            ->rawColumns(['actions'])
            ->editColumn(
                'actions',
                function ($email) {
                    $b = '<a href="'.route('emaileditor.email.show', $email->id).
                        '" class="btn btn-default btn-sm mrs" target="_blank"><i class="fa fa-eye"></i></a>';
                    $b .= '<a href="'.route('emaileditor.email.edit', $email->id).
                        '" class="btn btn-primary btn-sm mrs"><i class="fa fa-pencil"></i></a>';

                    if (Auth::user()->ability('admin', 'emaileditor_email_dev')) {
                        $b .= '<a href="'.route('emaileditor.email.destroy', $email->id).
                            '" class="btn btn-danger btn-sm destroy"><i class="fa fa-trash"></i></a>';
                    }

                    return $b;
                }
            )->make(true);
    }

    /**
     * Show the form for creating a new email.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!Auth::user()->ability('admin', 'emaileditor_email_dev')) {
            abort(403);
        }

        $userEmail = $request->user()->email;
        $layouts = EmailLayout::getList();

        return view('boilerplate-email-editor::email.create', compact('userEmail', 'layouts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     *
     */
    public function store(Request $request)
    {
        $request->merge(['content' => $this->parseContent($request->input('content'))]);
        $request->merge(['layout' => $request->input('layout') == '0' ? null : $request->input('layout')]);

        $this->validate(
            $request,
            [
                'subject'      => 'required',
                'content'      => 'required',
                'sender_email' => 'nullable|email',
                'slug'         => 'unique:emails,slug',
            ],
            [],
            [
                'subject'      => __('boilerplate-email-editor::email.subject'),
                'sender_email' => __('boilerplate-email-editor::email.sender_email'),
                'slug'         => __('boilerplate-email-editor::email.slug'),
            ]
        );

        $email = Email::create($request->all());

        return redirect()->route('emaileditor.email.edit', $email)
            ->with('growl', [__('boilerplate-email-editor::email.savesuccess'), 'success']);
    }

    /**
     * Parse e-mail content.
     *
     * @param Request $request
     *
     * @return string
     */
    public function content(Request $request)
    {
        return $this->parseContent($request->input('content'));
    }

    /**
     * Parse e-mail content from layout html.
     *
     * @param $content
     *
     * @return string
     */
    private function parseContent($content, $data = [])
    {
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
        $content = preg_replace('`<variable.*?>\[(.*?)]</variable>`', '[$1]', $content);

        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $content = str_replace("[$k]", $v, $content);
            }
        }

        $html = new \DOMDocument('1.0', 'utf-8');
        @$html->loadHTML($content);

        try {
            $innerHtml = function ($node) {
                return implode(array_map([$node->ownerDocument, 'saveHTML'], iterator_to_array($node->childNodes)));
            };

            $content = $innerHtml($html->getElementById('mceEditableContent'));
        } catch (\Exception $e) {
        }

        $content = urldecode($content);

        return trim($content);
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
        return Email::find($id)->render([], false);
    }

    /**
     * Show the form for editing email layout.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id, Request $request)
    {
        $email = Email::findOrFail($id);
        $layouts = EmailLayout::getList();
        $userEmail = $request->user()->email;

        return view('boilerplate-email-editor::email.edit', compact('email', 'layouts', 'userEmail'));
    }

    /**
     * Update email layout in database.
     *
     * @param Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     *
     */
    public function update(Request $request, $id)
    {
        $request->merge(['content' => $this->parseContent($request->input('content'))]);
        $request->merge(['layout' => $request->input('layout_id') == '0' ? null : $request->input('layout')]);

        $email = Email::findOrFail($id);
        $data = $request->all();

        // By security
        if (!Auth::user()->ability('admin', 'emaileditor_email_dev')) {
            $data['description'] = $email->description;
            $data['slug'] = $email->slug;
            $data['layout'] = $email->layout;
        }

        $this->validate(
            $request,
            [
                'subject'      => 'required',
                'content'      => 'required',
                'sender_email' => 'nullable|email',
                'unique:emails,slug,'.$id
            ],
            [],
            [
                'subject'      => __('boilerplate-email-editor::email.subject'),
                'sender_email' => __('boilerplate-email-editor::email.sender_email'),
                'slug'         => __('boilerplate-email-editor::email.slug'),
            ]
        );


        $email->update($data);

        return redirect()->route('emaileditor.email.edit', $email)
            ->with('growl', [__('boilerplate-email-editor::email.updatesuccess'), 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     */
    public function destroy($id)
    {
        if (!Auth::user()->ability('admin', 'emaileditor_email_dev')) {
            abort(403);
        }

        Email::destroy($id);
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
        $to = $request->input('email') ?? $request->user()->email;

        $data = [
            'sender_email' => $request->input('sender_email') ?? config('mail.from.address'),
            'sender_name'  => $request->input('sender_name') ?? config('mail.from.name'),
        ];

        $content = $this->parseContent($request->input('content'), $data);
        $layout = $request->input('layout');

        if (!empty($layout)) {
            $data['content'] = $content;
            $content = (string)view($layout, $data);
        }

        $mail = new Preview($content);
        $mail->subject($request->input('subject') ?? 'E-mail preview');

        Mail::to($to)->send($mail);
    }

    /**
     * Save preview in session before redirect with js to preview.
     *
     * @param Request $request
     */
    public function previewPost(Request $request)
    {
        $request->session()->flash('content', $request->input('content'));
        $request->session()->flash('layout', $request->input('layout'));
        $request->session()->flash('sender_email', $request->input('sender_email') ?? config('mail.from.address'));
        $request->session()->flash('sender_name', $request->input('sender_name') ?? config('mail.from.name'));
    }

    /**
     * Preview layout in browser.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     *
     */
    public function preview(Request $request)
    {
        $data = [
            'sender_email' => $request->session()->get('sender_email'),
            'sender_name'  => $request->session()->get('sender_name'),
        ];

        $content = $this->parseContent($request->session()->get('content'), $data);
        $layout = $request->session()->get('layout');

        if (!empty($layout)) {
            $data['content'] = $content;
            return view($layout, $data);
        }

        return response($content, 200)->header('Content-Type', 'text/html');
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

        $content = $request->post('content', '');

        if (empty($request->post('view'))) {
            return $content;
        }

        $content = [
            'sender_email' => $request->input('sender_email') ?? config('mail.from.address'),
            'sender_name'  => $request->input('sender_name') ?? config('mail.from.name'),
            'content'      => sprintf('<div id="mceEditableContent" contenteditable="true">%s</div>', $content),
        ];

        return view($request->post('view'), $content);
    }
}
