<?php
require_once '../../config/init.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('views/dashboard/index.php');
    exit();
}

// Build Google OAuth URL
$googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id'     => $_ENV['GOOGLE_CLIENT_ID']    ?? '',
    'redirect_uri'  => $_ENV['GOOGLE_REDIRECT_URI'] ?? '',
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'access_type'   => 'online',
    'prompt'        => 'select_account',
]);

include '../../reusable/header.php';
include '../../reusable/loader.php';
?>

<style>
    .step-slide {
        transition: all 0.4s ease;
    }
    .step-hidden {
        display: none;
        opacity: 0;
        transform: translateY(10px);
    }
    .step-visible {
        display: block;
        opacity: 1;
        transform: translateY(0);
        animation: fadeIn 0.4s ease forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .otp-input {
        letter-spacing: 0.5rem;
        font-size: 1.6rem;
        font-weight: 900;
        text-align: center;
    }
    .countdown-ring circle {
        transition: stroke-dashoffset 1s linear;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }
</style>

<div class="min-h-screen relative flex items-center justify-center bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <!-- Background Blobs Layer -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-400/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Card Container -->
    <div class="relative z-10 w-full max-w-md">
        <div class="glass-card bg-white/80 backdrop-blur-xl rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-white/60 p-8 sm:p-10 transition-all overflow-hidden">
        
        <!-- Header Icon & Title -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-2xl shadow-lg flex items-center justify-center text-white transform rotate-3 hover:rotate-0 transition-transform" id="headerIcon">
                    <span class="material-symbols-outlined text-3xl" id="headerIconSymbol">person_add</span>
                </div>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-2 tracking-tight" id="pageTitle">Create Account</h1>
            <p class="text-slate-500 font-medium" id="pageSubtitle">Join the FitCore Gym Portal</p>
        </div>

        <!-- ── STEP 1: Registration Form ──────────────────────────────── -->
        <div id="step1" class="step-slide step-visible">

        <form id="signupForm" class="space-y-5" enctype="multipart/form-data" method="post">
            <div class="flex justify-center mb-6">
                <div class="relative group cursor-pointer" onclick="document.getElementById('profileImage').click()">
                    <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg overflow-hidden bg-slate-100 flex items-center justify-center group-hover:border-blue-100 transition-colors">
                        <span id="imgPreview" class="material-symbols-outlined text-4xl text-slate-400 group-hover:text-blue-500 transition-colors">person</span>
                    </div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center shadow-lg group-hover:bg-blue-700 transition-all border-2 border-white transform group-hover:scale-110">
                        <span class="material-symbols-outlined text-white text-sm">photo_camera</span>
                        <input type="file" name="image" id="profileImage" class="hidden" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-3.5 text-slate-400 text-xl group-focus-within:text-blue-500 transition-colors">badge</span>
                    <input type="text" name="full_name" id="full_name" placeholder="Enter your full name" 
                        class="w-full pl-12 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-700 font-medium placeholder:font-normal">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Username</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-3.5 text-slate-400 text-xl group-focus-within:text-blue-500 transition-colors">person</span>
                    <input type="text" name="username" id="username" placeholder="Choose a username" 
                        class="w-full pl-12 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-700 font-medium placeholder:font-normal">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-3.5 text-slate-400 text-xl group-focus-within:text-blue-500 transition-colors">alternate_email</span>
                    <input type="email" name="email" id="email" placeholder="name@example.com" 
                        class="w-full pl-12 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-700 font-medium placeholder:font-normal">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Phone Number</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-3.5 text-slate-400 text-xl group-focus-within:text-blue-500 transition-colors">call</span>
                    <input type="text" name="phone" id="phone" placeholder="+1234567890" 
                        class="w-full pl-12 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-700 font-medium placeholder:font-normal">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-3.5 text-slate-400 text-xl group-focus-within:text-blue-500 transition-colors">lock</span>
                    <input type="password" name="password" id="password" placeholder="Create a password" 
                        class="w-full pl-12 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-700 font-medium placeholder:font-normal">
                </div>
            </div>
            
            <!-- role_id=2 (User) is always assigned on signup. Admin accounts are created by the system only. -->
            <input type="hidden" name="role_id" value="2">

            <!-- <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">User Role</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-3.5 text-slate-400 text-xl group-focus-within:text-blue-500 transition-colors">manage_accounts</span>
                    <select name="role_id" id="role_id" 
                        class="w-full pl-12 pr-10 py-3.5 bg-white/50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-700 font-medium appearance-none">
                        <option value="" disabled selected>Select a role</option>
                        <option value="2">Trainer</option>
                        <option value="3">Member</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-4 top-3.5 text-slate-400 pointer-events-none group-focus-within:text-blue-500 transition-colors">expand_more</span>
                </div>
            </div> -->
            
            <button type="submit"
                class="w-full mt-4 cursor-pointer relative overflow-hidden group py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl shadow-xl shadow-slate-900/20 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    Sign Up <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </span>
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="px-4 bg-white/80 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Or sign up with</span>
            </div>
        </div>

        <!-- Google Sign Up Button -->
        <a href="<?php echo htmlspecialchars($googleAuthUrl); ?>" id="googleSignupBtn"
            class="flex items-center justify-center gap-3 w-full py-3.5 border-2 border-slate-200 hover:border-blue-300 rounded-2xl bg-white hover:bg-blue-50/50 transition-all group shadow-sm active:scale-[0.98] cursor-pointer mb-6">
            <svg width="20" height="20" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.36-8.16 2.36-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            <span class="text-[14px] font-bold text-slate-700 group-hover:text-blue-700 transition-colors">Continue with Google</span>
        </a>
        </div> <!-- end step 1 -->

        <div id="step2" class="step-slide step-hidden">
            <div class="space-y-6">
                <!-- Info Box -->
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl px-4 py-3">
                    <span class="material-symbols-outlined text-emerald-500 text-[20px] flex-shrink-0">mark_email_read</span>
                    <div>
                        <p class="text-[12px] font-bold text-emerald-700">Verify Email</p>
                        <p class="text-[11px] text-emerald-600" id="emailSentTo">Check your inbox for 6-digit code.</p>
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

                <button type="button" id="btnVerifyOtp"
                    class="w-full cursor-pointer relative overflow-hidden group py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl shadow-xl shadow-slate-900/20 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        Verify & Complete <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">verified</span>
                    </span>
                </button>

                <!-- Resend -->
                <p class="text-center text-[12px] text-slate-400">
                    Didn't receive it?
                    <button type="button" id="btnResend" class="text-blue-600 font-bold hover:underline ml-1 disabled:opacity-40 disabled:cursor-not-allowed" disabled>Resend Code</button>
                </p>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-200/60 text-center mt-6">
            <p class="text-slate-500 text-sm font-medium">
                Already have an account?
                <a href="login.php" class="text-blue-600 font-bold hover:text-blue-800 transition-colors">Sign in</a>
            </p>
        </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>js/Auth/singup.js"></script>

<script>
    $("#profileImage").on("change", function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                // Ensure it covers the circle nicely
                const imgHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-full">`;
                const container = $("#imgPreview").parent();
                container.empty().append(imgHTML);
            }
            reader.readAsDataURL(file);
        }
    });
</script>

<?php include '../../reusable/footer.php'; ?>