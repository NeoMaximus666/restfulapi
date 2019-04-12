<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @SWG\Swagger(
 *   schemes={"http"},
 *   host="restfulapi",
 *   basePath="/",
 *   @SWG\Info(
 *     title="Meeting Scheduler API",
 *     version="1.0.0"
 *   )
 * )
 */
/**
 * @SWG\SecurityScheme(
 *   securityDefinition="Basic",
 *   type="basic"
 * ),
 * @SWG\SecurityScheme(
 *   securityDefinition="JWT",
 *   type="apiKey",
 *   in="header",
 *   name="Authorization"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
