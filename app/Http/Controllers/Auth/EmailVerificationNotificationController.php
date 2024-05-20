<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return $this->createJsonResponse('Email already verified', 422);
        }

        $user->sendEmailVerificationNotification();

        return $this->createJsonResponse('Verification link sent');
    }

    /**
     * Create a JSON response.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createJsonResponse(string $message, int $statusCode = 200): JsonResponse
    {
        return response()->json(['message' => $message], $statusCode);
    }
}
