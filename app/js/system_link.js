let btnAction = "Insert";
let SYSTEM_LINK_API_URL = "../../api/system_links.php";
let currentPage = 1;
let rowLimit = 8;

$(document).ready(function () {
  loadData(currentPage);
  fillLinks();
  fillCategories();

  $("#linksSearch").on("input", function () {
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

//  1. SUBMIT FORM (REGISTER & UPDATE)
$("#links_form").on("submit", function (e) {
  e.preventDefault();
  
  let form_data = new FormData(this);
  let action = btnAction === "Insert" ? "register_links" : "update_links";
  form_data.append("action", action);

  AJAX.post(SYSTEM_LINK_API_URL, form_data, function (res) {
    if (typeof closeModal === 'function') closeModal();
    Toast.show(true, res.message);
    loadData(currentPage);
    btnAction = "Insert";
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

  AJAX.post(SYSTEM_LINK_API_URL, sendingData, function (res) {
    let response = res.data;

    $("#links_table thead").empty();
    $("#links_table tbody").empty();

    if (response && response.length > 0) {
      let headers = Object.keys(response[0]);
      let th = "<tr class='bg-slate-50 dark:bg-white/5'>";
      headers.forEach((header) => {
        if (header !== "TotalCount") {
          let cleanHeader = header.replace("_", " ").toUpperCase();
          th += `<th class="px-8 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">${cleanHeader}</th>`;
        }
      });
      th += "<th class='px-8 py-4 text-right uppercase text-[11px] font-bold text-slate-400'>Actions</th></tr>";
      $("#links_table thead").append(th);

      response.forEach((item) => {
        let tr = `<tr class="text-sm border-b dark:border-white/5 hover:bg-slate-50/50 transition-all">`;
        headers.forEach((header) => {
          if (header !== "TotalCount") {
            tr += `<td class="px-8 py-4 text-slate-600 dark:text-slate-300">${item[header]}</td>`;
          }
        });

        tr += `
              <td class="px-8 py-4 text-right">
                  <div class="flex justify-end gap-2">
                      <button onclick="FetchUser(${item.id})" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors">
                          <span class="material-symbols-outlined text-lg">edit</span>
                      </button>
                      <button onclick="confirmDelete(${item.id})" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors">
                          <span class="material-symbols-outlined text-lg">delete</span>
                      </button>
                  </div>
              </td></tr>`;
        $("#links_table tbody").append(tr);
      });

      let totalRecords = parseInt(response[0].TotalCount) || 0;
      $("#totalCount").text(totalRecords);
      updatePaginationUI(page, totalRecords);
    } else {
      $("#links_table tbody").append(
        '<tr><td colspan="100%" class="p-10 text-center text-slate-400">No records found matching your search.</td></tr>',
      );
      $("#totalCount").text(0);
      updatePaginationUI(page, 0);
    }
  });
}

// system links and category
function fillLinks() {
  AJAX.post(SYSTEM_LINK_API_URL, { action: "read_all_system_links" }, function (res) {
    let response = res.data;
    let html = "";
    if (response) {
      response.forEach((item) => {
        html += `<option value="${item}">${item}</option>`;
      });
      $("#link_id").append(html);
    }
  });
}

function fillCategories() {
  AJAX.post("../../api/category.php", { action: "read_all" }, function (res) {
    let response = res.data;
    let html = "";
    if (response) {
      response.forEach((item) => {
        html += `<option value="${item.id}">${item.name}</option>`;
      });
      $("#category").append(html);
    }
  });
}

function FetchUser(id) {
  AJAX.post(SYSTEM_LINK_API_URL, { action: "read_info", id: id }, function (res) {
    let response = res.data;
    if (response) {
      btnAction = "Update";

      $("#update_id").val(response["id"]);
      $("#name").val(response["name"]);
      $("#link_id").val(response["link"]);
      $("#category").val(response["category_id"]);

      $("#modalTitle").text("Edit links Information");
      $("#btnSave").text("Update links");
      $("#modal").removeClass("hidden").addClass("flex");
    }
  });
}

//  4. DELETE links
function confirmDelete(id) {
  ConfirmBox.danger({
    title: "Ma hubtaa in aad tirtirto?",
    text: "Xogtan dib looma soo celin karo!",
    confirmText: "Haa, tirtir",
    cancelText: "Iska daa",
    onConfirm: function () {
      AJAX.post(SYSTEM_LINK_API_URL, { action: "delete_links", id: id }, function (res) {
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
