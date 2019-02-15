@push('css')
    <link rel="stylesheet" href="{{ mix('/vendor/codemirror/codemirror.min.css', '/assets/vendor/boilerplate-email-editor') }}">
    <style>.CodeMirror {border:1px solid #CCC;height:auto;font-size: 12px}</style>
@endpush

@push('js')
    <script src="{{ mix('/vendor/codemirror/codemirror.min.js', '/assets/vendor/boilerplate-email-editor') }}"></script>

    @isset($id)
    <script>
        var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("{{ $id }}"), {
            lineNumbers: true,
            mode: 'text/html',
            autoCloseTags: true,
            matchTags: true
        });
    </script>
    @endisset
@endpush