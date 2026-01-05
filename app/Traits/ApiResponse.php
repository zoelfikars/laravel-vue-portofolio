<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    protected function success($data, string $message = null, int $code = 200): JsonResponse
    {
        $response = [
            'message' => $message,
            'code' => $code,
        ];

        if ($data instanceof ResourceCollection) {
            $resourceResponse = $data->response()->getData(true);

            $response['data'] = $resourceResponse['data'] ?? $data;
            
            $meta = $resourceResponse['meta'] ?? $resourceResponse['links'] ?? [];
            
            $response['meta'] = array_intersect_key($meta, array_flip([
                'current_page', 'from', 'last_page', 'per_page', 'to', 'total'
            ]));
        } elseif ($data instanceof JsonResource) {
            $response['data'] = $data;
        } else {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
    protected function error(string $message, int $code, $errors = null): JsonResponse
    {
        $response = [
            'message' => $message,
            'code' => $code,
            'errors' => $errors,
        ];

        return response()->json($response, $code);
    }
}
