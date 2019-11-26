@extends('boilerplate::layout.index', [
    'title' => __('boilerplate-email-editor::editor.title'),
    'subtitle' => __('boilerplate-email-editor::layout.edit.title'),
    'breadcrumb' => [
        __('boilerplate-email-editor::editor.title'),
        __('boilerplate-email-editor::layout.title') => 'emaileditor.layout.index',
        __('boilerplate-email-editor::layout.edit.title')
    ]
])

@section('content')
    {!! Form::open(['route' => ['emaileditor.layout.update', $emailLayout->id], 'method' => 'put', 'autocomplete'=> 'off', 'id' => 'layout-form']) !!}
    <div class="row">
        <div class="col-sm-12 mbl">
            <a href="{{ route('emaileditor.layout.index') }}" class="btn btn-default">
                {{ __('boilerplate-email-editor::layout.backtolist') }}
            </a>
            <span class="btn-group pull-right">
                <button type="button" class="btn btn-default btn-preview">
                    {{ __('boilerplate-email-editor::layout.preview') }}
                </button>
                <button type="button" class="btn btn-default btn-preview-mail">
                    {{ __('boilerplate-email-editor::layout.previewbymail') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    {{ __('boilerplate-email-editor::layout.save') }}
                </button>
            </span>
        </div>
    </div>
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">{{ __('boilerplate-email-editor::layout.edit.title') }}</h3>
        </div>
        <div class="box-body">
            <div class="form-group {{ $errors->has('label') ? 'has-error' : '' }}">
                {{ Form::label('label', __('boilerplate-email-editor::layout.Label')) }}
                {{ Form::text('label', old('label', $emailLayout->label), ['class' => 'form-control']) }}
                {!! $errors->first('label','<p class="text-danger"><strong>:message</strong></p>') !!}
            </div>
            <div class="form-group {{ $errors->has('content') ? 'has-error' : '' }}">
                {{ Form::label('content', __('boilerplate-email-editor::layout.Content')) }}
                {{ Form::textarea('content', old('content', $emailLayout->content), ['class' => 'form-control', 'id' => 'body']) }}
                {!! $errors->first('content','<p class="text-danger"><strong>:message</strong></p>') !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@include('boilerplate-email-editor::layout.scripts')
