let btnAction = "Insert";
const api_url = "../api/category.php";
let currentPage = 1;
let rowLimit = 8;

$(document).ready(function () {
  loadData(currentPage);

  $("#categorySearch").on("input", function () {
    currentPage = 1;
    loadData(currentPage);
  });

  $("#nextPage").on("click", function () {
    currentPage++;
    loadData(currentPage);
  });

  $("#prevPage").on("click", function () {
    if (currentPage > 1) {
      currentPage--;
      loadData(currentPage);
    }
  });
});

/* =========================================
   1. SUBMIT FORM (REGISTER & UPDATE)
========================================= */
$("#category_form").on("submit", function (e) {
  e.preventDefault();

  let form_data = new FormData(this);
  let action = btnAction === "Insert" ? "register_category" : "update_category";
  form_data.append("action", action);

  AJAX.post(api_url, form_data, function (res) {
    if (typeof closeModal === 'function') closeModal();
    Toast.show(true, res.message);
    loadData(currentPage);
    btnAction = "Insert";
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

  AJAX.post(api_url, sendingData, function (res) {
    let response = res.data;

    $("#category_table thead").empty();
    $("#category_table tbody").empty();

    if (response && response.length > 0) {
      let th = "";
      let tr = "";

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

      response.forEach((item) => {
        tr += `<tr class="text-sm border-b dark:border-white/5 hover:bg-slate-50/50 transition-all">`;

        headers.forEach((header) => {
          if (header !== "TotalCount" && header !== "user_id") {
            if (header.toLowerCase() === "icon") {
              tr += `<td class="px-8 py-4 text-sm text-slate-600 dark:text-slate-300 border-b">
                      <span class="material-symbols-outlined text-primary">${item[header]}</span>
                    </td>`;
            } else {
              tr += `<td class="px-8 py-4 text-sm text-slate-600 dark:text-slate-300 border-b">${item[header]}</td>`;
            }
          }
        });

        tr += `
          <td class="px-8 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button type="button" onclick="FetchUser(${item.id})" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                                <button type="button" onclick="confirmDelete(${item.id})" class="p-2 text-red-600 hover:bg-red-100 rounded-lg">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </td></tr>`;
      });
      $("#category_table tbody").append(tr);

      let totalRecords = parseInt(response[0].TotalCount) || 0;
      $("#totalCount").text(totalRecords);
      updatePaginationUI(page, totalRecords);
    } else {
      $("#totalCount").text(0);
      updatePaginationUI(page, 0);
    }
  });
}

function FetchUser(id) {
  let sendingData = {
    action: "read_info",
    id: id,
  };  
  
  AJAX.post(api_url, sendingData, function (res) {
    let response = res.data;
    if (response) {
      btnAction = "Update";

      $("#update_id").val(response['id']);
      $("#name").val(response['name']);
      $("#icon").val(response['icon']);
      $("#role").val(response['role']);

      $("#modalTitle").text("Edit category Information");
      $("#btnSave").text("Update category");
      $("#modal").removeClass("hidden").addClass("flex");
    }
  });
}

/* =========================================
   4. DELETE category
========================================= */
function confirmDelete(id) {
  ConfirmBox.danger({
    title: "Ma hubtaa in aad tirtirto?",
    text: "Xogtan dib looma soo celin karo!",
    confirmText: "Haa, tirtir",
    cancelText: "Iska daa",
    onConfirm: function () {
      AJAX.post(api_url, { action: "delete_category", id: id }, function (res) {
        Toast.show(true, res.message);
        loadData(currentPage);
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
