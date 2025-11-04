<?php

use App\Modules\Auth\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Hash;
use \Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('user/login', function (Request $request) {
    $user = User::Where('email', $request->email)->first();
    if ($user && Hash::check($request->password, $user->password)) {
        $objToken = $user->createToken('User');
        $strToken = $objToken->plainTextToken;
        $response = \App\Helpers\Helper::createSuccessResponse([
            "exist" => $user->email ? true : false,
            "token_type" => "Bearer",
            "token" => $strToken,
            'user' => $user
        ], "Welcome back $user->name");
        return response()->json($response, 200);
    } else {
        $response = \App\Helpers\Helper::createErrorResponse([], 'Incorrect Email or Password');
        return response()->json($response, 401);
    }
});
