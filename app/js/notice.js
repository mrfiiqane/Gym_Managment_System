let btnAction = "create_notice";
let currentPage = 0;
let totalRecords = 0;
const itemsPerPage = 10;

$(document).ready(function () {
  loadNotices();

  // Search functionality
  $("#noticeSearch").on("keyup", function () {
    currentPage = 0;
    loadNotices();
  });

  // Pagination
  $("#prevPage").on("click", function () {
    if (currentPage > 0) {
      currentPage--;
      loadNotices();
    }
  });

  $("#nextPage").on("click", function () {
    if ((currentPage + 1) * itemsPerPage < totalRecords) {
      currentPage++;
      loadNotices();
    }
  });

  // Form submission
  $("#notice_form").on("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append("action", btnAction);

    $.ajax({
      url: "../api/notices.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function () {
        showLoader(2);
      },
      success: function (response) {
        if (response.status) {
          Swal.fire({
            icon: "success",
            title: "Success",
            text: response.data,
            confirmButtonColor: "#89986D",
          });
          closeModal();
          loadNotices();
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.data,
            confirmButtonColor: "#89986D",
          });
        }
      },
      },
      complete: function () {
        hideLoader();
      },
    });
  });
});

function loadNotices() {
  const search = $("#noticeSearch").val();

  $.ajax({
    url: "../api/notices.php",
    type: "POST",
    data: {
      action: "read_all",
      p_search: search,
      p_limit: itemsPerPage,
      p_offset: currentPage * itemsPerPage,
    },
    beforeSend: function () {
      showLoader(2);
    },
    success: function (response) {
      if (response.status && response.data.length > 0) {
        totalRecords = response.data[0].TotalCount || 0;
        displayNotices(response.data);
        updatePagination();
      } else {
        $("#notice_table tbody").html(`
                    <tr><td colspan="6" class="text-center py-12 text-slate-400">
                        <span class="material-symbols-outlined text-6xl opacity-20">notifications_off</span>
                        <p class="mt-4 font-bold">No notices found</p>
                    </td></tr>
                `);
        $("#notice_table thead").html("");
        totalRecords = 0;
        updatePagination();
      }
    },
    },
    complete: function () {
      hideLoader();
    },
  });
}

function displayNotices(data) {
  let thead = `
        <tr class="text-xs font-black text-slate-400 uppercase tracking-widest">
            <th class="p-6">Type</th>
            <th class="p-6">Title</th>
            <th class="p-6">Message</th>
            <th class="p-6">Posted By</th>
            <th class="p-6">Date</th>
            <th class="p-6">Actions</th>
        </tr>
    `;

  let tbody = "";
  data.forEach((notice) => {
    const typeColors = {
      Holiday: "bg-red-100 text-red-600 dark:bg-red-500/10",
      Announcement: "bg-blue-100 text-blue-600 dark:bg-blue-500/10",
      Update: "bg-green-100 text-green-600 dark:bg-green-500/10",
      Event: "bg-purple-100 text-purple-600 dark:bg-purple-500/10",
    };

    tbody += `
            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-all">
                <td class="p-6">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase ${typeColors[notice.type] || "bg-slate-100 text-slate-600"}">${notice.type}</span>
                </td>
                <td class="p-6">
                    <p class="font-bold text-slate-800 dark:text-white">${notice.title}</p>
                </td>
                <td class="p-6">
                    <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2">${notice.message}</p>
                </td>
                <td class="p-6">
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-300">${notice.posted_by_name}</p>
                </td>
                <td class="p-6">
                    <p class="text-xs text-slate-400">${new Date(notice.created_at).toLocaleDateString()}</p>
                </td>
                <td class="p-6">
                    <div class="flex items-center gap-2">
                        <button onclick='editNotice(${JSON.stringify(notice)})' class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-primary/10 text-primary transition-all">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </button>
                        <button onclick="deleteNotice(${notice.id})" class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-red-50 text-red-500 transition-all">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
        `;
  });

  $("#notice_table thead").html(thead);
  $("#notice_table tbody").html(tbody);
}

function editNotice(notice) {
  btnAction = "update_notice";
  $("#update_id").val(notice.id);
  $("#title").val(notice.title);
  $("#message").val(notice.message);
  $("#type").val(notice.type);
  $("#expires_at").val(notice.expires_at);
  $("#modalTitle").text("Edit Notice");
  $("#btnSave").text("Update Notice");
  $("#modal").removeClass("hidden").addClass("flex");
}

function deleteNotice(id) {
  Swal.fire({
    title: "Are you sure?",
    text: "This notice will be permanently deleted!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#89986D",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../api/notices.php",
        type: "POST",
        data: {
          action: "delete_notice",
          id: id,
        },
        beforeSend: function () {
          showLoader(2);
        },
        success: function (response) {
          if (response.status) {
            Swal.fire({
              icon: "success",
              title: "Deleted!",
              text: response.data,
              confirmButtonColor: "#89986D",
            });
            loadNotices();
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.data,
              confirmButtonColor: "#89986D",
            });
          }
        },
        },
        complete: function () {
          hideLoader();
        },
      });
    }
  });
}

function updatePagination() {
  const start = currentPage * itemsPerPage + 1;
  const end = Math.min((currentPage + 1) * itemsPerPage, totalRecords);

  $("#paginationInfo").text(
    `Showing ${start} - ${end} of ${totalRecords} notices`,
  );

  $("#prevPage").prop("disabled", currentPage === 0);
  $("#nextPage").prop("disabled", (currentPage + 1) * itemsPerPage >= totalRecords,);
}
