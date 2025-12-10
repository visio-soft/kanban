<?php

namespace Visiosoft\Kanban\Http\Controllers\API;

use Illuminate\Routing\Controller;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Boards",
 *     description="API Endpoints of Boards"
 * )
 * 
 * @OA\Tag(
 *     name="Issues",
 *     description="API Endpoints of Issues"
 * )
 */
class BaseController extends Controller
{
    public function sendResponse($result, $message)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
