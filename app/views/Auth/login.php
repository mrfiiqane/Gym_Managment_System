<?php
require_once '../../config/init.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('views/dashboard/index.php');
    exit();
}
include '../../reusable/header.php';
include '../../reusable/loader.php';


// Build Google OAuth URL
$googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id'     => $_ENV['GOOGLE_CLIENT_ID']    ?? '',
    'redirect_uri'  => $_ENV['GOOGLE_REDIRECT_URI'] ?? '',
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'access_type'   => 'online',
    'prompt'        => 'select_account',
]);


?>

<div class="min-h-screen flex items-center justify-center p-6 bg-slate-50 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-500/10 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-400/10 rounded-full blur-3xl pointer-events-none animate-pulse" style="animation-delay: 2s;"></div>

    <div class="glass-card p-10 rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-white/60 w-full max-w-md z-10 backdrop-blur-xl bg-white/80 transition-all">
        <div class="text-center mb-10">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-2xl shadow-lg flex items-center justify-center text-white transform rotate-3 hover:rotate-0 transition-transform">
                    <span class="material-symbols-outlined text-3xl">fitness_center</span>
                </div>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-2 tracking-tight">Welcome Back</h1>
            <p class="text-slate-500 font-medium">Log in to your FitCore Portal</p>
        </div>

        <form id="loginForm" class="space-y-6">
            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Username or Email</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-3.5 text-slate-400 text-xl group-focus-within:text-blue-500 transition-colors">person</span>
                    <input type="text" id="username" name="username" placeholder="Enter username or Email"
                        class="w-full pl-12 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-700 font-medium placeholder:font-normal">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-3.5 text-slate-400 text-xl group-focus-within:text-blue-500 transition-colors">lock</span>
                    <input type="password" id="password" name="password" placeholder="••••••••"
                        class="w-full pl-12 pr-4 py-3.5 bg-white/50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-700 font-medium placeholder:font-normal">
                </div>
                <div class="flex justify-between items-center pr-1 mt-3">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" id="remember" name="remember" class="w-4 h-4 opacity-0 absolute">
                            <div class="w-5 h-5 border-2 border-slate-300 rounded-md group-hover:border-blue-500 flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined text-[14px] text-white opacity-0 transition-opacity" id="checkIcon">check</span>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-0.5 group-hover:text-slate-700 transition-colors">Remember Me</span>
                    </label>
                    <a href="forget_password.php"
                        class="text-[10px] font-bold text-blue-600 hover:text-blue-800 hover:underline hover:underline-offset-2 uppercase tracking-widest transition-colors">Forgot password?</a>
                </div>
            </div>

            <button type="submit" id="submitBtn"
                class="w-full cursor-pointer relative overflow-hidden group py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl shadow-xl shadow-slate-900/20 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    Sign In <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </span>
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="px-4 bg-white/80 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Or continue with</span>
            </div>
        </div>

        <!-- Google Sign In Button -->
        <a href="<?php echo htmlspecialchars($googleAuthUrl); ?>" id="googleLoginBtn"
            class="flex items-center justify-center gap-3 w-full py-3.5 border-2 border-slate-200 hover:border-blue-300 rounded-2xl bg-white hover:bg-blue-50/50 transition-all group shadow-sm active:scale-[0.98] cursor-pointer">
            <!-- Google SVG Logo -->
            <svg width="20" height="20" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.36-8.16 2.36-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            <span class="text-[14px] font-bold text-slate-700 group-hover:text-blue-700 transition-colors">Continue with Google</span>
        </a>

        <!-- Demo Accounts -->
        <div class="mt-6 pt-6 border-t border-slate-200/60">
            <p class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">
                <span class="material-symbols-outlined text-[13px] align-middle mr-0.5">bolt</span>
                Quick Demo Login
            </p>
            <div class="grid grid-cols-1 gap-2" id="demoAccountsGrid">
                <!-- Admin Demo -->
                <button type="button" id="demoAdminBtn"
                    onclick="fillDemo('maxamed064','123456')"
                    class="demo-btn flex items-center gap-3 w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-blue-50 hover:border-blue-300 transition-all group cursor-pointer">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center flex-shrink-0 shadow-md group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-white text-[18px]">shield_person</span>
                    </div>
                    <div class="text-left flex-1">
                        <p class="text-[12px] font-bold text-slate-700 group-hover:text-blue-700 transition-colors">Admin Account</p>
                        <p class="text-[11px] text-slate-400 font-medium">maxamed064 &bull; Full Access</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-300 group-hover:text-blue-400 group-hover:translate-x-1 transition-all text-[18px]">arrow_forward</span>
                </button>
            </div>
        </div>

        <div class="mt-5 pt-5 border-t border-slate-200/60 text-center">
            <p class="text-slate-500 text-sm font-medium">
                Don't have an account?
                <a href="singup.php" class="text-blue-600 font-bold hover:text-blue-800 transition-colors">Create one</a>
            </p>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>js/Auth/login.js"></script>

<script>
    // Custom Checkbox Logic
    document.getElementById('remember').addEventListener('change', function() {
        const box = this.nextElementSibling;
        const icon = document.getElementById('checkIcon');
        if (this.checked) {
            box.classList.remove('border-slate-300');
            box.classList.add('bg-blue-600', 'border-blue-600');
            icon.classList.remove('opacity-0');
        } else {
            box.classList.remove('bg-blue-600', 'border-blue-600');
            box.classList.add('border-slate-300');
            icon.classList.add('opacity-0');
        }
    });

    // Demo Account Quick-Fill
    function fillDemo(username, password) {
        const uField = document.getElementById('username');
        const pField = document.getElementById('password');
        const btn    = document.getElementById('demoAdminBtn');

        btn.classList.add('scale-95', 'ring-2', 'ring-blue-400');
        setTimeout(() => btn.classList.remove('scale-95', 'ring-2', 'ring-blue-400'), 250);

        uField.value = '';
        pField.value = '';
        uField.focus();

        let i = 0;
        const typeUsername = setInterval(() => {
            uField.value += username[i];
            i++;
            if (i >= username.length) {
                clearInterval(typeUsername);
                let j = 0;
                pField.focus();
                const typePassword = setInterval(() => {
                    pField.value += password[j];
                    j++;
                    if (j >= password.length) {
                        clearInterval(typePassword);
                        setTimeout(() => {
                            document.getElementById('loginForm').dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
                        }, 300);
                    }
                }, 40);
            }
        }, 50);
    }
</script>

<?php include '../../reusable/footer.php'; ?>