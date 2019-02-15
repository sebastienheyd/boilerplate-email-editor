@extends('boilerplate::layout.index', [
    'title' => __('boilerplate-email-editor::editor.title'),
    'subtitle' => __('boilerplate-email-editor::layout.title'),
    'breadcrumb' => [
        __('boilerplate-email-editor::editor.title'),
        __('boilerplate-email-editor::layout.title'),
    ]
])

@section('content')
    <div class="row">
        <div class="col-sm-12 mbl">
            <span class="btn-group pull-right">
                <a href="{{ route("emaileditor.layout.create") }}" class="btn btn-primary">
                    {{ __('boilerplate-email-editor::layout.add') }}
                </a>
            </span>
        </div>
    </div>
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">{{ __('boilerplate-email-editor::layout.list') }}</h3>
        </div>
        <div class="box-body">
            <table class="table table-striped table-hover va-middle" id="layouts-table">
                <thead>
                <tr>
                    <th>{{ __('boilerplate-email-editor::layout.id') }}</th>
                    <th>{{ __('boilerplate-email-editor::layout.Label') }}</th>
                    <th>Actions</th>
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
            oTable = $('#layouts-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: {
                    url: '{!! route('emaileditor.layout.datatable') !!}',
                    type: 'post',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                },
                columns: [
                    {data: 'id', name: 'id', width : '70px'},
                    {data: 'label', name: 'label', searchable: true},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false, width : '110px'}
                ]
            });

            $('#layouts-table').on('click', '.destroy', function (e) {
                e.preventDefault();

                var href = $(this).attr('href');

                bootbox.confirm("{{ __('boilerplate-email-editor::layout.confirmdelete') }}", function (result) {
                    if (result === false) return;

                    $.ajax({
                        url: href,
                        method: 'delete',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        success: function(){
                            oTable.ajax.reload();
                            growl("{{ __('boilerplate-email-editor::layout.deletesuccess') }}", "success");
                        }
                    });
                });
            });
        });
    </script>
@endpush