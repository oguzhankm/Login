<?php

namespace App\Http\Controllers;


use App\Mail\ForgotPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function register()
    {
        if (session()->has('loggedInUser')) {
            return redirect('/profile');
        } else {
            return view('auth.register');
        }
    }

    public function forgot()
    {
        if (session()->has('loggedInUser')) {
            return redirect('/profile');
        } else {
            return view('auth.forgot');
        }

    }

    public function reset(Request $request)
    {
        $email = $request->email;
        $token = $request->token;
        return view('auth.reset', ['email' => $email, 'token' =>$token]);
    }

    //Register ajax istekleri
    public function saveUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:100',
            'password' => 'required|min:6|max:50',
            'cpassword' => 'required|min:6|same:password',
        ], [
            'cpassword.same' => 'Girdiğiniz şifre eşleşmedi!',
            'cpassword.required' => 'Şifre onayı gereklidir!'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'messages' => $validator->getMessageBag()
            ]);
        } else {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json([
                'status' => 200,
                'messages' => 'Başarıyla Kaydedildi!'
            ]);
        }
    }


    //Login yapan user istekleri

    public function loginUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100',
            'password' => 'required|min:6|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'messages' => $validator->getMessageBag()
            ]);
        } else {
            $user = User::all()->where('email', $request->email)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    $request->session()->put('loggedInUser', $user->id);
                    return response()->json([
                        'status' => 200,
                        'messages' => 'success'
                    ]);
                } else {
                    return response()->json([
                        'status' => 401,
                        'messages' => 'E-mail ve ya şifreniz doğru değil!'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 401,
                    'messages' => 'Kullanıcı bulunamadı!'
                ]);
            }
        }

    }

    //Profil Sayfası
    public function profile()
    {
        return view('profile');
    }

    //Çıkış Sayfası

    public function logout()
    {
        if (session()->has('loggedInUser')) {
            session()->pull('loggedInUser');
            return redirect('/');
        }
    }


    //Forgot Password ajax istekleri

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'messages' => $validator->getMessageBag()
            ]);
        } else {
            $token = Str::uuid();
            $user = DB::table('users')->where('email', $request->email)->first();
            $details = [
                'body' => route('reset', ['email' => $request->email, 'token' => $token])
            ];

            if ($user) {
                User::query()->where('email', $request->email)->update([
                    'token' => $token,
                    'token_expire' => Carbon::now()->addMinutes(10)->toDateTimeString()
                ]);

                Mail::to($request->email)->send(new ForgotPassword($details));
                return response()->json([
                    'status' => 200,
                    'messages' => 'Şifre sıfırlama linki e-mail adresinize gönderildi. Lütfen gelen kutunuzu kontrol ediniz!'
                ]);
            } else {
                return response()->json([
                    'status' => 401,
                    'messages' => 'Bu e-mail adresi sistemimizde kayıtlı değildir!'
                ]);
            }
        }
    }


    //Reset Password ajax istekleri

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npass' => 'required|min:6|max:50',
            'cpass' => 'required|min:6|max:50|same:npass'
        ], [
            'cpass.same' => 'Şifre eşleşmedi!'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'messages' => $validator->getMessageBag()
            ]);
        }else {
            $token = Str::uuid();
            $user = DB::table('users')->where('email', $request->email)->whereNotNull('token')->where('token', $request->token)->where('token_expire', '>', Carbon::now())->exists();

            if ($user){
                User::query()->where('email', $request->email)->update([
                   'password' => Hash::make($request->npass),
                    'token' => null,
                    'token_expire' => null
                ]);

                return response()->json([
                    'status' => 200,
                    'messages' => 'Şifreniz başarıyla değiştirildi:&nbsp;<a href="/">Giriş Yapın</a>'
                ]);
            }else{
                return response()->json([
                    'status' => 401,
                    'messages' => 'Şifre sıfırlama link süresi doldu! Lütfen tekrar şifre sıfırlama isteği oluşturunuz.'
                ]);
            }
        }
    }
}
