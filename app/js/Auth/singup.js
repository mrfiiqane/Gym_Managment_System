$(document).ready(function () {
  // --- State ---
  let userId = "";
  let userEmail = "";
  let countdown = null;
  const TOTAL_SEC = 300; // 5 minutes

  // --- Step Navigation ---
  function goToStep(step) {
    if (step === 1) {
      $("#step1").removeClass("step-hidden").addClass("step-visible");
      $("#step2").removeClass("step-visible").addClass("step-hidden");
      $("#pageTitle").text("Create Account");
      $("#pageSubtitle").text("Join the FitCore Gym Portal");
      $("#headerIconSymbol").text("person_add");
    } else if (step === 2) {
      $("#step1").removeClass("step-visible").addClass("step-hidden");
      $("#step2").removeClass("step-hidden").addClass("step-visible");
      $("#pageTitle").text("Verify Email");
      $("#pageSubtitle").text("Enter the code we sent you.");
      $("#headerIconSymbol").text("mark_email_read");
    }
  }

  // --- Countdown Timer ---
  function startCountdown() {
    let remaining = TOTAL_SEC;
    const circle = document.getElementById("countdownCircle");
    const textEl = document.getElementById("countdownText");
    const labelEl = document.getElementById("countdownLabel");
    const circumference = 175.9;

    clearInterval(countdown);
    $("#btnResend").prop("disabled", true);

    countdown = setInterval(() => {
      remaining--;
      const mins = Math.floor(remaining / 60);
      const secs = remaining % 60;
      const display = mins + ":" + String(secs).padStart(2, "0");

      textEl.textContent = display;
      labelEl.textContent = display;

      // Update ring
      const offset = circumference * (1 - remaining / TOTAL_SEC);
      if (circle) circle.style.strokeDashoffset = offset;

      if (remaining <= 60) {
        if (circle) circle.setAttribute("stroke", "#ef4444");
        textEl.classList.replace("text-blue-600", "text-red-500");
      }

      if (remaining <= 0) {
        clearInterval(countdown);
        textEl.textContent = "0:00";
        $("#btnResend").prop("disabled", false);
      }
    }, 1000);
  }

  // --- STEP 1: Registration ---
  $("#signupForm").on("submit", function (e) {
    e.preventDefault();

    let form_data = new FormData(this);
    form_data.append("action", "register_user");

    AJAX.post("../../api/Auth/singup.php", form_data, function (res) {
      // res.status is true here
      userId = res.data.user_id;
      userEmail = res.data.email;

      Toast.show(true, res.message || "Registration successful!");
      $("#emailSentTo").html(
        `Check your inbox: <strong class='text-emerald-700'>${userEmail}</strong>`
      );

      goToStep(2);
      startCountdown();
    });
  });

  // --- STEP 2: OTP Verification ---
  $("#btnVerifyOtp").on("click", function () {
    const otp = $("#otpInput").val().trim();

    if (otp.length !== 6 || !/^\d{6}$/.test(otp)) {
      Toast.show(false, "Please enter the 6-digit verification code.");
      return;
    }

    AJAX.post(
      "../../api/Auth/singup.php",
      {
        action: "verify_otp",
        user_id: userId,
        otp: otp,
      },
      function (res) {
        clearInterval(countdown);
        Toast.show(true, res.message || "Account verified!");
        setTimeout(() => {
          window.location.href = "login.php";
        }, 2500);
      }
    );
  });

  // --- Resend Code ---
  $("#btnResend").on("click", function () {
    AJAX.post(
      "../../api/Auth/singup.php",
      {
        action: "resend_otp",
        user_id: userId,
      },
      function (res) {
        Toast.show(true, "New code sent!");
        $("#otpInput").val("");
        // Reset countdown ring color
        const circle = document.getElementById("countdownCircle");
        if (circle) circle.setAttribute("stroke", "#3b82f6");
        $("#countdownText").removeClass("text-red-500").addClass("text-blue-600");
        startCountdown();
      }
    );
  });

  // --- OTP: Numbers only ---
  $("#otpInput").on("input", function () {
    this.value = this.value.replace(/\D/g, "").slice(0, 6);
  });
});
