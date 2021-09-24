@extends('boilerplate::layout.index', [
    'title' => __('boilerplate-email-editor::editor.title'),
    'subtitle' => __('boilerplate-email-editor::email.add.title'),
    'breadcrumb' => [
        __('boilerplate-email-editor::editor.title'),
        __('boilerplate-email-editor::email.add.title')
    ]
])

@section('content')
    @if(isset($email))
    {!! Form::open(['route' => ['emaileditor.email.update', $email->id], 'method' => 'put', 'autocomplete'=> 'off', 'id' => 'email-form']) !!}
    @else
    {!! Form::open(['route' => 'emaileditor.email.store', 'method' => 'post', 'autocomplete'=> 'off', 'id' => 'email-form']) !!}
    @endif
    @include('boilerplate-email-editor::email.toolbar')
    <div class="row">
        <div class="col-md-4">
            @component('boilerplate::card', ['color' => 'success', 'title' => __('boilerplate-email-editor::email.header')])
                @component('boilerplate::input', ['name' => 'subject', 'value' => $email->subject ?? '', 'label' => __('boilerplate-email-editor::email.Subject'), 'group-class' => 'required'])@endcomponent()
                @component('boilerplate::input', ['name' => 'sender_name', 'value' => $email->sender_name ?? '', 'label' => __('boilerplate-email-editor::email.Sender_name'), 'placeholder' => config('mail.from.name'), 'help' => __('boilerplate-email-editor::email.ifNameEmpty')])@endcomponent()
                @component('boilerplate::input', ['name' => 'sender_email', 'value' => $email->sender_email ?? '', 'label' => __('boilerplate-email-editor::email.Sender_email'), 'placeholder' => config('mail.from.address'), 'help' => __('boilerplate-email-editor::email.ifAdressEmpty')])@endcomponent()
            @endcomponent

            @ability('admin', 'emaileditor_email_dev')
            @component('boilerplate::card', ['color' => 'primary', 'title' => __('boilerplate-email-editor::email.Parameters')])
                @component('boilerplate::input', ['name' => 'slug', 'value' => $email->slug ?? '', 'label' => __('boilerplate-email-editor::email.Slug'), 'help' => __('boilerplate-email-editor::email.Slug_tip'), 'group-class' => 'required'])@endcomponent()
                @component('boilerplate::input', ['name' => 'description', 'value' => $email->description ?? '', 'label' => __('boilerplate-email-editor::email.Description')])@endcomponent()
                @component('boilerplate::input', ['type' => 'select', 'name' => 'layout', 'value' => $email->layout ?? '0', 'options' => ['0' => '-'] + $layouts, 'id' => 'layout', 'label' => __('boilerplate-email-editor::email.Layout')])@endcomponent()
            @endcomponent
            @else
                <input type="hidden" name="layout" value="{{ $email->layout ?? '' }}">
            @endpermission
        </div>
        <div class="col-md-8">
            @component('boilerplate::card', ['color' => 'info', 'tabs' => true])
                @slot('header')
                    <ul class="nav nav-tabs" id="email-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="content-tab" data-toggle="pill" href="#tab-content" role="tab" aria-controls="email-tabs-content" aria-selected="true">{{ __('boilerplate-email-editor::email.Content') }}</a>
                        </li>
                        @ability('admin', 'emaileditor_email_dev')
                        <li class="nav-item">
                            <a class="nav-link" id="code-tab" data-toggle="pill" href="#tab-code" role="tab" aria-controls="email-tabs-code" aria-selected="false">{{ __('boilerplate-email-editor::email.Code') }}</a>
                        </li>
                        @endability
                    </ul>
                @endslot
                <div class="tab-content" id="email-tabs-tabContent">
                    <div class="tab-pane fade show active" id="tab-content" role="tabpanel" aria-labelledby="content-tab">
                        @component('boilerplate::input', ['type' => 'textarea', 'name' => 'content', 'value' => $email->mce_content ?? '', 'id' => 'content'])@endcomponent()
                    </div>
                    <div class="tab-pane fade" id="tab-code" role="tabpanel" aria-labelledby="code-tab"></div>
                </div>
            @endcomponent
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@push('css')
@component('boilerplate::minify', ['type' => 'css'])
    <style>
        .required label:after {
            content: '*';
            padding-left: 5px;
            color: crimson;
        }
    </style>
@endcomponent
@endpush

@include('boilerplate-email-editor::email.scripts')
