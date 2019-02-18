@push('js')
    <script src="{{ mix('/vendor/tinymce/tinymce.min.js', '/assets/vendor/boilerplate-email-editor') }}"></script>
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

            $('.btn-preview').on('click', function(e){
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
                        if(result !== null) {
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
                language: 'fr',
                plugins: ["autoresize,image,link,code"],
                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
                object_resizing: false,
                image_advtab: true,
                remove_script_host: true,
                removed_menuitems: 'newdocument',
                forced_root_block: "",
                branding: false,
                visual: false,
                verify_html:false,
                browser_spellcheck: true,
                entity_encoding : "raw",
                encoding: "UTF-8",
                relative_urls: false,
                convert_urls: false,
                link_class_list: [
                    {title: '-', value: ''},
                    {title: 'Button', value: 'btn'},
                ],
                content_style: 'body {overflow-x:hidden;padding-bottom:0 !important;}',
                init_instance_callback: function (editor) {
                    loadLayout(editor);
                },
                file_picker_callback: function (callback, value, meta) {
                    tinymce.activeEditor.windowManager.open({
                        file: '{{ route('mediamanager.mce', [], false) }}?type=' + meta.filetype,
                        title: 'File Manager',
                        width: Math.round(window.innerWidth * 0.8),
                        height: Math.round(window.innerHeight * 0.8)
                    }, {
                        oninsert: function (file) {
                            if (meta.filetype === 'image') {
                                callback(file.url, {alt: file.name});
                            }

                            if (meta.filetype === 'file') {
                                callback(file.url, {text: file.name, title: file.name});
                            }
                        }
                    });

                    return false;
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

            if($('#layout_id').val() !== '0') {
                tinymce.activeEditor.getBody().setAttribute('contenteditable', false);
            }
        }
    </script>
@endpush