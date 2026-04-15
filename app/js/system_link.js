let btnAction = "Insert";
const api_url = "../api/system_links.php";
let currentPage = 1;
let rowLimit = 8;
  fillLinks();
  fillCategories();
$(document).ready(function () {
  loadData(currentPage);


  // Raadinta (Search)
  $("#linksSearch").on("input", function () {
    currentPage = 1;
    loadData(currentPage);
  });

  // Bogga xiga (Next)
  $("#nextPage").on("click", function () {
    currentPage++;
    loadData(currentPage);
  });

  // Bogga hore (Prev)
  $("#prevPage").on("click", function () {
    if (currentPage > 1) {
      currentPage--;
      loadData(currentPage);
    }
  });
});

//  1. SUBMIT FORM (REGISTER & UPDATE)
$("#links_form").on("submit", function (e) {
  e.preventDefault();
  
  let form_data = new FormData(this);
  // Hubi in magacyadan ay la mid yihiin kuwa PHP-ga ku jira
  let action = btnAction === "Insert" ? "register_links" : "update_links";
  form_data.append("action", action);

  $.ajax({
    method: "POST",
    dataType: "JSON",
    url: api_url,
    data: form_data,
    processData: false,
    contentType: false,
    beforeSend: function () {
      showLoader(2);
    },
    success: function (data) {
      if (data.status) {
        displayMessage("success", data.data);
        // closeModal(); // Hubi in function-kan uu jiro
        loadData(currentPage);
        btnAction = "Insert";
        // $("#links_form")[0].reset();
      } else {
        displayMessage("error", data.data);
      }
    },
    error: function (data) {
      // $("#error .msg-content").text(data.data);
      displayMessage("error", data.responseText);
    },

    complete: function () {
      hideLoader();
    },
  });
});

function loadData(page) {
  let offset = (page - 1) * rowLimit;
  let searchValue = $("#linksSearch").val() || "";

  let sendingData = {
    action: "read_all",
    p_limit: rowLimit,
    p_offset: offset,
    p_search: searchValue,
  };

  $.ajax({
    method: "POST",
    dataType: "JSON",
    url: api_url,
    data: sendingData,
    // ... (qaybtii hore ee ajax)
    beforeSend: function () {
      showLoader(2);
    },
    success: function (data) {
      let status = data.status;
      let response = data.data;

      $("#links_table thead").empty();
      $("#links_table tbody").empty();

      if (status && response.length > 0) {
        // 1. DHISIDDA DYNAMIC HEADER
        let headers = Object.keys(response[0]);
        let th = "<tr class='bg-slate-50 dark:bg-white/5'>";
        headers.forEach((header) => {
          if (header !== "TotalCount") {
            // Ha muujin ID-yada
            let cleanHeader = header.replace("_", " ").toUpperCase();
            th += `<th class="px-8 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">${cleanHeader}</th>`;
          }
        });
        th +=
          "<th class='px-8 py-4 text-right uppercase text-[11px] font-bold text-slate-400'>Actions</th></tr>";
        $("#links_table thead").append(th);

        // 2. DHISIDDA ROWS
        response.forEach((res) => {
          let tr = `<tr class="text-sm border-b dark:border-white/5 hover:bg-slate-50/50 transition-all">`;
          headers.forEach((header) => {
            if (header !== "TotalCount") {
              tr += `<td class="px-8 py-4 text-slate-600 dark:text-slate-300">${res[header]}</td>`;
            }
          });

          tr += `
                <td class="px-8 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <button onclick="FetchUser(${res.id})" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </button>
                        <button onclick="confirmDelete(${res.id})" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                </td></tr>`;
          $("#links_table tbody").append(tr);
        });

        // 3. CUSBOONAYSIINTA COUNTERS-KA
        let totalRecords = parseInt(response[0].TotalCount);
        $("#totalCount").text(totalRecords); // Wadarta guud ee sare
        updatePaginationUI(page, totalRecords);
      } else {
        $("#links_table tbody").append(
          '<tr><td colspan="100%" class="p-10 text-center text-slate-400">No records found matching your search.</td></tr>',
        );
        $("#totalCount").text(0);
        updatePaginationUI(page, 0);
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

// system links and category
function fillLinks() {
  let sendingData = {
    action: "read_all_system_links",
  };

  $.ajax({
    method: "POST",
    dataType: "JSON",
    url: api_url,
    data: sendingData,
    beforeSend: function () {
      showLoader(2);
    },
    success: function (data) {
      let status = data.status;
      let response = data.data;
      let html = "";
      let tr = "";

      if (status) {
        response.forEach((res) => {
          html += `<option value="${res}">${res}</option>`;
        });

        $("#link_id").append(html);
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

// soo akhri categories ka file ka category.js kaso akhri
function fillCategories() {
  let sendingData = {
    action: "read_all",
  };

  $.ajax({
    method: "POST",
    dataType: "JSON",
    url: "../api/category.php",
    data: sendingData,
    beforeSend: function () {
      showLoader(2);
    },
    success: function (data) {
      let status = data.status;
      let response = data.data;
      let html = "";
      let tr = "";

      if (status) {
        response.forEach((res) => {
          // user ku wuxu arkaa name , database ka id ayuu ku keydiyaa
          html += `<option value="${res["id"]}">${res["name"]}</option>`;
        });
        $("#category").append(html);
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

function FetchUser(id) {
  let sendingData = {
    action: "read_info",
    id: id,
  };
  $.ajax({
    method: "POST",
    dataType: "JSON",
    url: api_url,
    data: sendingData,
    beforeSend: function () {
      showLoader(2);
    },
    success: function (data) {
      let status = data.status;
      let response = data.data;
      if (status && response) {
        // let response = data.data[0];
        btnAction = "Update";

        // Hubi in ID-yadan ay la mid yihiin kuwa Input-kaaga iyo DB
        $("#update_id").val(response["id"]);
        $("#name").val(response["name"]);
        $("#link_id").val(response["link"]);
        $("#category").val(response["category_id"]);

        $("#modalTitle").text("Edit links Information");
        $("#btnSave").text("Update links");
        $("#modal").removeClass("hidden").addClass("flex");
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

//  4. DELETE links
function confirmDelete(id) {
  Swal.fire({
    title: "Ma hubtaa?",
    text: "Xogtan dib looma soo celin karo!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    confirmButtonText: "Haa, tirtir!",
    cancelButtonText: "Iska daa",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: api_url,
        method: "POST",
        dataType: "json",
        data: { action: "delete_links", id: id },
        beforeSend: function () {
          showLoader(2);
        },
        success: function (res) {
          if (res.status) {
            displayMessage("success", res.data);
            loadData(currentPage);
          } else {
            displayMessage("error", res.data);
          }
        },
        complete: function () {
          hideLoader();
        }
      });
    }
  });
}

function updatePaginationUI(page, total) {
  let endEntry = Math.min(page * rowLimit, total);
  let startEntry = total === 0 ? 0 : (page - 1) * rowLimit + 1;
  $("#paginationInfo").text(
    `Showing ${startEntry} to ${endEntry} of ${total} entries`,
  );

  $("#prevPage")
    .prop("disabled", page === 1)
    .css("opacity", page === 1 ? "0.3" : "1");
  $("#nextPage")
    .prop("disabled", endEntry >= total)
    .css("opacity", endEntry >= total ? "0.3" : "1");
}
