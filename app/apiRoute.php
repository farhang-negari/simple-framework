<?php

use FarhangNegari\App\Base\Router;
use FarhangNegari\App\Controller\ApiController;
use FarhangNegari\App\Controller\LoginController;
use FarhangNegari\App\Controller\UrlController;

Router::post('/admin/login', LoginController::class , 'index');
Router::post('/admin/register', LoginController::class , 'register');

Router::get('/admin/url', UrlController::class , 'index',1);
Router::post('/admin/url', UrlController::class , 'create',1);
Router::get('/admin/url/{id}', UrlController::class , 'show',1);
Router::put('/admin/url/{id}', UrlController::class , 'update',1);
Router::delete('/admin/url/{id}', UrlController::class , 'delete',1);

Router::get('/{code}', ApiController::class , 'index');