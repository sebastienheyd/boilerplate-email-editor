@include('boilerplate-media-manager::load.tinymce')
@push('js')
    <script>

        $(function () {

            loadMCE();

            $('#layout_id').on('change', function () {
                $.ajax({
                    url: '{{ route('emaileditor.email.content') }}',
                    type: 'post',
                    data: {
                        content: $("#content").tinymce().getContent()
                    },
                    success: function (text) {
                        $('#content').html(text);
                        reloadMCE();
                    }
                });
            });

            $('.btn-preview').on('click', function (e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('emaileditor.email.preview') }}',
                    type: 'post',
                    data: {
                        content: $("#content").tinymce().getContent(),
                        layout_id: $('#layout_id').val(),
                        sender_email: $('#sender_email').val(),
                        sender_name: $('#sender_name').val()
                    },
                    success: function () {
                        window.open('{{ route('emaileditor.email.preview') }}', '_blank');
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
                                url: '{{ route('emaileditor.email.preview.email') }}',
                                type: 'post',
                                data: {
                                    content: $("#content").tinymce().getContent(),
                                    layout_id: $('#layout_id').val(),
                                    email: result,
                                    subject: $('#subject').val(),
                                    sender_email: $('#sender_email').val(),
                                    sender_name: $('#sender_name').val()
                                },
                                success: function () {
                                    growl("{{ __('boilerplate-email-editor::editor.mail-success') }}", 'success')
                                }
                            });
                        }
                    }
                });
            });
        });

        function reloadMCE() {
            $('#content').tinymce().remove();
            loadMCE();
        }

        function loadMCE() {
            $('#content').tinymce({
                plugins: tinymce.defaultSettings.plugins + " noneditable",
                remove_script_host: true,
                entity_encoding: "raw",
                relative_urls: false,
                convert_urls: false,
                visual: false,
                verify_html: false,
                link_class_list: [
                    {title: '-', value: ''},
                    {title: 'Button', value: 'btn btn-default'},
                ],
                content_style: 'body {overflow-x:hidden;padding-bottom:0 !important;}',
                code_change_callback: function (editor) {
                    $('#layout_id').trigger('change');
                },
                init_instance_callback: function (editor) {
                    loadLayout(editor);
                }
            });
        }

        function loadLayout(editor) {
            $.ajax({
                url: '{{ route('emaileditor.layout.mce', [], false) }}',
                type: 'post',
                data: {
                    content: editor.getContent(),
                    id: $('#layout_id').val(),
                    sender_email: $('#sender_email').val(),
                    sender_name: $('#sender_name').val()
                },
                success: function (html) {
                    editor.setContent(html);
                }
            });

            if ($('#layout_id').val() !== '0') {
                tinymce.activeEditor.getBody().setAttribute('contenteditable', false);
            }
        }
    </script>
@endpush
