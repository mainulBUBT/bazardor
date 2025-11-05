<?php

namespace App\Services\Api;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuthService
{
    public function __construct(private User $user)
    {
    }

    /**
     * Summary of register
     * @param array $data
     * @param mixed $zoneId
     * @return array{access_token: string, message: array, success: bool, user: User}
     */
    public function register(array $data, ?string $zoneId = null): array
    {
        $attributes = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'dob' => $data['dob'],
            'password' => Hash::make($data['password']),
            'city' => $data['city'],
            'division' => $data['division'],
            'gender' => $data['gender'],
            'is_active' => true,
            'zone_id' => $zoneId,
        ];

        if (!empty($data['phone'])) {
            $attributes['phone'] = $data['phone'];
        }

        if (!empty($data['referred_by'])) {
            $attributes['referred_by'] = $data['referred_by'];
        }

        if (!empty($data['image'])) {
            $image = $data['image'];
            $extension = $image->getClientOriginalExtension();
            $attributes['image_path'] = handle_file_upload('users/', $extension, $image, null);
        }

        $attributes['referral_code'] = Str::upper(Str::random(8));

        $user = $this->user->create($attributes);

        $token = $user->createToken('api-token');

        return [
            'success' => true,
            'message' => AUTH_REGISTER_200,
            'user' => $user,
            'access_token' => $token->plainTextToken,
        ];
    }

    /**
     * Summary of login
     * @param array $credentials
     * @param mixed $zoneId
     * @return array{access_token: string, message: array, success: bool, user: User}
     */
    public function login(array $credentials, ?string $zoneId = null): array
    {
        $user = $this->user->where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return [
                'success' => false,
                'message' => AUTH_FAILURE_401,
            ];
        }

        if (!$user->is_active) {
            return [
                'success' => false,
                'message' => AUTH_INACTIVE_401,
            ];
        }

        $user->zone_id = $zoneId;
        $user->last_login_at = now();
        $user->subscribed_to_newsletter = true;
        $user->save();
        $token = $user->createToken('api-token');

        return [
            'success' => true,
            'message' => AUTH_LOGIN_200,
            'user' => $user,
            'access_token' => $token->plainTextToken,
        ];
    }

    /**
     * Summary of sendPasswordResetOtp
     * @param array $data
     * @return array{message: array, success: bool|array{message: array{message: string, response_code: string}, success: bool}}
     */
    public function sendPasswordResetOtp(array $data)
    {
        $user = $this->user->where('email', $data['email'])->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => DEFAULT_404,
            ];
        }

        // Generate 6-digit OTP
        $otp = env('APP_ENV') == 'live' ? str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT) : '123456';
        $expires_at = env('APP_ENV') == 'live' ? 3 : 1000;

        $attributes = [
            'email' => $user->email,
            'token' => $otp,
            'expires_at' => Carbon::now()->addMinutes($expires_at),
            'created_at' => now()
        ];

        $data = DB::table('password_reset_tokens')->where(['email' => $user->email])->first();
        $resend_after = 15;
        if ($data && Carbon::parse($data->created_at)->diffInSeconds() < $resend_after) {
            return [
                'success' => false,
                'message' => [
                    'response_code' => 'otp_too_many_attempt_405',
                    'message' => translate('messages.please_try_again_after_') . CarbonInterval::seconds($resend_after - Carbon::parse($data->created_at)->diffInSeconds())->forHumans(),
                ],
            ];
        }

        if ($data) {
            DB::table('password_reset_tokens')->where(['email' => $user->email])->delete();
        }

        DB::table('password_reset_tokens')->insert($attributes);

        // Send OTP via email
        try {
            Mail::send('admin.email-templates.password-reset-otp', [
                'user' => $user,
                'otp' => $otp,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Password Reset OTP - ' . config('app.name'));
            });

            return [
                'success' => true,
                'message' => OTP_SENT_200,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => DEFAULT_500,
            ];
        }
    }

    /**
     * Summary of resetPassword
     * @param array $data
     * @return array{message: array, success: bool|array{message: mixed, success: bool}}
     */
    public function resetPassword(array $data): array
    {
        $user = $this->user->where('email', $data['email'])->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => DEFAULT_404,
            ];
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return [
            'success' => true,
            'message' => DEFAULT_200,
        ];
    }

    /**
     * Summary of verifyOtp
     * @param array $data
     * @return array{message: array, success: bool|array{message: mixed, success: bool}}
     */
    public function verifyOtp(array $data): array
    {
        $otp = DB::table('password_reset_tokens')->where(['email' => $data['email']])->first();
        $user = $this->user->where('email', $data['email'])->first();
        
        if(!$otp){
            return [
                'success' => false,
                'message' => OTP_INVALID_403    ,
            ];
        }

        if ($otp->is_temp_blocked) {
            $seconds_passed = Carbon::parse($otp->temp_blocked_at)->diffInSeconds();
            if ($seconds_passed < TEMP_BLOCK_DURATION) {
                return [
                    'success' => false,
                    'message' => [
                        'response_code' => 'otp_too_many_attempt_405',
                        'message' => translate('lang.please_try_again_after_') . CarbonInterval::seconds(TEMP_BLOCK_DURATION - $seconds_passed)->forHumans()
                    ],
                ];
            }
            $otp->update(['is_temp_blocked' => false, 'temp_blocked_at' => null, 'otp_hit_count' => 0]);
        }

        if (Carbon::parse($otp->expires_at) > now() && $otp->token == $data['otp']) {
            DB::table('password_reset_tokens')->where(['email' => $data['email']])->delete();
            return [
                'success' => true,
                'message' => OTP_VERIFIED_200,
            ];
        }

        $otp->increment('otp_hit_count');

        if ($otp->otp_hit_count >= MAX_OTP_HIT) {
            $otp->update(['is_temp_blocked' => true, 'temp_blocked_at' => now()]);
        }

        return [
            'success' => false,
            'message' => OTP_INVALID_403,
        ];
    }

    /**
     * Summary of logout
     * @param User $user
     * @return array{message: array, success: bool|array{message: mixed, success: bool}}
     */
    public function logout(User $user): array
    {
        $user->tokens()->delete();
        return [
            'success' => true,
            'message' => AUTH_LOGOUT_200,
        ];
    }
}
