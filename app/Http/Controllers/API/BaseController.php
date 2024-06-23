<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
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
    public function handleResponse($result, $msg, $lastPage = null, $count = null)
    {
        if ($lastPage != null && $count != null) {
            $res = [
                'success'   => true,
                'message'   => $msg,
                'data'      => $result,
                'lastPage'  => $lastPage,
                'count'     => $count
            ];

            return response()->json($res, 200);

        } else {
            if ($lastPage != null && $count == null) {
                $res = [
                    'success'   => true,
                    'message'   => $msg,
                    'data'      => $result,
                    'lastPage'  => $lastPage
                ];

            } else if ($lastPage == null && $count != null) {
                $res = [
                    'success'   => true,
                    'message'   => $msg,
                    'data'      => $result,
                    'count'     => $count
                ];

            } else {
                $res = [
                    'success' => true,
                    'message' => $msg,
                    'data'    => $result
                ];
            }

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
