<?php

use Illuminate\Support\Facades\Route;
use Uccello\Core\Facades\Uccello;

Route::middleware('web', 'auth')
->namespace('Uccello\Import\Http\Controllers')
->name('uccello.import.')
->group(function () {

    // This makes it possible to adapt the parameters according to the use or not of the multi domains
    if (!Uccello::useMultiDomains()) {
        $domainAndModuleParams = '{module}';
    } else {
        $domainAndModuleParams = '{domain}/{module}';
    }

    Route::get($domainAndModuleParams.'/import', 'ImportController@index')->name('index');
    Route::post($domainAndModuleParams.'/import/prepare', 'ImportController@prepare')->name('prepare');
    Route::post($domainAndModuleParams.'/import/process', 'ImportController@process')->name('process');
    Route::get($domainAndModuleParams.'/import/field/config', 'ImportController@fieldConfig')->name('field.config');
});
