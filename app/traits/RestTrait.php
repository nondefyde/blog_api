<?php
/**
 * Created by PhpStorm.
 * User: ZuumaPC
 * Date: 12/29/2016
 * Time: 12:26 PM
 */

namespace App\Traits;

use Illuminate\Http\Request;

trait RestTrait
{

    /**
     * Determines if request is an api call.
     *
     * If the request URI contains '/api/v'.
     *
     * @param Request $request
     * @return bool
     */
    protected function isApiCall(Request $request)
    {
        return strpos($request->getUri(), '/api/v1') !== false;
//        return $request->is('api/*');
    }

}