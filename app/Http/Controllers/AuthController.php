<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {

        //to create RegisterRequest i wrote in command line "php artisan make:request RegisterRequest"
        //automaticly check the filed that we sending for validation

        $pasword = Hash::make($request['password']);

        //generage automaticly "userName" from email that we will send


        $user = User::create([
            'email' => $request->email,
            'password' => $pasword,
            'user_name' => $request->user_name
        ]);

        $token = $user->createToken('register-token')->plainTextToken; //we need that our user model extends Authenticatable, and than this line created token


        return response([
            'success' => true,
            'token' => $token,
            'user' => $user
        ]);
    }


    public function login(LoginRequest $request)
    {


        $user = User::where('email', $request['email'])->first();

        if (!$user) {
            return response(['success' => false, "message" => 'user does not exists']);
        }

        if (!Hash::check($request['password'], $user['password'])) {
            return response(['success' => false, 'password error']);
        }

        return response([
            'success' => true,
            'user' => $user,
            'token' => $user->createToken('login-token')->plainTextToken
        ]);
    }

    public function check_token(Request $request)
    {
        $user = $request->user();

        if ($user) {
            return response(['success' => true, 'user' => $user]);
        }

        return response(['success' => false, 'no token']);
    }

    public function log_out(Request $request)
    {
        $user = $request->user();


        // $user->currentAccessToken()->delete();// to delete current token

        $user->tokens()->delete(); // to delete all user's tokens

        return response(['success' => true]);
    }


    //
    public function save_image_in_folder(Request $request, $customer)
    {
        $parentFolder = 'images_folder';

        if ($request->file('user_image')) {

            $image = $request->file('user_image');

            $ext = $image->getClientOriginalExtension();

            $image_name = "image_" . "{$customer->id}_" . Str::random(25) . "." . $ext;

            $save_image = Image::make($image);

            $save_image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });


            $path = storage_path("/app/{$parentFolder}");


            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            $customer_path = "{$path}/customers_pictures";


            if (!is_dir($customer_path)) {
                mkdir($customer_path, 0755, true);
            }

            if (File::exists("{$customer_path}/{$customer->image_url}")) {
                File::delete("{$customer_path}/{$customer->image_url}");
            }


            $save_image->save("{$customer_path}/{$image_name}");


            // Customer::where('id', $customer->id)->update([
            //     'image_url' => $image_name
            // ]);
        }
    }


    //
    public function get_image()
    {

        $customer = null;

        if (!$customer) {
            return response()->file(public_path('/temp_pictures/user-gray-256.png'));
        }

        if (!$customer->image_url) {
            return response()->file(public_path('/temp_pictures/user-gray-256.png'));
        }

        $parentPath = "images_folder";

        $customer_path = storage_path("/{$parentPath}/customers_pictures");

        if (!is_dir($customer_path)) {
            return response()->file(public_path('/temp_pictures/user-gray-256.png'));
        }

        if (!File::exists("{$customer_path}/{$customer->image_url}")) {
            return response()->file(public_path('/temp_pictures/user-gray-256.png'));
        }

        return response()->file("{$customer_path}/{$customer->image_url}");
        // if(!$customer->)


    }

    public function get_video()
    {
        throw 1;
        return response()->file(storage_path('/app/videos/bee.mp4'));
    }

    public function simple_json()
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'Simple JSON response',
            'timestamp' => now(),
        ]);
    }
}
