<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

    /**
     * @OA\Info(
     *     version="1.0.0",
     *     title="task for net-farma",
     *     description="task for net-farma Api Documentation",
     *     @OA\Contact(
     *         name="Saber Qadimi",
     *         email="qadimi43@gmail.com"
     *     ),
     *     @OA\License(
     *         name="Apache 2.0",
     *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *     )
     * ),
     * @OA\Server(
     *     url="/api/v1",
     * ),
     */
class Controller extends BaseController
{


    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
