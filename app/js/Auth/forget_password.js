$(document).ready(function () {

    // ── State ──────────────────────────────────────────────────────────────────
    let userEmail  = '';
    let userId     = '';
    let countdown  = null;
    const TOTAL_SEC = 300;

    // ── Step Navigation ────────────────────────────────────────────────────────
    function goToStep(step) {
        // Hide all steps
        ['step1', 'step2', 'step3'].forEach((id, idx) => {
            const el = document.getElementById(id);
            el.classList.remove('step-visible');
            el.classList.add('step-hidden');

            // Update dots
            const dot = document.getElementById('dot' + (idx + 1));
            if (idx + 1 < step) {
                // completed
                dot.classList.remove('bg-slate-200', 'text-slate-500', 'bg-blue-600', 'text-white');
                dot.classList.add('bg-emerald-500', 'text-white');
                dot.innerHTML = '<span class="material-symbols-outlined text-[14px]">check</span>';
            } else if (idx + 1 === step) {
                // active
                dot.classList.remove('bg-slate-200', 'text-slate-500', 'bg-emerald-500');
                dot.classList.add('bg-blue-600', 'text-white');
                dot.textContent = step;
            } else {
                // upcoming
                dot.classList.remove('bg-blue-600', 'text-white', 'bg-emerald-500');
                dot.classList.add('bg-slate-200', 'text-slate-500');
                dot.textContent = idx + 1;
            }

            // Update lines
            if (idx < 2) {
                const line = document.getElementById('line' + (idx + 1));
                if (idx + 1 < step) {
                    line.classList.remove('bg-slate-200');
                    line.classList.add('bg-emerald-400');
                } else {
                    line.classList.remove('bg-emerald-400');
                    line.classList.add('bg-slate-200');
                }
            }
        });

        // Show current step
        const current = document.getElementById('step' + step);
        setTimeout(() => {
            current.classList.remove('step-hidden');
            current.classList.add('step-visible');
        }, 50);

        // Update header content
        const titles    = ['Forgot Password?',     'Check Your Email',           'New Password'];
        const subtitles = ['Enter your email and we\'ll send a code.', 'Enter the 6-digit code we sent.', 'Choose a strong new password.'];
        const icons     = ['key',                  'mark_email_read',            'lock_reset'];

        document.getElementById('pageTitle').textContent    = titles[step - 1];
        document.getElementById('pageSubtitle').textContent = subtitles[step - 1];
        document.getElementById('headerIconSymbol').textContent = icons[step - 1];
    }

    // ── Countdown Timer ────────────────────────────────────────────────────────
    function startCountdown() {
        let remaining = TOTAL_SEC;
        const circle  = document.getElementById('countdownCircle');
        const textEl  = document.getElementById('countdownText');
        const labelEl = document.getElementById('countdownLabel');
        const circumference = 175.9;

        clearInterval(countdown);
        $('#btnResend').prop('disabled', true);

        countdown = setInterval(() => {
            remaining--;
            const mins = Math.floor(remaining / 60);
            const secs = remaining % 60;
            const display = mins + ':' + String(secs).padStart(2, '0');

            textEl.textContent  = display;
            labelEl.textContent = display;

            // Update ring
            const offset = circumference * (1 - remaining / TOTAL_SEC);
            circle.style.strokeDashoffset = offset;

            // Color shifts as time runs out
            if (remaining <= 30) {
                circle.setAttribute('stroke', '#ef4444');
                textEl.classList.replace('text-blue-600', 'text-red-500');
            }

            if (remaining <= 0) {
                clearInterval(countdown);
                textEl.textContent = 'Expired';
                $('#btnResend').prop('disabled', false);
            }
        }, 1000);
    }

    // ── Password Strength ──────────────────────────────────────────────────────
    $('#newPassword').on('input', function () {
        const val  = $(this).val();
        const fill = $('#pwdStrengthFill');
        const text = $('#pwdStrengthText');
        let score  = 0;

        if (val.length >= 6)  score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { w: '20%',  color: 'bg-red-500',    label: 'Too weak' },
            { w: '40%',  color: 'bg-orange-400',  label: 'Weak' },
            { w: '60%',  color: 'bg-yellow-400',  label: 'Fair' },
            { w: '80%',  color: 'bg-blue-500',    label: 'Strong' },
            { w: '100%', color: 'bg-emerald-500', label: 'Very strong' },
        ];

        if (val.length === 0) {
            fill.css('width', '0').attr('class', 'h-full rounded-full transition-all duration-500 w-0');
            text.text('');
            return;
        }

        const lvl = levels[Math.min(score - 1, 4)];
        fill.css('width', lvl.w).attr('class', 'h-full rounded-full transition-all duration-500 ' + lvl.color);
        text.text(lvl.label).css('color', '');
    });

    // ── Toggle Password Visibility ─────────────────────────────────────────────
    $('#togglePwd').on('click', function () {
        const input = document.getElementById('newPassword');
        const eye   = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            eye.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            eye.textContent = 'visibility';
        }
    });

    // ── STEP 1: Send OTP ───────────────────────────────────────────────────────
    $('#step1Form').on('submit', function (e) {
        e.preventDefault();
        userEmail = $('#emailInput').val().trim();

        if (!userEmail) {
            Toast.show(false, 'Please enter your email address.');
            return;
        }

        AJAX.post(
            '../../api/Auth/forget_password.php',
            { action: 'send_otp', email: userEmail },
            function (res) {
                Toast.show(true, res.message || 'Code sent!');
                $('#emailSentTo').text('Code sent to: ' + userEmail);
                goToStep(2);
                startCountdown();
            }
        );
    });

    // ── STEP 2: Verify OTP ─────────────────────────────────────────────────────
    $('#btnStep2').on('click', function () {
        const otp = $('#otpInput').val().trim();

        if (otp.length !== 6 || !/^\d{6}$/.test(otp)) {
            Toast.show(false, 'Please enter the full 6-digit code.');
            return;
        }

        AJAX.post(
            '../../api/Auth/forget_password.php',
            { action: 'verify_otp', email: userEmail, otp: otp },
            function (res) {
                userId = res.data.id;
                clearInterval(countdown);
                Toast.show(true, 'Code verified! Set your new password.');
                goToStep(3);
            }
        );
    });

    // ── Resend Code ────────────────────────────────────────────────────────────
    $('#btnResend').on('click', function () {
        AJAX.post(
            '../../api/Auth/forget_password.php',
            { action: 'send_otp', email: userEmail },
            function (res) {
                Toast.show(true, 'New code sent!');
                $('#otpInput').val('');
                // Reset countdown ring color
                document.getElementById('countdownCircle').setAttribute('stroke', '#3b82f6');
                document.getElementById('countdownText').classList.replace('text-red-500', 'text-blue-600');
                startCountdown();
            }
        );
    });

    // ── OTP: Numbers only ──────────────────────────────────────────────────────
    $('#otpInput').on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });

    // ── STEP 3: Reset Password ─────────────────────────────────────────────────
    $('#btnStep3').on('click', function () {
        const password = $('#newPassword').val();

        if (password.length < 6) {
            Toast.show(false, 'Password must be at least 6 characters.');
            return;
        }
        if (password.length > 20) {
            Toast.show(false, 'Password must be less than 20 characters.');
            return;
        }

        AJAX.post(
            '../../api/Auth/forget_password.php',
            { action: 'reset_password', id: userId, password: password },
            function (res) {
                Toast.show(true, (res.message || 'Password reset!') + ' Redirecting...');
                setTimeout(() => { window.location.href = 'login.php'; }, 2500);
            }
        );
    });

    // Init step 1
    goToStep(1);
});
