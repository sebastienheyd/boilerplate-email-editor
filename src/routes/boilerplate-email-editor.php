<?php

$default = [
    'prefix'     => config('boilerplate.app.prefix', '').'/email-editor',
    'domain'     => config('boilerplate.app.domain', ''),
    'middleware' => ['web', 'boilerplatelocale', 'boilerplateauth', 'ability:admin,backend_access'],
    'as'         => 'emaileditor.',
    'namespace'  => '\Sebastienheyd\BoilerplateEmailEditor\Controllers',
];

Route::group($default, function () {

    // Layouts
    Route::get('layout/preview', ['as' => 'layout.preview', 'uses' => 'EmailLayoutController@preview']);
    Route::post('layout/preview', ['as' => 'layout.preview.post', 'uses' => 'EmailLayoutController@previewPost']);
    Route::post('layout/datatable', ['as' => 'layout.datatable', 'uses' => 'EmailLayoutController@datatable']);
    Route::post('layout/preview/email', [
        'as'   => 'layout.preview.email',
        'uses' => 'EmailLayoutController@previewEmail',
    ]);
    Route::post('layout/mce', ['as' => 'layout.mce', 'uses' => 'EmailLayoutController@getMce']);
    Route::resource('layout', 'EmailLayoutController');

    // Emails
    Route::get('email/preview', ['as' => 'email.preview', 'uses' => 'EmailController@preview']);
    Route::post('email/preview', ['as' => 'email.preview.post', 'uses' => 'EmailController@previewPost']);
    Route::post('email/preview/email', ['as' => 'email.preview.email', 'uses' => 'EmailController@previewEmail']);
    Route::post('email/datatable', ['as' => 'email.datatable', 'uses' => 'EmailController@datatable']);
    Route::post('email/content', ['as' => 'email.content', 'uses' => 'EmailController@content']);
    Route::resource('email', 'EmailController');
});
