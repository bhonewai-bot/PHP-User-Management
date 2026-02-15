<?php

declare(strict_types=1);

use App\Presentation\Http\Controllers\AuthController;
use App\Presentation\Http\Controllers\HealthController;
use App\Presentation\Http\Controllers\RoleController;
use App\Presentation\Http\Controllers\UserController;
use App\Presentation\Http\Middlewares\AuthMiddleware;
use App\Presentation\Http\Middlewares\PermissionMiddleware;
use App\Presentation\Http\Routing\Router;

return function (Router $router): void {
    $auth = new AuthMiddleware();
    
    $canRolesRead = new PermissionMiddleware('roles.read');
    $canRolesCreate = new PermissionMiddleware('roles.create');
    $canRolesUpdate = new PermissionMiddleware('roles.update');

    $canUserRead = new PermissionMiddleware('user.read'); 
    $canUserCreate = new PermissionMiddleware('user.create');
    $canUserUpdate = new PermissionMiddleware('user.update');

    // Auth
    $router->get('/', [AuthController::class, 'showLogin']);
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->post('/logout', [AuthController::class, 'logout'], [$auth]);   

    // Health
    $router->get('/health', [HealthController::class, 'index'], [$auth]);

    // Role
    $router->get('/roles', [RoleController::class, 'index'], [$auth, $canRolesRead]);
    $router->get('/roles/create', [RoleController::class, 'create'], [$auth, $canRolesCreate]);
    $router->post('/roles', [RoleController::class, 'store'], [$auth, $canRolesCreate]);
    $router->get('/roles/edit', [RoleController::class, 'edit'], [$auth, $canRolesUpdate]);
    $router->post('/roles/update', [RoleController::class, 'update'], [$auth, $canRolesUpdate]);

    // User
    $router->get('/users', [UserController::class, 'index'], [$auth, $canUserRead]);
    $router->get('/users/create', [UserController::class, 'create'], [$auth, $canUserCreate]);
    $router->post('/users', [UserController::class, 'store'], [$auth, $canUserCreate]);
    $router->post('/users/toggle-active', [UserController::class, 'toggleActive'], [$auth, $canUserUpdate]);
};