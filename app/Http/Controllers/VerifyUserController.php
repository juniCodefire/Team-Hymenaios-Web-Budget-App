<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Auth;

class VerifyUserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
 
	public function verifyUser(Request $request, User $user)
    {
        $this->validate($request, [
            'verifycode' => 'required|max:6'
        ]);

        $verifycode = $request->input('verifycode');

        $checkCode = User::where('verifycode', $verifycode)->exists();

        if ($checkCode) {

        $user = User::where('verifycode', $verifycode)->first();

        $token = Auth::guard()->login($user);
        
            if ($user->email_verified_at == null){
                $user->email_verified_at = date("Y-m-d H:i:s");
                $user->save();
                
                $msg["message"] = "Account is verified. You can now login.";
                $msg['verified'] = "True";
                $msg['user'] = $user;
                $msg['token'] = 'Bearer ' . $token;
                $msg['image_link'] = 'https://res.cloudinary.com/getfiledata/image/upload/';
                $msg['image_format'] = 'w_200,c_thumb,ar_4:4,g_face/';

                return response()->json($msg, 200);
                
            } else {
                $msg["message"] = "Account verified already. Please Login";
                $msg['note'] = 'please redirect to login page';
                $msg['verified'] = "Done";

                return response()->json($msg, 200);
             }

        } else{

            $msg["message"] = "Account with code does not exist!";

            return response()->json($msg, 409);

        }
            
		
	}
}