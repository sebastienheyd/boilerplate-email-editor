<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Controllers;

use App\Http\Controllers\Controller;
use Sebastienheyd\BoilerplateEmailEditor\Mail\Preview;
use Sebastienheyd\BoilerplateEmailEditor\Models\Email;
use Sebastienheyd\BoilerplateEmailEditor\Facades\Blade;
use Illuminate\Http\Request;
use Mail;
use DataTables;
use Sebastienheyd\BoilerplateEmailEditor\Models\EmailLayout;

class EmailController extends Controller
{
    /**
     * EmailController constructor.
     */
    public function __construct()
    {
        $this->middleware('ability:admin,emaileditor_email_crud');
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
     * Get listing of emails for datatable
     *
     * @return mixed
     * @throws \Exception
     */
    public function datatable()
    {
        return Datatables::of(Email::select('*'))
            ->rawColumns(['actions'])
            ->editColumn('actions', function($email) {
                $b = '<a href="'.route('emaileditor.email.show', $email->id).'" class="btn btn-default btn-sm mrs" target="_blank"><i class="fa fa-eye"></i></a>';
                $b .= '<a href="'.route('emaileditor.email.edit', $email->id).'" class="btn btn-primary btn-sm mrs"><i class="fa fa-pencil"></i></a>';
                $b .= '<a href="'.route('emaileditor.email.destroy', $email->id).'" class="btn btn-danger btn-sm destroy"><i class="fa fa-trash"></i></a>';
                return $b;
            })->make(true);
    }

    /**
     * Show the form for creating a new email.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userEmail = $request->user()->email;
        $layouts = EmailLayout::all()->pluck('label', 'id')->toArray();
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
        $request->merge(['layout_id' => $request->input('layout_id') == '0' ? null : $request->input('layout_id')]);

        $this->validate($request, [
            'label'        => 'required',
            'subject'      => 'required',
            'content'      => 'required',
            'sender_email' => 'nullable|email'
        ], [], [
            'subject'      => __('boilerplate-email-editor::email.subject'),
            'label'        => __('boilerplate-email-editor::email.label'),
            'sender_email' => __('boilerplate-email-editor::email.sender_email')
        ]);

        $email = Email::create($request->all());

        return redirect()->route('emaileditor.email.edit', $email)
            ->with('growl', [__('boilerplate-email-editor::email.savesuccess'), 'success']);
    }

    /**
     * Parse e-mail content
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
     * Parse e-mail content from layout html
     *
     * @param $content
     *
     * @return string
     */
    private function parseContent($content)
    {
        // Retrieve content
        $html = new \DOMDocument();
        @$html->loadHTML($content);

        try {
            $innerHtml = function($node) {
                return implode(array_map([$node->ownerDocument, "saveHTML"], iterator_to_array($node->childNodes)));
            };

            $content = $innerHtml($html->getElementById('mceEditableContent'));
        } catch(\Exception $e) {
        }

        $content = htmlentities($content, null, 'utf-8');
        $content = str_replace("&nbsp;", " ", $content);
        $content = html_entity_decode($content);

        return trim($content);
    }

    /**
     * Display the specified resource.
     *
     * @param  integer $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Email::findOrFail($id)->render([], false);
    }

    /**
     * Show the form for editing email layout
     *
     * @param integer $id
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id, Request $request)
    {
        $email = Email::findOrFail($id);
        $layouts = EmailLayout::all()->pluck('label', 'id')->toArray();
        $userEmail = $request->user()->email;
        return view('boilerplate-email-editor::email.edit', compact('email', 'layouts', 'userEmail'));
    }

    /**
     * Update email layout in database
     *
     * @param Request $request
     * @param integer $id
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $request->merge(['content' => $this->parseContent($request->input('content'))]);
        $request->merge(['layout_id' => $request->input('layout_id') == '0' ? null : $request->input('layout_id')]);

        $this->validate($request, [
            'label'        => 'required',
            'subject'      => 'required',
            'content'      => 'required',
            'sender_email' => 'nullable|email'
        ], [], [
            'subject'      => __('boilerplate-email-editor::email.subject'),
            'label'        => __('boilerplate-email-editor::email.label'),
            'sender_email' => __('boilerplate-email-editor::email.sender_email')
        ]);

        $email = Email::findOrFail($id);
        $email->update($request->all());

        return redirect()->route('emaileditor.email.edit', $email)
            ->with('growl', [__('boilerplate-email-editor::email.updatesuccess'), 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param integer $id
     */
    public function destroy($id)
    {
        Email::destroy($id);
    }

    /**
     * Send layout preview by mail
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
            'sender_name'  => $request->input('sender_name') ?? config('mail.from.name')
        ];

        $content = Blade::get($this->parseContent($request->input('content')), $data, false);
        $layout = EmailLayout::find($request->input('layout_id'));

        if($layout !== null) {
            $data['content'] = $content;
            $content = $layout->render($data, false)->getContent();
        }

        $mail = new Preview($content);
        $mail->subject($request->input('subject') ?? 'E-mail preview');

        Mail::to($to)->send($mail);
    }

    /**
     * Save preview in session before redirect with js to preview
     *
     * @param Request $request
     */
    public function previewPost(Request $request)
    {
        $request->session()->flash('content', $request->input('content'));
        $request->session()->flash('layout_id', $request->input('layout_id'));
        $request->session()->flash('sender_email', $request->input('sender_email') ?? config('mail.from.address'));
        $request->session()->flash('sender_name', $request->input('sender_name') ?? config('mail.from.name'));
    }

    /**
     * Preview layout in browser
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function preview(Request $request)
    {
        $data = [
            'sender_email' => $request->session()->get('sender_email'),
            'sender_name'  => $request->session()->get('sender_name')
        ];

        $content = Blade::get($this->parseContent($request->session()->get('content')), $data, false);
        $layout = EmailLayout::find($request->session()->get('layout_id'));

        if($layout !== null) {
            $data['content'] = $content;
            $content = $layout->render($data, false)->getContent();
        }

        return response($content, 200)->header('Content-Type', 'text/html');
    }
}
