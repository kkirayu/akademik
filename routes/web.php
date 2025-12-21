<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/clear-deploy', function() {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return "Semua Cache Berhasil Dihapus! Silakan coba API lagi.";
});

Route::get('/cek-route', function () {
    $routes = [];
    foreach (Route::getRoutes() as $route) {
        $routes[] = [
            'method' => implode('|', $route->methods()),
            'uri'    => $route->uri(),
            'name'   => $route->getName(),
            'action' => $route->getActionName(),
        ];
    }
    return $routes;
});