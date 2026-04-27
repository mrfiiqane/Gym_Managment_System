let api_url = "../api/user_authorities.php";

$(document).ready(function () {
  loadData();
  fill_Users();
});

$("#user_id").on("change", function () {
  let value = $(this).val();
  load_Users_Permissions(value);
});

$("#all_authority").on("change", function () {
  if ($(this).is(":checked")) {
    $("input[type='checkbox']").prop("checked", true);
  } else {
    $("input[type='checkbox']").prop("checked", false);
  }
});

$("#authorityArea").on("change", "input[name='category_authority[]']", function () {
  let value = $(this).val();
  if ($(this).is(":checked")) {
    $(`#authorityArea input[type='checkbox'][category='${value}']`).prop("checked", true);
  } else {
    $(`#authorityArea input[type='checkbox'][category='${value}']`).prop("checked", false);
  }
});

$("#authorityArea").on("change", "input[name='system_link[]']", function () {
  let value = $(this).val();
  if ($(this).is(":checked")) {
    $(`#authorityArea input[type='checkbox'][link_id='${value}']`).prop("checked", true);
  } else {
    $(`#authorityArea input[type='checkbox'][link_id='${value}']`).prop("checked", false);
  }
});

function fill_Users() {
  AJAX.post("../api/users/users.php", { action: "Read_All" }, function (res) {
    let response = res.data;
    let html = "";
    $("#user_id").empty();
    if (response) {
      html += `<option value="0">Select User</option>`;
      response.forEach((item) => {
        html += `<option value="${item.id}">${item.username}</option>`;
      });
      $("#user_id").append(html);
    }
  });
}

function load_Users_Permissions(id) {
  AJAX.post(api_url, { action: "get_user_authorities", user_id: id }, function (res) {
    let response = res.data;
    $("#authorityArea input[type='checkbox']").prop("checked", false);
    if (response && response.length >= 1) {
      response.forEach((users) => {
        $(`input[type='checkbox'][name='category_authority[]'][value='${users.category}']`).prop("checked", true);
        $(`input[type='checkbox'][name='system_link[]'][value='${users.link_id}']`).prop("checked", true);
        $(`input[type='checkbox'][name='system_action[]'][value='${users.action_id}']`).prop("checked", true);
      });
    }
  });
}

$("#userForm").on("submit", function (event) {
  event.preventDefault();
  let action_id = [];
  let selectedAuthority = [];
  let user_id = $("#user_id").val();

  if (user_id == 0) {
    Toast.show(false, "Please Select User");
    return; 
  }

  $("input[name='system_action[]']:checked").each(function () {
    action_id.push($(this).val());
    selectedAuthority.push($(this).closest("label").text().trim());
  });

  let sendingData = {
    user_id: user_id,
    action_id: action_id,
    action: "authorize_user",
  };

  AJAX.post(api_url, sendingData, function (res) {
    let successHTML = "<strong>Successfully! The user has been granted authorization for the following:</strong>";
    successHTML += "<ul class='list-disc ms-5 mt-2'>";
    selectedAuthority.forEach((item) => {
      successHTML += `<li>${item}</li>`;
    });
    successHTML += "</ul>";

    $("#success").removeClass("hidden").html(successHTML);
    setTimeout(() => {
      $("#success").addClass("hidden");
    }, 10000);
    
    Toast.show(true, res.message);
  });
});

function loadData() {
  AJAX.post(api_url, { action: "read_all" }, function (res) {
    let response = res.data;
    if (!response) return;

    let html = "";
    let role = "";
    let system_link = "";
    let system_action = "";

    response.forEach((item) => {
      if (item.category !== role) {
        if (role !== "") html += `</div></fieldset>`;
        html += `
          <fieldset class="border border-slate-200 rounded-2xl p-4 mb-4 bg-panel-soft/50">
            <legend class="font-bold text-slate-700 mb-2 flex items-center gap-2 px-2 pb-1">
              <input type="checkbox" class="w-4 h-4 text-primary rounded bg-panel border-slate-300 focus:ring-primary/50" name="category_authority[]" value="${item.category}">
              ${item.category}
            </legend>
            <div class="ml-4 text-sm text-text-soft flex flex-col gap-3">`;
        role = item.category;
      }

      if (item.name !== system_link) {
        html += `
          <div class="flex items-center px-3 py-2 bg-panel border border-slate-100 rounded-xl shadow-sm">
            <label class="flex items-center gap-3 cursor-pointer text-slate-600 font-medium">
              <input type="checkbox" class="w-4 h-4 text-primary rounded" name="system_link[]" category="${item.category}" value="${item.link_id}" category_id="${item.category_id}" link_id="${item.link_id}">
              ${item.name}
            </label>
          </div>`;
        system_link = item.name;
      }

      if (item.action_name !== system_action) {
        html += `
          <div class="ml-8 my-1 flex flex-col gap-1 text-[13px]">
            <label class="flex items-center gap-3 cursor-pointer text-text-soft hover:text-slate-700:text-slate-300 transition-colors">
              <input type="checkbox" name="system_action[]" class="w-4 h-4 text-primary rounded" category="${item.category}" value="${item.action_id}" category_id="${item.category_id}" link_id="${item.link_id}" action_id="${item.action_id}">
              ${item.action_name}
            </label>
          </div>`;
        system_action = item.action_name;
      }
    });

    html += `</div></fieldset>`;
    $("#authorityArea").append(html);
  });
}
