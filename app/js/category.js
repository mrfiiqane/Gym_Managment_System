let btnAction = "Insert";
const api_url = "../api/category.php";
let currentPage = 1;
let rowLimit = 8;

$(document).ready(function () {
  loadData(currentPage);

  // Raadinta (Search)
  $("#categorySearch").on("input", function () {
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

/**
 * 0. Farriimaha (Toast Message)
 */
// function displayMessage(type, message) {
//   const Toast = Swal.mixin({
//     toast: true,
//     position: "top-end",
//     showConfirmButton: false,
//     timer: 3000,
//     timerProgressBar: true,
//   });
//   Toast.fire({
//     icon: type,
//     title: message,
//   });
// }

/* =========================================
   1. SUBMIT FORM (REGISTER & UPDATE)
========================================= */
$("#category_form").on("submit", function (e) {
  e.preventDefault();

  // let form_data = new FormData($("#category_form")[0]);  
  
  // if(btnAction == "Insert"){
  //     form_data.append("action", "register_categorys");
  // }else{
  //     form_data.append("action", "update_categorys");
  // }  

  let form_data = new FormData(this);
  // Hubi in magacyadan ay la mid yihiin kuwa PHP-ga ku jira
  let action = btnAction === "Insert" ? "register_category" : "update_category";
  form_data.append("action", action);

  $.ajax({
    method: "POST",
    dataType: "JSON",
    url: api_url,
    data: form_data,
    processData: false,
    contentType: false,
    success: function (data) {
      if (data.status) {
        displayMessage("success", data.data);
        // closeModal(); // Hubi in function-kan uu jiro
        loadData(currentPage);
        btnAction = "Insert";
        // $("#category_form")[0].reset();
      } else {
        displayMessage("error", data.data);
      }
    },
    error: function (data) {
      // $("#error .msg-content").text(data.data);
      displayMessage("error", data.responseText);
    },
  });
});

function loadData(page) {
  let offset = (page - 1) * rowLimit;
  let searchValue = $("#categorySearch").val() || "";

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
    success: function (data) {
      let status = data.status;
      let response = data.data;

      if (status) {
        $("#category_table thead").empty();
        $("#category_table tbody").empty();

        if (response.length > 0) {
          let th = "";
          let tr = "";

          // 1. DHISIDDA DYNAMIC HEADER (Kaliya hal mar ayuu dhacayaa)
          let headers = Object.keys(response[0]);
          th = "<tr class='bg-sky-100 text-zinc-500 my-4 text-md font-semibold px-8 py-4 text-right'>";

          headers.forEach((header) => {
            if (header !== "TotalCount" && header !== "user_id") {
              let cleanHeader = header.replace("_", " ").toUpperCase();
              th += `<th class="px-8 py-4 text-left">${cleanHeader}</th>`;
            }
          });
          th += "<th class='px-8 py-4 text-center'>Actions</th></tr>";
          $("#category_table thead").append(th);

          // 2. DHISIDDA DYNAMIC ROWS (Loop-ka xogta)
          response.forEach((res) => {
            tr = `<tr class="text-sm border-b dark:border-white/5 hover:bg-slate-50/50 transition-all">`;

            headers.forEach((header) => {
              if (header !== "TotalCount" && header !== "user_id") {
                if (header.toLowerCase() === "icon") {
                  tr += `<td class="px-8 py-4 text-sm text-slate-600 dark:text-slate-300 border-b">
                          <span class="material-symbols-outlined text-primary">${res[header]}</span>
                        </td>`;
                } else {
                  tr += `<td class="px-8 py-4 text-sm text-slate-600 dark:text-slate-300 border-b">${res[header]}</td>`;
                }
              }
            });

            // Badhamada Update iyo Delete oo wata SVG-gaagii
            tr += `
              <td class="px-8 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button type="button" onclick="FetchUser(${res.id})" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg">
                                        <span class="material-symbols-outlined">edit</span>
                                    </button>
                                    <button type="button" onclick="confirmDelete(${res.id})" class="p-2 text-red-600 hover:bg-red-100 rounded-lg">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </td>`;
            tr += "</tr>";
            $("#category_table tbody").append(tr);
          });

          // Pagination Logic
          let totalRecords = parseInt(response[0].TotalCount) || 0;
          $("#totalCount").text(totalRecords);
          updatePaginationUI(page, totalRecords);
        }
      } else {
        displayMessage("error", response);
      }
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
    success: function (data) {
      let status = data.status;
      let response = data.data;
      if (status && response) {
        // let response = data.data[0];
        btnAction = "Update";

        // Hubi in ID-yadan ay la mid yihiin kuwa Input-kaaga iyo DB
        $("#update_id").val(response['id']);
        $("#name").val(response['name']);
        $("#icon").val(response['icon']);
        $("#role").val(response['role']);

        $("#modalTitle").text("Edit category Information");
        $("#btnSave").text("Update category");
        $("#modal").removeClass("hidden").addClass("flex");
      }
    },
    error: function (data) {
      displayMessage("error", data.responseText);
    },
  });
}

/* =========================================
   4. DELETE category
========================================= */
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
      $.post(
        api_url,
        { action: "delete_category", id: id },
        function (res) {
          if (res.status) {
            displayMessage("success", res.data);
            loadData(currentPage);
          } else {
            displayMessage("error", res.data);
          }
        },
        "json",
      );
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

