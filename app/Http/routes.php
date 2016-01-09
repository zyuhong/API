<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
        return abort(403);
    }
);

$app->get('/watermark/list', 'WatermarkController@catList');
$app->post('/watermark/list', 'WatermarkController@catList');
$app->get('/watermark/detail', 'WatermarkController@detail');
$app->post('/watermark/detail', 'WatermarkController@detail');
$app->get('/watermark/check', 'WatermarkController@check');
$app->post('/watermark/check', 'WatermarkController@check');
