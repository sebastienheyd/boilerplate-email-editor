<?php

$default = [
    'prefix'     => config('boilerplate.app.prefix', '').'/email-editor',
    'domain'     => config('boilerplate.app.domain', ''),
    'middleware' => [
        'web',
        'boilerplatelocale',
        'boilerplateauth',
        'ability:admin,emaileditor_email_edition,emaileditor_email_dev'
    ],
    'as'         => 'emaileditor.',
    'namespace'  => '\Sebastienheyd\BoilerplateEmailEditor\Controllers',
];

Route::group($default, function () {
    Route::post('email/mce', ['as' => 'email.mce', 'uses' => 'EmailController@getMce']);
    Route::get('email/preview', ['as' => 'email.preview', 'uses' => 'EmailController@preview']);
    Route::post('email/preview', ['as' => 'email.preview.post', 'uses' => 'EmailController@previewPost']);
    Route::post('email/preview/email', ['as' => 'email.preview.email', 'uses' => 'EmailController@previewEmail']);
    Route::post('email/datatable', ['as' => 'email.datatable', 'uses' => 'EmailController@datatable']);
    Route::post('email/content', ['as' => 'email.content', 'uses' => 'EmailController@content']);
    Route::resource('email', 'EmailController');
});
