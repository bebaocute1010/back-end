<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Response;

class Responses
{
    public function success(string $message) {
        return \response()->json(['message' => $message], Response::HTTP_OK);
    }

    public function successWithData($data) {
        return \response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function successWithToken(string $token) {
        return \response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function error(string $message, int $status) {
        return \response()->json(['message' => $message], $status);
    }

    public function exceptionError(\Throwable $exception) {
        return \response()->json(['message' => $exception->getMessage()], Response::HTTP_CONFLICT);
    }

    public function unauthorized() {
        return \response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }
}
