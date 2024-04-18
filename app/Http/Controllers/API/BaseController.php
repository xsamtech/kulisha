<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

/**
 * @author Xanders
 * @see https://www.linkedin.com/in/xanders-samoth-b2770737/
 */
class BaseController extends Controller
{
    /**
     * Handle response
     *
     * @param  $result
     * @param  $msg
     * @return \Illuminate\Http\Response
     */
    public function handleResponse($result, $msg, $lastPage = null)
    {
        if ($lastPage != null) {
            $res = [
                'success' => true,
                'message' => $msg,
                'data'    => $result,
                'lastPage'    => $lastPage
            ];

            return response()->json($res, 200);

        } else {
            $res = [
                'success' => true,
                'message' => $msg,
                'data'    => $result
            ];

            return response()->json($res, 200);
        }
    }

    /**
     * Handle response error
     *
     * @param  $error
     * @param array  $errorMsg
     * @param  $code
     * @return \Illuminate\Http\Response
     */
    public function handleError($error, $errorMsg = [], $code = 404)
    {
        if (empty($errorMsg)) {
			$res = [
				'success' => false,
				'message' => $error
			];

			return response()->json($res, $code);
        }

        if (!empty($errorMsg)) {
			$res = [
				'success' => false,
				'data' => $error
			];

            $res['message'] = $errorMsg;

			return response()->json($res, $code);
        }
    }
}
