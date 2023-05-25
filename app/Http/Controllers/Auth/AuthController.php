<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function login(AuthRequest $request)
    {
        $email = Str::lower($request->email);
        $credentials = [
            'email' => $email,
            'password' => $request->password,
        ];
        $token = auth('api')->attempt($credentials);
        if ($token === false) {
            return response()->json(['errors' => [
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => __('validation.login'),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($this->respondWithToken($token));
    }

    public function register(AuthRequest $request)
    {
        $email = Str::lower($request->email);
        $credentials = [
            'email' => $email,
            'password' => $request->password,
        ];

        $model = User::where('email', $email)->first();


        if (!empty($model)) {
            return response()->json(['errors' => [
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => __('validation.login'),
            ],
            ], 422);
        }

        $user = new User();
        $user->email = $email;
        $user->name = $request->name;
        $user->password = bcrypt(request('password'));
        $user->save();
        $token = auth('api')->attempt($credentials);
        if ($token === false) {
            return response()->json(['errors' => [
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => __('validation.login'),
            ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($this->respondWithToken($token));
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $user = auth('api')->user();

        return response()->json($user);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json($this->respondWithToken(auth('api')->refresh()));
    }

    private function respondWithToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }
}
