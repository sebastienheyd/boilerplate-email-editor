@extends('boilerplate::layout.index', [
    'title' => __('boilerplate-email-editor::editor.title'),
    'subtitle' => __('boilerplate-email-editor::email.list'),
    'breadcrumb' => [
        __('boilerplate-email-editor::editor.title'),
        __('boilerplate-email-editor::email.list'),
    ]
])

@section('content')
    @ability('admin', 'emaileditor_email_dev')
    <div class="row">
        <div class="col-sm-12 mbl">
            <span class="btn-group pull-right">
                <a href="{{ route("emaileditor.email.create") }}" class="btn btn-primary">
                    {{ __('boilerplate-email-editor::email.add') }}
                </a>
            </span>
        </div>
    </div>
    @endability
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">{{ __('boilerplate-email-editor::email.list') }}</h3>
        </div>
        <div class="box-body">
            <table class="table table-striped table-hover va-middle" id="emails-table">
                <thead>
                <tr>
                    <th>{{ __('boilerplate-email-editor::email.id') }}</th>
                    <th>{{ __('boilerplate-email-editor::email.Slug') }}</th>
                    <th>{{ __('boilerplate-email-editor::email.Description') }}</th>
                    <th>{{ __('boilerplate-email-editor::email.actions') }}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@include('boilerplate::load.datatables')

@push('js')
    <script>
        $(function () {
            oTable = $('#emails-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: {
                    url: '{!! route('emaileditor.email.datatable') !!}',
                    type: 'post',
                },
                columns: [
                    {data: 'id', name: 'id', width: '70px', visible: false, searchable: false},
                    {data: 'slug', name: 'slug', width: '120px'},
                    {data: 'description', name: 'description', searchable: true},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false, width: '110px'}
                ]
            });

            $('#emails-table').on('click', '.destroy', function (e) {
                e.preventDefault();

                var href = $(this).attr('href');

                // todo traduction
                bootbox.confirm("{{ __('boilerplate-email-editor::email.delete') }}", function (result) {
                    if (result === false) {
                        return;
                    }

                    $.ajax({
                        url: href,
                        method: 'delete',
                        success: function () {
                            oTable.ajax.reload();
                            growl("{{ __('boilerplate-email-editor::email.deletesuccess') }}", "success");
                        }
                    });
                });
            });
        });
    </script>
@endpush