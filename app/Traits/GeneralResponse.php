<?php


namespace App\Traits;

/**
 * General Response
 */
trait GeneralResponse
{
    /**
     * handle Error response
     * @ params {$errNum: Number, $errMSG: String}
     * @ return {status , message}
     */
    public function errorResponse($errMSG, $errNum)
    {
        return response()->json([
            "status" => false,
            "message" => $errMSG
        ], $errNum);
    }

    /**
     * handle Error response
     * @param Number $errNum  String $massege
     * @return {status , message}
     */
    public function successResponse($massege, $errNum)
    {
        return response()->json([
            "status" => true,
            "message" => $massege
        ], $errNum);
    }

    /**
     * handle Error response
     * @ params {$errNum: Number,$data: Array}
     * @ return {data }
     */
    public function dataResponse($data, $errNum)
    {
        return response()->json($data, $errNum);
    }
}