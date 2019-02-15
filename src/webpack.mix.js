let mix = require('laravel-mix');
const Clean = require('clean-webpack-plugin');

mix.webpackConfig({plugins: [new Clean(['public'], {verbose: false})]})
    .setPublicPath("public")
    .setResourceRoot('/assets/vendor/boilerplate-email-editor');

// ======= Code Mirror
mix.scripts([
    'node_modules/codemirror/lib/codemirror.js',
    'node_modules/codemirror/mode/xml/xml.js',
    'node_modules/codemirror/mode/css/css.js',
    'node_modules/codemirror/mode/javascript/javascript.js',
    'node_modules/codemirror/mode/htmlmixed/htmlmixed.js',
    'node_modules/codemirror/addon/edit/closetag.js',
    'node_modules/codemirror/addon/fold/xml-fold.js',
    'node_modules/codemirror/addon/edit/matchtags.js',
], 'public/vendor/codemirror/codemirror.min.js').version();

mix.styles('node_modules/codemirror/lib/codemirror.css',
    'public/vendor/codemirror/codemirror.min.css').version();

// ======= TinyMCE

mix.copy('node_modules/tinymce/plugins', 'public/vendor/tinymce/plugins');
mix.copy('node_modules/tinymce/skins', 'public/vendor/tinymce/skins');
mix.copy('node_modules/tinymce/themes', 'public/vendor/tinymce/themes');

// https://www.tiny.cloud/get-tiny/language-packages/
mix.copy('resources/js/vendor/tinymce/langs', 'public/vendor/tinymce/langs');

mix.scripts([
    'node_modules/tinymce/tinymce.min.js',
    'node_modules/tinymce/jquery.tinymce.min.js'
], 'public/vendor/tinymce/tinymce.min.js').version();