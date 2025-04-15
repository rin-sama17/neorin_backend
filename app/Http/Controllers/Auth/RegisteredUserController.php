<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\Sms\SmsService;
use App\Models\User;
use App\Models\User\Otp;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{

    public function sendOtp(Request $request, SmsService $smsService)
    {
        $request->validate([
            'mobile' => ['required', 'string', 'min:11', 'max:15'],
        ], [
            'mobile.required' => 'شماره موبایل الزامی است.',
            'mobile.string' => 'شماره موبایل باید یک رشته باشد.',
            'mobile.min' => 'شماره موبایل باید حداقل ۱۱ رقم باشد.',
            'mobile.max' => 'شماره موبایل باید حداکثر ۱۵ رقم باشد.',
        ]);
        $otpCode = random_int(1000, 9999);
        $token = Str::random(60);

        $user = User::where('mobile', $request->mobile)->first();


        Otp::updateOrCreate(
            ['login_id' => $request->mobile, 'used' => 0],
            [
                'token' => $token,
                'login_id' => $request->mobile,
                'otp_code' => $otpCode,
                'type' => 0,
                'attempts' => 0,
                'user_id' => $user ? $user->id : null,
            ]
        );

        $smsService->sendSmsOtp($request->mobile, $otpCode);

        return response()->json([
            'message' => 'کد تایید با موفقیت ارسال شد ' . $otpCode . '  :کد تایید شما(در محصول نهایی کد تایید از طریق پیامک ارسال خواهد شد )',
            'token' => $token,
        ], 200);
    }


    public function verifyOtpAndRegister(Request $request)
    {

        $request->validate([
            'mobile' => ['required', 'string', 'max:15'],
            'otp' => ['required', 'string', 'size:4'],
            'token' => ['required', 'string'],
        ]);

        $otp = Otp::where('login_id', $request->mobile)->where('token', $request->token)->where('used', 0)->first();


        if (!$otp) {
            return response()->json([
                'message' => 'کد تایید یافت نشد',
            ], 422);
        }

        if ($otp->attempts >= 3) {
            return response()->json([
                'message' => 'تعداد دفعات مجاز این کد به پایان رسیده است',
            ], 429);
        }

        if (Carbon::now()->diffInMinutes($otp->created_at) > 5) {
            return response()->json([
                'message' => 'زمان مجاز این کد به پایان رسیده است',
            ], 422);
        }

        if ($otp->otp_code !== $request->otp) {
            $otp->increment('attempts');
            return response()->json([
                'message' => 'کد وارد شده صحیح نمیباشد',
            ], 422);
        }

        $otp->update(['used' => 1]);

        $user = User::where('mobile', $request->mobile)->first();
        if ($user) {
            Auth::login($user);
            return response()->json([
                'message' => 'با موفقیت وارد شدید',
            ], 200);
        } else {
            $user = User::create([
                'password' => Hash::make(Str(10)),
                'mobile' => $request->mobile,
                'city_id' => $request->city_id,
                "mobile_verified_at" => now()
            ]);

            event(new Registered($user));

            Auth::login($user);
            return response()->json([
                'message' => 'با موفقیت ثبت نام و وارد شدید',
            ], 200);
        };
    }
}





/**
 * Handle an incoming registration request.
 *
 * @throws \Illuminate\Validation\ValidationException
 */

//     public function store(Request $request): Response
//     {
//         $request->validate([
//             'name' => ['required', 'string', 'max:255'],
//             'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
//             'password' => ['required', 'confirmed', Rules\Password::defaults()],
//             'mobile' => ['required', 'string','max:15','unique:'.User::class],
//             'city_id' => ['required', 'exists:cities,id'],
//         ]);

//         $user = User::create([
//             'name' => $request->name,
//             'email' => $request->email,
//             'password' => Hash::make($request->string('password')),
//             'mobile' => $request->mobile,
//             'city_id' => $request->city_id,

//         ]);

//         event(new Registered($user));

//         Auth::login($user);

//         return response()->noContent();
//     }
// }
