<?php

namespace App\Traits;

use Illuminate\Http\Response;

/**
 * ApiResponserTrait Trait.
 */
trait ApiResponserTrait
{
    /**
     * Returns a successful response.
     *
     * @param string|array|null $data   the data thats going to be sent
     * @param int               $status the response status code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($data = null, int $status = Response::HTTP_OK)
    {
        return response()->json(
            compact('status', 'data'),
            $status
        );
    }

    /**
     * Returns a error response.
     *
     * @param string|array $error  the error returned by the application
     * @param int          $status the response status code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($error, int $status)
    {
        return response()->json(
            compact('status', 'error'),
            $status
        );
    }
}
