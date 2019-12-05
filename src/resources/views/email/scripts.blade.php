@include('boilerplate-media-manager::load.tinymce')
@push('js')
    <script>

        $(function () {

            loadMCE();

            $('[name=layout]').on('change', function () {
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
                        layout: $('[name=layout]').val(),
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
                                    layout: $('[name=layout]').val(),
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
                object_resizing: "img",
                relative_urls: false,
                convert_urls: false,
                visual: false,
                verify_html: false,
                toolbar1: tinymce.defaultSettings.toolbar,
                toolbar2: 'insertVar',
                table_toolbar: '',
                link_class_list: [
                    {title: '-', value: ''},
                    {title: 'Button', value: 'btn btn-default'},
                ],
                content_style: 'body{overflow-x:hidden}variable{background:#F0F0F0;cursor:not-allowed}',
                setup: function (editor) {
                    editor.ui.registry.addButton('insertVar', {
                        text: '{{ __('boilerplate-email-editor::email.insert_var') }}',
                        disabled: true,
                        onAction: function (_) {
                            bootbox.prompt("{{ __('boilerplate-email-editor::email.var_name') }}", function (result) {
                                if(result === null) { return null; }
                                editor.insertContent('<variable contenteditable="false">[' + result + ']</variable>');
                            });
                        },
                        onSetup: function (button) {
                            var editorEventCallback = function (e) {
                                button.setDisabled(e.element.nodeName.toLowerCase() === 'variable');
                            };
                            editor.on('NodeChange', editorEventCallback);

                            return function () {
                                editor.off('NodeChange', editorEventCallback);
                            };
                        }
                    });
                },
                code_change_callback: function (editor) {
                    $('[name=layout]').trigger('change');
                },
                init_instance_callback: function (editor) {
                    loadLayout(editor);
                }
            });
        }

        function loadLayout(editor) {
            $.ajax({
                url: '{{ route('emaileditor.email.mce', [], false) }}',
                type: 'post',
                data: {
                    content: editor.getContent(),
                    view: $('[name=layout]').val(),
                    sender_email: $('#sender_email').val(),
                    sender_name: $('#sender_name').val()
                },
                success: function (html) {
                    editor.setContent(html);
                }
            });

            if ($('[name=layout]_id').val() !== '0') {
                tinymce.activeEditor.getBody().setAttribute('contenteditable', false);
            }
        }
    </script>
@endpush
