<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\Sms\SmsService;
use App\Models\User;
use App\Models\User\Otp;
use App\Models\User\Role;
use App\Http\Services\Cart\CartMergeService;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

use function Pest\Laravel\json;

class RegisteredUserController extends Controller
{

    public function loginWithPassword(Request $request)
    {
        $validate = $request->validate([
            "mobile" => ["require", 'string', 'min:11', 'max:15'],
            "password" => ['require', 'string'],
        ], [
            'mobile.required' => 'شماره موبایل الزامی است.',
            'mobile.string' => 'شماره موبایل باید یک رشته باشد.',
            'mobile.min' => 'شماره موبایل باید حداقل ۱۱ رقم باشد.',
            'mobile.max' => 'شماره موبایل باید حداکثر ۱۵ رقم باشد.',
        ]);

        $user = User::where("mobile", $validate['mobile'])->first();

        if (!$user) {
            return response()->json([
                'message' => "کاربر یافت نشد"
            ], 404);
        }
        if (!$user->password) {
            return response()->json([
                'message' => "ورود با پسورد امکان پذیر نمی‌باشد"
            ], 401);
        }
        if (!Hash::check($validate['password'], $user->password)) {
            return response()->json([
                'message' => "پسورد وارد شده صحیح نمی‌باشد"
            ], 422);
        }
        Auth::login($user);
        app(CartMergeService::class)->merge(
            $request->session()->getId(),
            auth()->user()
        );
        return response()->json([
            'message' => 'با موفقیت وارد شدید',
        ], 200);
    }

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

        // $smsService->sendSmsOtp($request->mobile, $otpCode);

        return response()->json([
            'message' => 'کد تایید با موفقیت ارسال شد ',
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

        $otp = Otp::where('login_id', $request->mobile)
            ->where('token', $request->token)
            ->where('used', 0)
            ->first();

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
            if (!$user->mobile_verified_at) {
                $user->update(['mobile_verified_at' => now()]);
            }

            $this->loginAndMergeCart($request, $user);

            return response()->json([
                'message' => 'با موفقیت وارد شدید',
            ], 200);
        }

        $user = User::create([
            'mobile' => $request->mobile,
            'mobile_verified_at' => now(),
        ]);

        $userRole = Role::where('slug', 'user')->first();
        if ($userRole) {
            $user->roles()->attach($userRole);
        }

        event(new Registered($user));

        $this->loginAndMergeCart($request, $user);

        return response()->json([
            'message' => 'با موفقیت ثبت نام و وارد شدید',
        ], 200);
    }

    private function loginAndMergeCart(Request $request, User $user): void
    {
        $guestSessionId = $request->session()->getId();

        Auth::login($user);

        app(CartMergeService::class)->merge($guestSessionId, $user);

        $request->session()->regenerate();
    }
}
