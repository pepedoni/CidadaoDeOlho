<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/preencherDados', 'DeputadoController@preencherDados');
Route::get('/deputados_por_verba/{mes}', 'DeputadoController@getDeputadosOrdenadosPorVerba');
Route::get('/redes_sociais_mais_utilizadas', 'DeputadoController@getRedesSociaisMaisUtilizadas');