<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return $this->sendError('Invalid email or password', [], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('MyApp')->plainTextToken;

        $data = $this->createResponseData($user, $token);

        return $this->sendResponse($data, 'User authenticated successfully.');
    }

    private function createResponseData($user, $token)
    {
        return [
            "type" => "sessions",
            "token" => $token,
            "attributes" => [
                "user_id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "created_at" => $user->created_at,
                "updated_at" => $user->updated_at
            ],
            "links" => [
                "self" => url("/users/".$user->id)
            ]
        ];
    }

    public function sendResponse($result, $message)
    {
        $response = [
            'data' => $result,
            'meta' => [
                'message' => $message
            ]
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 401)
    {
        $response = [
            'error' => $error,
            'errorMessages' => $errorMessages,
        ];

        return response()->json($response, $code);
    }

    public function destroy(Request $request, $allDevices = true)
    {
        $user = $request->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'data' => [],
                'meta' => [
                    'message' => 'User is not authenticated.'
                ]
            ], 401); // HTTP status code 401: Unauthorized
        }

        if ($allDevices) {
            $user->tokens()->delete();
            $message = 'User logged out from all devices successfully.';
        } else {
            $user->currentAccessToken()->delete();
            $message = 'User logged out from current device successfully.';
        }

        return response()->json([
            'data' => [],
            'meta' => [
                'message' => $message
            ]
        ], 200);
    }
}
