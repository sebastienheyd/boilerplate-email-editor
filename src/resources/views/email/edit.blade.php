@extends('boilerplate::layout.index', [
    'title' => __('boilerplate-email-editor::editor.title'),
    'subtitle' => __('boilerplate-email-editor::email.add.title'),
    'breadcrumb' => [
        __('boilerplate-email-editor::editor.title'),
        __('boilerplate-email-editor::email.add.title')
    ]
])

@section('content')
    {!! Form::open(['route' => ['emaileditor.email.update', $email->id], 'method' => 'put', 'autocomplete'=> 'off', 'id' => 'email-form']) !!}
    @include('boilerplate-email-editor::email.toolbar')
    <div class="row">
        <div class="col-md-4">
            @component('boilerplate::card', ['color' => 'success', 'title' => __('boilerplate-email-editor::email.header')])
                <div class="form-group">
                    {{ Form::label('subject', __('boilerplate-email-editor::email.Subject'), ['class' => 'required']) }}
                    {{ Form::text('subject', old('subject', $email->subject), ['class' => 'form-control'.$errors->first('subject',' is-invalid')]) }}
                    {!! $errors->first('subject','<div class="error-bubble"><div>:message</div></div>') !!}
                </div>
                <div class="form-group">
                    {{ Form::label('sender_name', __('boilerplate-email-editor::email.Sender_name')) }}
                    {{ Form::text('sender_name', old('sender_name', $email->sender_name), ['class' => 'form-control'.$errors->first('sender_name',' is-invalid'), 'placeholder' => config('mail.from.name')]) }}
                    {!! $errors->first('sender_name','<div class="error-bubble"><div>:message</div></div>') !!}
                    <small class="text-muted">{{ __('boilerplate-email-editor::email.ifNameEmpty') }}</small>
                </div>
                <div class="form-group">
                    {{ Form::label('sender_email', __('boilerplate-email-editor::email.Sender_email')) }}
                    {{ Form::text('sender_email', old('sender_email', $email->sender_email), ['class' => 'form-control'.$errors->first('sender_email',' is-invalid'), 'placeholder' => config('mail.from.address')]) }}
                    {!! $errors->first('sender_email','<div class="error-bubble"><div>:message</div></div>') !!}
                    <small class="text-muted">{{ __('boilerplate-email-editor::email.ifAdressEmpty') }}</small>
                </div>
            @endcomponent

            @ability('admin', 'emaileditor_email_dev')
            @component('boilerplate::card', ['color' => 'primary', 'title' => __('boilerplate-email-editor::email.Parameters')])
                <div class="form-group">
                    {{ Form::label('slug', __('boilerplate-email-editor::email.Slug'), ['class' => 'required']) }}
                    {{ Form::text('slug', old('slug', $email->slug), ['class' => 'form-control'.$errors->first('slug',' is-invalid')]) }}
                    {!! $errors->first('slug','<div class="error-bubble"><div>:message</div></div>') !!}
                    <small class="text-muted">{{ __('boilerplate-email-editor::email.Slug_tip') }}</small>
                </div>
                <div class="form-group">
                    {{ Form::label('description', __('boilerplate-email-editor::email.Description')) }}
                    {{ Form::text('description', old('description', $email->description), ['class' => 'form-control'.$errors->first('description',' is-invalid')]) }}
                    {!! $errors->first('description','<div class="error-bubble"><div>:message</div></div>') !!}
                </div>
                <div class="form-group">
                    {{ Form::label('layout', __('boilerplate-email-editor::email.Layout')) }}
                    {{ Form::select('layout', ['0' => '-'] + $layouts, old('layout', $email->layout), ['class' => 'form-control'.$errors->first('layout',' is-invalid')]) }}
                    {!! $errors->first('layout','<div class="error-bubble"><div>:message</div></div>') !!}
                </div>
            @endcomponent
            @else
                <input type="hidden" name="layout" value="{{ $email->layout }}">
            @endpermission
        </div>
        <div class="col-md-8">
            @component('boilerplate::card', ['color' => 'info', 'title' => __('boilerplate-email-editor::email.Content')])
                <div class="form-group">
                    {!! $errors->first('content','<div class="error-bubble"><div>:message</div></div>') !!}
                    {{ Form::textarea('content', old('content', $email->mce_content), ['class' => 'form-control'.$errors->first('subject',' is-invalid'), 'id' => 'content']) }}
                </div>
            @endcomponent
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@push('css')
    <style>
        .required:after {
            content: '*';
            padding-left: 5px;
            color: crimson;
        }
    </style>
@endpush

@include('boilerplate-email-editor::email.scripts')
