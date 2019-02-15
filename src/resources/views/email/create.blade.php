@extends('boilerplate::layout.index', [
    'title' => __('boilerplate-email-editor::editor.title'),
    'subtitle' => __('boilerplate-email-editor::email.add.title'),
    'breadcrumb' => [
        __('boilerplate-email-editor::editor.title'),
        __('boilerplate-email-editor::layout.title') => 'emaileditor.layout.index',
        __('boilerplate-email-editor::email.add.title')
    ]
])

@section('content')
    {!! Form::open(['route' => 'emaileditor.email.store', 'method' => 'post', 'autocomplete'=> 'off', 'id' => 'email-form']) !!}
    <div class="row">
        <div class="col-sm-12 mbl">
            <a href="{{ route('emaileditor.email.index') }}" class="btn btn-default">
                {{ __('boilerplate-email-editor::email.backtolist') }}
            </a>
            <span class="btn-group pull-right">
                <button type="button" class="btn btn-default btn-preview">
                    {{ __('boilerplate-email-editor::email.preview') }}
                </button>
                <button type="button" class="btn btn-default btn-preview-mail">
                    {{ __('boilerplate-email-editor::email.previewbymail') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    {{ __('boilerplate-email-editor::email.save') }}
                </button>
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">{{ __('boilerplate-email-editor::email.header') }}</h3>
                </div>
                <div class="box-body">
                    <div class="form-group {{ $errors->has('subject') ? 'has-error' : '' }}">
                        {{ Form::label('subject', __('boilerplate-email-editor::email.Subject'), ['class' => 'required']) }}
                        {{ Form::text('subject', old('subject'), ['class' => 'form-control']) }}
                        {!! $errors->first('subject','<p class="text-danger"><strong>:message</strong></p>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('sender_name') ? 'has-error' : '' }}">
                        {{ Form::label('sender_name', __('boilerplate-email-editor::email.Sender_name')) }}
                        {{ Form::text('sender_name', old('sender_name'), ['class' => 'form-control', 'placeholder' => config('mail.from.name')]) }}
                        <small class="text-muted">{{ __('boilerplate-email-editor::email.ifNameEmpty') }}</small>
                        {!! $errors->first('sender_name','<p class="text-danger"><strong>:message</strong></p>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('sender_email') ? 'has-error' : '' }}">
                        {{ Form::label('sender_email', __('boilerplate-email-editor::email.Sender_email')) }}
                        {{ Form::text('sender_email', old('sender_email'), ['class' => 'form-control', 'placeholder' => config('mail.from.address')]) }}
                        <small class="text-muted">{{ __('boilerplate-email-editor::email.ifAdressEmpty') }}</small>
                        {!! $errors->first('sender_email','<p class="text-danger"><strong>:message</strong></p>') !!}
                    </div>
                </div>
            </div>
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">{{ __('boilerplate-email-editor::email.Parameters') }}</h3>
                </div>
                <div class="box-body">
                    <div class="form-group {{ $errors->has('label') ? 'has-error' : '' }}">
                        {{ Form::label('label', __('boilerplate-email-editor::email.Label'), ['class' => 'required']) }}
                        {{ Form::text('label', old('label'), ['class' => 'form-control']) }}
                        <small class="text-muted">{{ __('boilerplate-email-editor::email.LabelBo') }}</small>
                        {!! $errors->first('label','<p class="text-danger"><strong>:message</strong></p>') !!}
                    </div>
                    <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                        {{ Form::label('description',  __('boilerplate-email-editor::email.Description')) }}
                        {{ Form::text('description', old('description'), ['class' => 'form-control']) }}
                        {!! $errors->first('description','<p class="text-danger"><strong>:message</strong></p>') !!}
                    </div>
                    <div class="form-group">
                        {{ Form::label('layout_id', __('boilerplate-email-editor::email.Layout')) }}
                        {{ Form::select('layout_id', [0 => '-'] + $layouts, old('layout_id'), ['class' => 'form-control'.($errors->has('layout_id') ? ' is-invalid' : '')]) }}
                        {!! $errors->first('layout_id','<p class="text-danger"><strong>:message</strong></p>') !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">{{ __('boilerplate-email-editor::email.Content') }}</h3>
                </div>
                <div class="box-body">
                    <div class="form-group {{ $errors->has('content') ? 'has-error' : '' }}">
                        {!! $errors->first('content','<p class="text-danger"><strong>:message</strong></p>') !!}
                        {{ Form::textarea('content', old('content'), ['class' => 'form-control', 'id' => 'content']) }}
                    </div>
                </div>
            </div>
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