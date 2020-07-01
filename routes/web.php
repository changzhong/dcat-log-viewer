<?php

use Dcat\Admin\Extension\LogViewer\Http\Controllers;

Route::get('logs', Controllers\LogViewerController::class.'@index')->name('log-viewer-index');
Route::get('logs/{file}', Controllers\LogViewerController::class.'@index')->name('log-viewer-file');
Route::get('logs/{file}/tail', Controllers\LogViewerController::class.'@tail')->name('log-viewer-tail');
