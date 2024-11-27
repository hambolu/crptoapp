<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/users/{uuid}",
     *     summary="Get User by UUID",
     *     description="Fetch the name, username, and an obfuscated email of a user using their UUID.",
     *     tags={"User"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="The UUID of the user to retrieve.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details retrieved successfully.",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request.",
     *
     *     )
     * )
     */
    public function getUserByUUID($uuid)
    {
        try {
            $user = User::where('uuid', $uuid)->first();

            if (!$user) {
                return $this->errorResponse('User not found.', 404);
            }

            // Obfuscate email
            $email = $user->email;
            $emailParts = explode('@', $email);
            $localPart = $emailParts[0];
            $domain = $emailParts[1];

            // Show only the first and last letters of the local part
            $obfuscatedLocalPart = substr($localPart, 0, 1) . str_repeat('*', strlen($localPart) - 2) . substr($localPart, -1);
            $obfuscatedEmail = $obfuscatedLocalPart . '@' . $domain;

            // Prepare response data
            $userDetails = [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $obfuscatedEmail,
            ];

            return $this->successResponse($userDetails, 'User details retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Could not retrieve user.', 500, ['error' => $e->getMessage()]);
        }
    }

}
