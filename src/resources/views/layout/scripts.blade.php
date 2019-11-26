@include('boilerplate::load.codemirror', ['theme' => 'storm'])
@push('js')
    <script>
        var myCodeMirror = $('#body').codemirror();

        $('.btn-preview').on('click', function () {
            $.ajax({
                url: '{{ route('emaileditor.layout.preview') }}',
                type: 'post',
                data: {content: myCodeMirror.getValue()},
                success: function () {
                    window.open('{{ route('emaileditor.layout.preview') }}', '_blank');
                }
            });
        });

        $('.btn-preview-mail').on('click', function () {
            bootbox.prompt({
                title: "{{ __('boilerplate-email-editor::editor.mail-prompt') }}",
                placeholder: '{{ $userEmail }}',
                buttons: {
                    confirm: {
                        label: "{{ __('boilerplate-email-editor::editor.send') }}"
                    }
                },
                callback: function (result) {
                    if (result !== null) {
                        $.ajax({
                            url: '{{ route('emaileditor.layout.preview.email') }}',
                            type: 'post',
                            data: {
                                label: $('input[name="label"]').val(),
                                content: myCodeMirror.getValue(),
                                email: result
                            },
                            success: function () {
                                growl("{{ __('boilerplate-email-editor::editor.mail-success') }}", 'success')
                            }
                        });
                    }
                }
            });
        });
    </script>
@endpush
