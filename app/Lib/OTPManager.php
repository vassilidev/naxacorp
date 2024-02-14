<?php

namespace App\Lib;

use App\Models\OtpVerification;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Generate One Time Password (OTP)
 * Send the otp to users
 * Verify the user for further action
 */
class OTPManager
{
    /** @var object
     * Contains the instance of otp_verifiable_type model
     */
    public $parent;

    /** @var string
     * How the code will be sent to the user
     * The value will be email or sms
     */
    public $sendVia;

    /** @var string
     * Which notification template will be used to send the OTP
     */
    public $notifyTemplate;

    /** @var object
     * contains the row inserted for OTP verification in database
     */
    public $verification;

    /** @var bool
     * contains the row inserted for OTP request from API
     */
    public $apiRequest;

    /**
     * Insert a new row in database including the otp code
     * Send the otp code to user's email or mobile
     *
     * @param  object  $parent the instance of verifiable type
     * @param  string  $sendVia how the otp will send to the user
     * @param  string  $notifyTemplate which notification template will be used to send the OTP
     * @param  array  $additionalData contains if any additional data needed after verified
     * @return object
     **/
    public function newOTP(object $parent, string $sendVia, string $notifyTemplate, array $additionalData, $apiRequest = false): object
    {
        $isOtpEnable = checkIsOtpEnable();

        $this->parent = $parent;
        $this->sendVia = $sendVia;
        $this->notifyTemplate = $notifyTemplate;
        $this->additionalData = $additionalData;

        $otpVerification = new OtpVerification();
        $otpVerification->user_id = auth()->id();
        $otpVerification->send_via = $sendVia;
        $otpVerification->notify_template = $notifyTemplate;
        $otpVerification->additional_data = $additionalData;
        $otpVerification->send_at = now();

        if ($this->sendVia != '2fa' && $isOtpEnable) {
            $otpVerification->otp = verificationCode(6);
            $otpVerification->expired_at = now()->addSeconds(gs()->otp_time);
        }
        $this->parent->verifications()->save($otpVerification);
        $this->verification = $otpVerification;

        if ($apiRequest) {
            if (! $isOtpEnable) {
                return callApiMethod($additionalData['after_verified'], $otpVerification->id);
            }
            $this->sendOtp();
            $notify[] = 'OTP send successfully';

            return response()->json([
                'remark' => 'send_otp',
                'status' => 'success',
                'message' => ['success' => $notify],
                'data' => [
                    'otpId' => $otpVerification->id,
                ],
            ]);
        } else {
            session()->put('otp_id', $otpVerification->id);
            if (! $isOtpEnable) {
                return to_route($additionalData['after_verified']);
            }
            $this->sendOtp();

            return to_route('user.otp.verify');
        }
    }

    /**
     * Renew the otp code if user request for resend OTP
     *
     * Send the otp code to user's email or mobile
     *
     * @return object
     *
     * @throws ValidationException
     **/
    public function renewOTP($apiRequest = false): object
    {
        $otpTime = gs()->otp_time;
        $targetTime = $this->verification->send_at->addSeconds($otpTime);

        if ($targetTime >= now()) {

            if ($apiRequest) {
                $notify[] = 'Please Try after '.$targetTime->timestamp - time().' Seconds';

                return response()->json([
                    'remark' => 'otp_resend_time',
                    'status' => 'success',
                    'message' => ['success' => $notify],
                ]);

            } else {
                throw ValidationException::withMessages(['resend' => 'Please Try after '.$targetTime->timestamp - time().' Seconds']);
            }
        }

        $this->verification->send_at = now();
        $this->verification->expired_at = now()->addSeconds($otpTime);
        $this->verification->otp = verificationCode(6);
        $this->verification->save();
        $this->sendOtp();

        return $this->verification;
    }

    /**
     * Send the otp code to user's email or mobile
     *
     * @return void
     **/
    public function sendOtp(): void
    {
        if ($this->sendVia != '2fa') {
            $verification = $this->verification;
            $shortCodes = ['otp' => $this->verification->otp];
            notify($verification->user, $verification->notify_template, $shortCodes, [$verification->send_via], false);
        }
    }

    /**
     * Check the otp code to submitted by the user
     *
     * @return bool
     *
     * @throws ValidationException
     **/
    public function checkOTP($otp, $apiRequest = false, $validator = null): bool
    {
        $verification = $this->verification;
        if ($verification->send_via == '2fa' && (! verifyG2fa(auth()->user(), $otp))) {
            if ($apiRequest) {
                return addCustomValidation($validator, 'error', 'Invalid session data');
            } else {
                throw ValidationException::withMessages(['error' => 'Invalid session data']);
            }
        }

        if ($verification->user_id != auth()->id()) {
            if ($apiRequest) {
                return addCustomValidation($validator, 'unauthorized', 'Unauthorized action');
            } else {
                throw ValidationException::withMessages(['error' => 'Unauthorized action']);
            }
        }

        if ($verification->send_via != '2fa' && $verification->otp != $otp) {
            if ($apiRequest) {
                return addCustomValidation($validator, 'otp_invalid', 'Invalid OTP provided');
            } else {
                throw ValidationException::withMessages(['error' => 'Invalid OTP provided']);
            }
        }

        if ($verification->used_at) {
            if ($apiRequest) {
                return addCustomValidation($validator, 'error', 'This OTP has already been used');
            } else {
                throw ValidationException::withMessages(['error' => 'This OTP has already been used']);
            }
        }

        if (now() > Carbon::parse($verification->expired_at)) {
            if ($apiRequest) {
                return addCustomValidation($validator, 'error', 'This OTP has already been expired');
            } else {
                throw ValidationException::withMessages(['error' => 'This OTP has already been expired']);
            }
        }

        return true;
    }

    /**
     * Check if the verification data belongs to the authenticated user
     * Check if the verification data is for the exact verifiable type
     * Check if the user verified with the valid otp code
     *
     * @return bool
     **/
    public static function checkVerificationData($verification, $verifiableType, $apiRequest = false, $validator = null): bool
    {
        if ($verification->user_id != auth()->id()) {
            if ($apiRequest) {
                return addCustomValidation($validator, 'error', 'Unauthorized action');
            } else {
                throw ValidationException::withMessages(['error' => 'Unauthorized action']);
            }
        }

        if ($verifiableType != $verification->verifiable_type) {
            if ($apiRequest) {
                return addCustomValidation($validator, 'error', 'Invalid session data');
            } else {
                throw ValidationException::withMessages(['error' => 'Invalid session data']);
            }
        }

        if (! $verification->used_at && checkIsOtpEnable()) {
            if ($apiRequest) {
                return addCustomValidation($validator, 'error', 'The user is not verified by a valid OTP code for this action');
            } else {
                throw ValidationException::withMessages(['error' => 'The user is not verified by a valid OTP code for this action']);
            }
        }

        return true;
    }
}
