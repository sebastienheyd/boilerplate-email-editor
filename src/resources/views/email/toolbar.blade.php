<div class="row py-2">
    <div class="col-12">
        <a href="{{ route('emaileditor.email.index') }}" class="btn btn-default">
            <span class="far fa-arrow-alt-circle-left text-muted"></span>
        </a>
        <button type="submit" class="btn btn-primary float-right ml-3">
            {{ __('boilerplate-email-editor::email.save') }}
        </button>
        <span class="btn-group float-right">
            <button type="button" class="btn btn-default btn-preview">
                {{ __('boilerplate-email-editor::email.preview') }}
            </button>
            <button type="button" class="btn btn-default btn-preview-mail">
                {{ __('boilerplate-email-editor::email.previewbymail') }}
            </button>
        </span>
    </div>
</div>