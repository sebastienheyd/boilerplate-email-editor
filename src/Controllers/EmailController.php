<?php

namespace Sebastienheyd\BoilerplateEmailEditor\Controllers;

use App\Http\Controllers\Controller;
use DOMDocument;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Sebastienheyd\BoilerplateEmailEditor\Mail\Preview;
use Sebastienheyd\BoilerplateEmailEditor\Models\Email;
use Sebastienheyd\BoilerplateEmailEditor\Models\EmailLayout;
use Yajra\DataTables\Facades\DataTables;

class EmailController extends Controller
{
    /**
     * Display a listing of emails layouts.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('boilerplate-email-editor::email.index');
    }

    /**
     * Get listing of emails for datatable.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function datatable()
    {
        return Datatables::of(Email::select('*'))
            ->rawColumns(['actions'])
            ->editColumn(
                'actions',
                function ($email) {
                    $b = '<a href="'.route('emaileditor.email.show', $email->id).
                        '" class="btn btn-default btn-sm mr-1" target="_blank"><i class="fa fa-fw fa-eye"></i></a>';
                    $b .= '<a href="'.route('emaileditor.email.edit', $email->id).
                        '" class="btn btn-primary btn-sm mr-1"><i class="fa fa-fw fa-pencil-alt"></i></a>';

                    if (Auth::user()->ability('admin', 'emaileditor_email_dev')) {
                        $b .= '<a href="'.route('emaileditor.email.destroy', $email->id).
                            '" class="btn btn-danger btn-sm destroy"><i class="fa fa-fw fa-trash"></i></a>';
                    }

                    return $b;
                }
            )->make(true);
    }

    /**
     * Show the form for creating a new email.
     *
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function create(Request $request)
    {
        if (!Auth::user()->ability('admin', 'emaileditor_email_dev')) {
            abort(403);
        }

        $userEmail = $request->user()->email;
        $layouts = EmailLayout::getList();

        return view('boilerplate-email-editor::email.edit', compact('userEmail', 'layouts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['content' => $this->parseContent($request->input('content') ?? '')]);
        $request->merge(['layout' => $request->input('layout') == '0' ? null : $request->input('layout')]);

        $this->validate(
            $request,
            [
                'subject'      => 'required',
                'content'      => 'required',
                'sender_email' => 'nullable|email',
                'slug'         => 'required|unique:emails,slug',
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
    public function content(Request $request): string
    {
        return $this->parseContent($request->input('content') ?? '');
    }

    /**
     * Parse e-mail content from layout html.
     *
     * @param string $content
     * @param array  $data
     *
     * @return string
     */
    private function parseContent(string $content, array $data = []): string
    {
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
        $content = preg_replace('`<variable.*?>\[(.*?)]</variable>`', '[$1]', $content);

        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $content = str_replace("[$k]", $v, $content);
            }
        }

        if ($content === '') {
            return $content;
        }

        $html = new DOMDocument('1.0', 'utf-8');
        @$html->loadHTML($content);

        try {
            $innerHtml = function ($node) {
                return implode(array_map([$node->ownerDocument, 'saveHTML'], iterator_to_array($node->childNodes)));
            };

            $content = $innerHtml($html->getElementById('mceEditableContent'));
        } catch (Exception $e) {
        }

        return trim(urldecode($content));
    }

    /**
     * Display the specified resource.
     *
     * @param Email $email
     *
     * @return Response
     */
    public function show(Email $email): Response
    {
        return response($email->render([]), 200)->header('Content-Type', 'text/html');
    }

    /**
     * Show the form for editing email layout.
     *
     * @param Email   $email
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function edit(Email $email, Request $request)
    {
        $layouts = EmailLayout::getList();
        $userEmail = $request->user()->email;

        return view('boilerplate-email-editor::email.edit', compact('email', 'layouts', 'userEmail'));
    }

    /**
     * Update email layout in database.
     *
     * @param Email   $email
     * @param Request $request
     *
     * @throws ValidationException
     *
     * @return RedirectResponse
     */
    public function update(Email $email, Request $request): RedirectResponse
    {
        $request->merge(['content' => $this->parseContent($request->input('content') ?? '')]);
        $request->merge(['layout' => $request->input('layout_id') == '0' ? null : $request->input('layout')]);

        // By security
        if (!Auth::user()->ability('admin', 'emaileditor_email_dev')) {
            $request->merge([
                'description' => $email->description,
                'slug'        => $email->slug,
                'layout'      => $email->layout,
            ]);
        }

        $data = $request->all();

        $this->validate(
            $request,
            [
                'subject'      => 'required',
                'content'      => 'required',
                'sender_email' => 'nullable|email',
                'slug'         => 'required|unique:emails,slug,'.$email->id,
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
     * @param Email $email
     */
    public function destroy(Email $email)
    {
        if (!Auth::user()->ability('admin', 'emaileditor_email_dev')) {
            abort(403);
        }

        $email->delete();
    }

    /**
     * Send layout preview by mail.
     *
     * @param Request $request
     *
     * @throws Exception
     */
    public function previewEmail(Request $request)
    {
        $to = $request->input('email') ?? $request->user()->email;

        $data = [
            'sender_email' => $request->input('sender_email') ?? config('boilerplate.email-editor.from.address'),
            'sender_name'  => $request->input('sender_name') ?? config('boilerplate.email-editor.from.name'),
        ];

        $content = $this->parseContent($request->input('content'), $data);
        $layout = $request->input('layout');

        if (!empty($layout)) {
            $data['content'] = $content;
            $content = (string) view($layout, $data);
        }

        $mail = (new Preview($content))->subject($request->input('subject') ?? 'E-mail preview');

        Mail::mailer(config('boilerplate.email-editor.mailer'))->to($to)->send($mail);
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
        $request->session()->flash(
            'sender_email',
            $request->input('sender_email') ?? config('boilerplate.email-editor.from.address')
        );
        $request->session()->flash(
            'sender_name',
            $request->input('sender_name') ?? config('boilerplate.email-editor.from.name')
        );
    }

    /**
     * Preview layout in browser.
     *
     * @param Request $request
     *
     * @return Application|ResponseFactory|Factory|View|Response
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

        if (preg_match('#<div id="mceEditableContent" contenteditable="true">#', $content)) {
            $content = $this->parseContent($content);
        }

        $content = [
            'sender_email' => $request->input('sender_email') ?? config('boilerplate.email-editor.from.address'),
            'sender_name'  => $request->input('sender_name') ?? config('boilerplate.email-editor.from.name'),
            'content'      => sprintf('<div id="mceEditableContent" contenteditable="true">%s</div>', $content),
        ];

        return view($request->post('view'), $content);
    }
}
