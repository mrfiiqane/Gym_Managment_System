$(document).ready(function () {
  if (localStorage.getItem("remember_username")) {
    $("#username").val(localStorage.getItem("remember_username"));
    $("#password").val(localStorage.getItem("remember_password"));
    $("#remember").prop("checked", true);

    // Trigger change event to update custom UI checkmark visually
    $("#remember").trigger("change");
  }
});

$("#loginForm").on("submit", function (e) {
  e.preventDefault();

  let username = $("#username").val();
  let password = $("#password").val();
  let remember = $("#remember").is(":checked");

  if (remember) {
    localStorage.setItem("remember_username", username);
    localStorage.setItem("remember_password", password);
  } else {
    localStorage.removeItem("remember_username");
    localStorage.removeItem("remember_password");
  }

  let sendingData = {
    action: "user_login",
    username: username,
    password: password,
    remember: remember,
  };

  // AJAX.post() in helper.js already shows an Error Toast centrally for failed responses.
  // Only pass a Success callback here — do NOT add an Error callback to avoid duplicate Toasts.
  AJAX.post(
    "../../api/Auth/login.php",
    sendingData,
    function (res) {
      // This callback is only reached when response.status === true
      Toast.show(true, res.message || "Login Successful!");

      let redirectUrl = "../../views/dashboard/admin/index.php"; // default
      $("#loginForm")[0].reset();
      if (res.data && res.data.redirect) {
        redirectUrl = res.data.redirect;
      }

      setTimeout(() => {
        window.location.href = redirectUrl;
      }, 2000);
    },
    // } else {
    //     Toast.show("error", res.message || "Invalid credentials");
    //   }
    // },
    // function(errorMsg) {
    //   Toast.show("error", errorMsg);
    // }
    // No Error callback needed — helper.js aya haya errorka  centrally with Toast.show(false, ...)
  );
});
