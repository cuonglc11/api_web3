<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
/**
 * @OA\Info(
 *    title="API Wallet Management",
 *    version="1.0.0",
 *    description="API Wallet Management"
 * )
 * @OA\SecurityScheme(
 *    securityScheme="Bearer",
 *    type="apiKey",
 *    in="header",
 *    name="Authorization",
 *    description="Enter token in format (Bearer <token>)"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

}
