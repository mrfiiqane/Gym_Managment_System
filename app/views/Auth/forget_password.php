<?php
require_once '../../config/init.php';

if (is_logged_in()) {
    redirect('views/dashboard/index.php');
    exit();
}

include '../../reusable/header.php';
include '../../reusable/loader.php';
?>

<style>
    .step-slide {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .step-hidden {
        opacity: 0;
        transform: translateX(30px);
        pointer-events: none;
        position: absolute;
        width: 100%;
    }
    .step-visible {
        opacity: 1;
        transform: translateX(0);
        position: relative;
    }
    .otp-input {
        letter-spacing: 0.5rem;
        font-size: 1.6rem;
        font-weight: 900;
        text-align: center;
    }
    /* Countdown ring */
    .countdown-ring circle {
        transition: stroke-dashoffset 1s linear;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }
</style>

<div class="min-h-screen flex items-center justify-center p-6 bg-slate-50 relative overflow-hidden py-12 z-0">
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-500/10 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-400/10 rounded-full blur-3xl pointer-events-none animate-pulse" style="animation-delay: 2s;"></div>

    <div class="glass-card w-full max-w-[460px] mx-auto rounded-[2.5rem] shadow-2xl shadow-blue-900/5 p-10 border border-white/60 z-10 backdrop-blur-xl bg-white/80 transition-all">

        <!-- ── Step Indicator ─────────────────────────────────────────── -->
        <div class="flex items-center justify-center gap-2 mb-8" id="stepIndicator">
            <div class="step-dot w-8 h-8 rounded-full bg-blue-600 text-white text-xs font-black flex items-center justify-center shadow-lg shadow-blue-500/30 transition-all" id="dot1">1</div>
            <div class="h-px w-8 bg-slate-200 transition-all" id="line1"></div>
            <div class="step-dot w-8 h-8 rounded-full bg-slate-200 text-slate-500 text-xs font-black flex items-center justify-center transition-all" id="dot2">2</div>
            <div class="h-px w-8 bg-slate-200 transition-all" id="line2"></div>
            <div class="step-dot w-8 h-8 rounded-full bg-slate-200 text-slate-500 text-xs font-black flex items-center justify-center transition-all" id="dot3">3</div>
        </div>

        <!-- Header Icon & Title -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-2xl shadow-lg flex items-center justify-center text-white transform rotate-3 hover:rotate-0 transition-transform" id="headerIcon">
                    <span class="material-symbols-outlined text-3xl" id="headerIconSymbol">key</span>
                </div>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-2 tracking-tight" id="pageTitle">Forgot Password?</h1>
            <p class="text-slate-500 font-medium" id="pageSubtitle">Enter your email and we'll send a code.</p>
        </div>

        <!-- ── STEP 1: Email ──────────────────────────────────────────── -->
        <div id="step1" class="step-slide step-visible">
            <form id="step1Form" class="space-y-5">
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-3.5 text-slate-400 text-xl group-focus-within:text-blue-500 transition-colors">alternate_email</span>
                        <input type="email" name="email" id="emailInput" placeholder="name@example.com" required
                            class="w-full pl-12 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-700 font-medium placeholder:font-normal">
                    </div>
                </div>
                <button type="submit" id="btnStep1"
                    class="w-full mt-2 cursor-pointer relative overflow-hidden group py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl shadow-xl shadow-slate-900/20 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        Send Code <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">send</span>
                    </span>
                </button>
            </form>
        </div>

        <!-- ── STEP 2: OTP ───────────────────────────────────────────── -->
        <div id="step2" class="step-slide step-hidden">
            <div class="space-y-5">
                <!-- Email sent info -->
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl px-4 py-3">
                    <span class="material-symbols-outlined text-emerald-500 text-[20px] flex-shrink-0">mark_email_read</span>
                    <div>
                        <p class="text-[12px] font-bold text-emerald-700">Code Sent!</p>
                        <p class="text-[11px] text-emerald-600" id="emailSentTo">Check your inbox.</p>
                    </div>
                </div>

                <!-- Countdown Timer -->
                <div class="flex flex-col items-center gap-2 py-2">
                    <div class="relative w-16 h-16">
                        <svg class="countdown-ring w-16 h-16" viewBox="0 0 64 64">
                            <circle cx="32" cy="32" r="28" fill="none" stroke="#e2e8f0" stroke-width="4"/>
                            <circle id="countdownCircle" cx="32" cy="32" r="28" fill="none" stroke="#3b82f6" stroke-width="4"
                                stroke-dasharray="175.9" stroke-dashoffset="0"/>
                        </svg>
                        <span id="countdownText" class="absolute inset-0 flex items-center justify-center text-[15px] font-black text-blue-600">5:00</span>
                    </div>
                    <p class="text-[11px] font-semibold text-slate-400">Code expires in <span id="countdownLabel" class="text-blue-500">5:00</span></p>
                </div>

                <!-- OTP Input -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Enter 6-Digit Code</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-3.5 text-slate-400 text-xl group-focus-within:text-blue-500 transition-colors">pin</span>
                        <input type="text" id="otpInput" maxlength="6" placeholder="000000"
                            class="otp-input w-full pl-12 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-700 placeholder:font-normal placeholder:text-2xl placeholder:tracking-widest">
                    </div>
                </div>

                <button type="button" id="btnStep2"
                    class="w-full cursor-pointer relative overflow-hidden group py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl shadow-xl shadow-slate-900/20 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        Verify Code <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">verified</span>
                    </span>
                </button>

                <!-- Resend -->
                <p class="text-center text-[12px] text-slate-400">
                    Didn't receive it?
                    <button type="button" id="btnResend" class="text-blue-600 font-bold hover:underline ml-1 disabled:opacity-40 disabled:cursor-not-allowed" disabled>Resend Code</button>
                </p>
            </div>
        </div>

        <!-- ── STEP 3: New Password ──────────────────────────────────── -->
        <div id="step3" class="step-slide step-hidden">
            <div class="space-y-5">
                <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-2xl px-4 py-3">
                    <span class="material-symbols-outlined text-blue-500 text-[20px] flex-shrink-0">shield_check</span>
                    <p class="text-[12px] font-bold text-blue-700">Identity verified! Set your new password.</p>
                </div>

                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">New Password</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-3.5 text-slate-400 text-xl group-focus-within:text-blue-500 transition-colors">lock</span>
                        <input type="password" id="newPassword" placeholder="Min 6 characters"
                            class="w-full pl-12 pr-12 py-3.5 bg-white/50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-700 font-medium">
                        <button type="button" id="togglePwd" class="absolute right-4 top-3.5 text-slate-400 hover:text-blue-500 transition-colors">
                            <span class="material-symbols-outlined text-xl" id="eyeIcon">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Password Strength -->
                <div id="pwdStrengthBar" class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                    <div id="pwdStrengthFill" class="h-full rounded-full transition-all duration-500 w-0"></div>
                </div>
                <p id="pwdStrengthText" class="text-[11px] font-semibold text-slate-400 -mt-3 ml-1"></p>

                <button type="button" id="btnStep3"
                    class="w-full cursor-pointer relative overflow-hidden group py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-2xl shadow-xl shadow-blue-500/25 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        Reset Password <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">check_circle</span>
                    </span>
                </button>
            </div>
        </div>

        <!-- Back to Login -->
        <div class="mt-8 pt-6 border-t border-slate-200/60 text-center">
            <p class="text-slate-500 text-sm font-medium">
                Remembered it?
                <a href="login.php" class="text-blue-600 font-bold hover:text-blue-800 transition-colors">Back to Login</a>
            </p>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>js/Auth/forget_password.js"></script>

<?php include '../../reusable/footer.php'; ?>
