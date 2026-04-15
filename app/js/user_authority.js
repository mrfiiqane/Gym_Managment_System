$(document).ready(function () {
  loadData();
  fill_Users();
  // fillCategories();
});

$("#user_id").on("change", function () {
  let value = $(this).val();
  load_Users_Permissions(value);
});

// 1: select All  soo qabo box all_authority marka isbadalo on change shaqadan qabo
$("#all_authority").on("change", function () {
  // cheack ma la saaray, haa prob bertigiisa true ka dhig ama false
  if ($(this).is(":checked")) {
    $("input[type='checkbox']").prop("checked", true);
  } else {
    $("input[type='checkbox']").prop("checked", false);
  }
});

// 2: CATEGORY Marka la doorto admin,subscriber iwm
$("#authorityArea").on(
  "change",
  "input[name='category_authority[]']",
  function () {
    let value = $(this).val();

    if ($(this).is(":checked")) {
      $(`#authorityArea input[type='checkbox'][category='${value}']`).prop(
        "checked",
        true,
      );
    } else {
      $(`#authorityArea input[type='checkbox'][category='${value}']`).prop(
        "checked",
        false,
      );
    }
  },
);

// 3: MAIN LINK Marka la doorto read, update,delete iwm
$("#authorityArea").on("change", "input[name='system_link[]']", function () {
  let value = $(this).val();

  if ($(this).is(":checked")) {
    $(`#authorityArea input[type='checkbox'][link_id='${value}']`).prop(
      "checked",
      true,
    );
  } else {
    $(`#authorityArea input[type='checkbox'][link_id='${value}']`).prop(
      "checked",
      false,
    );
  }
});

function fill_Users() {
  let sendingData = {
    action: "Read_All",
  };

  $.ajax({
    method: "POST",
    dataType: "JSON",
    url: "../api/users.php",
    data: sendingData,

    //loader bilaab (1 = Spinner, 2 = Dots)
    beforeSend: function () {
      showLoader(2, 5000);

    },

    success: function (data) {
      let status = data.status;
      let response = data.data;
      let html = "";

      $("#user_id").empty(); // reset si duplicate u imaan

      if (status) {
        html += `<option value="0">Select User</option>`;
        response.forEach((res) => {
          html += `<option value="${res.id}">${res.username}</option>`;
        });
        $("#user_id").append(html);
      } else {
        displayMessage("error", response);
      }
    },

    error: function (data) {
      displayMessage("error", data.responseText);
    },

    // 👉 HALKAN loader jooji (mar walba, success ama error)
    complete: function () {
      hideLoader();
    },
  });
}

// soo akhri PermissionStatus userska id userka baas
function load_Users_Permissions(id) {
  let sendingData = {
    action: "get_user_authorities",
    user_id: id,
  };

  $.ajax({
    method: "POST",
    dataType: "JSON",
    url: "../api/user_authorities.php",
    data: sendingData,
    beforeSend: function () {
      showLoader(1, 5000);

    },
    success: function (data) {
      let status = data.status;
      let response = data.data;
      let html = "";
      let tr = "";

      // marka hore reset samee
      $("#authorityArea input[type='checkbox']").prop("checked", false);

      if (status) {
        // 1 waa haddi data hysto 
        if (response.length >= 1) {
          response.forEach((users) => {
            $(
              `input[type='checkbox'][name='category_authority[]'][value='${users["category"]}']`,
            ).prop("checked", true);

            $(
              `input[type='checkbox'][name='system_link[]'][value='${users["link_id"]}']`,
            ).prop("checked", true);

            $(
              `input[type='checkbox'][name='system_action[]'][value='${users["action_id"]}']`,
            ).prop("checked", true);
          });
        } else {
          $("input[type='checkbox']").prop("checked", false);
        }
      } else {
        displayMessage("error", response);
      }
    },
    error: function (data) {
      displayMessage("error", data.responseText);
    },
    complete: function () {
      hideLoader();
    },
  });
}

$("#userForm").on("submit", function (event) {
  event.preventDefault();
  // sleep(2);
 
  // action la siiyey iyo user_id la doortay
  let action_id = [];
  let selectedAuthority = []; // Halkan ku kaydi magacyada (Dashboard, iwm)
  let user_id = $("#user_id").val();

  if (user_id == 0) {
    displayMessage("error", "Please Select User");
    return; //jooji
  }

  $("input[name='system_action[]']:checked").each(function () {
    // ku shub action link system_link vale
    action_id.push($(this).val());
    // Waxaan soo qaadaneynaa text-ka ku dhex jira label-ka checkbox-kaas agtiisa ah
    selectedAuthority.push($(this).closest("label").text().trim());
  });

  // let form_data = new FormData($("#userForm")[0]);

  // if(btnAction == "Insert"){
  //     form_data.append("action", "authorize_user");
  // }else{
  //     form_data.append("action", "updated_category");
  // }

  sendingData = {
    user_id: user_id,
    action_id: action_id,
    action: "authorize_user",
  };

  // ajex waa asyc js loo isticmaalo in request u diro api,databaseka
  $.ajax({
    method: "POST",
    dataType: "JSON",
    url: "../api/user_authorities.php",
    data: sendingData,

    // kaliya isticmaala marka formData la joogo
    // // processData: false,
    // // contentType: false,

    success: function (data) {
      let status = data.status;
      let response = data.data;

      if (status) {
        // Halkan waxaan ku dhiseynaa fariin qurux badan
        let successHTML =
          "<strong>Successfully! The user has been granted authorization for the following:</strong>";
        successHTML += "<ul class='list-disc ms-5 mt-2'>";

        selectedAuthority.forEach((res) => {
          successHTML += `<li>${res}</li>`;
        });

        successHTML += "</ul>";

        $("#success").removeClass("hidden").html(successHTML);

        setTimeout(() => {
          $("#success").addClass("hidden");
        }, 10000);
        // response.forEach((re) => {
        //   $("#success").removeClass("hidden");
        //   $("#error").addClass("hidden");
        //   $("#success").html(re["data"]);
        // });
      } else {
        displayMessage("error", "Khalad ayaa dhacay markii la kaydinayay.");
      }
      // let error = "<ul>";
      // $("#error").removeClass("hidden");
      // $("#success").addClass("hidden");
      // response.forEach((re) => {
      //   error += `<li>Registrtion user is ${re["data"]}</li>`;
      // });

      // error += `</ul>`;
      // $("#error").html(error);
    },

    error: function (data) {
      displayMessage(
        "error",
        "Xiriirka API-ga waa uu go'ay.",
        data.responseText,
      );
    },
  });
});

function loadData() {
  let sendingData = {
    action: "read_all",
  };

  $.ajax({
    method: "POST",
    url: "../api/user_authorities.php",
    dataType: "JSON",
    data: sendingData,
    beforeSend: function () {
      showLoader(2, 5000);

    },

    success: function (data) {
      let status = data.status;
      let response = data.data;

      if (!status) {
        displayMessage("error", response);
        return;
      }

      let html = "";
      let role = "";
      let system_link = "";
      let system_action = "";

      response.forEach((res) => {
        // Category (Role)
        if (res["category"] !== role) {
          if (role !== "") {
            html += `</div></fieldset>`;
          }

          html += `
            <fieldset class="border border-slate-200 dark:border-white/10 rounded-2xl p-4 mb-4 bg-slate-50/50 dark:bg-white/5">
              <legend class="font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2 px-2 pb-1">
                <input
                  type="checkbox"
                  class="w-4 h-4 text-primary rounded bg-white dark:bg-darkPanel border-slate-300 focus:ring-primary/50"
                  name="category_authority[]"
                  value="${res["category"]}"
                >
                ${res["category"]}
              </legend>

              <div class="ml-4 text-sm text-slate-500 flex flex-col gap-3">
          `;

          role = res["category"];
        }

        // Main Link
        if (res["name"] !== system_link) {
          html += `
            <div class="flex items-center px-3 py-2 bg-white dark:bg-darkPanel border border-slate-100 dark:border-white/5 rounded-xl shadow-sm">
              <label class="flex items-center gap-3 cursor-pointer text-slate-600 dark:text-slate-400 font-medium">
                <input
                  type="checkbox"
                  class="w-4 h-4 text-primary rounded"
                  name="system_link[]"
                  category="${res["category"]}"
                  value="${res["link_id"]}"
                  category_id="${res["category_id"]}"
                  link_id="${res["link_id"]}"
                >
                ${res["name"]}
              </label>
            </div>
          `;
          system_link = res["name"];
        }

        // Actions
        if (res["action_name"] !== system_action) {
          html += `
            <div class="ml-8 my-1 flex flex-col gap-1 text-[13px]">
              <label class="flex items-center gap-3 cursor-pointer text-slate-500 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                <input
                  type="checkbox"
                  name="system_action[]"
                  class="w-4 h-4 text-primary rounded"
                  category="${res["category"]}"
                  value="${res["action_id"]}"
                  category_id="${res["category_id"]}"
                  link_id="${res["link_id"]}"
                  action_id="${res["action_id"]}"
                >
                ${res["action_name"]}
              </label>
            </div>
          `;
          system_action = res["action_name"];
        }
      });

      html += `</div></fieldset>`;

      $("#authorityArea").append(html);
    },

    error: function (err) {
      console.log("Please Try Again:", err.responseText);
    },
    complete: function () {
      hideLoader();
    },
  });

}
