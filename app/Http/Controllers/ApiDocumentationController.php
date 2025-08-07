<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ApiDocumentationController extends Controller
{
    public function index()
    {
        $apiRoutes = [];
        $routes = Route::getRoutes()->getRoutes();

        foreach ($routes as $route) {
            // กรองเอาเฉพาะ Route ที่มี prefix 'api/'
            if (strpos($route->uri(), 'api/') === 0) {
                $apiRoutes[] = [
                    'uri' => '/' . $route->uri(),
                    'methods' => $route->methods(),
                    'action' => $route->getActionName(),
                    'middleware' => $route->gatherMiddleware(),
                ];
            }
        }

        return view('docs.api', compact('apiRoutes'));
    }
}
