<?php

namespace Larfree\Responses;

use App\Http\Resources\ApiResource;
use Illuminate\Http\JsonResponse;

class ApiResponse extends JsonResponse
{


    /**
     * Constructor.
     *
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     */
    public function __construct($data = null, $status = 200, $headers = [], $options = 0)
    {
//        $data = new ApiResource($data);
        $this->encodingOptions = $options;
        parent::__construct($data, $status, $headers);
    }

}
