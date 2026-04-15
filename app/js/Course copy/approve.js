const api_url = "../../api/Courses/approve.php";
let currentStatus = "all";

$(document).ready(function () {
  if ($("#EnrollmentsTable").length) {
    loadPendingEnrollments();
  }

  // Search Interaction
  let searchTimer;
  $("#searchEnrollment").on("input", function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function () {
      loadPendingEnrollments();
    }, 500);
  });

  // Filter Button Click
  $(".filter-btn").on("click", function() {
    $(".filter-btn").removeClass("active bg-blue-600 text-white shadow-lg shadow-blue-600/20").addClass("text-slate-400 hover:bg-slate-100");
    $(this).addClass("active bg-blue-600 text-white shadow-lg shadow-blue-600/20").removeClass("text-slate-400 hover:bg-slate-100");
    
    currentStatus = $(this).data("status");
    loadPendingEnrollments();
  });
});

function loadPendingEnrollments() {
    let search = $("#searchEnrollment").val() || "";
    
    $.ajax({
        method: "POST",
        dataType: "JSON",
        url: api_url,
        data: {
            action: "get_available_courses",
            search: search,
            status: currentStatus
        },
        success: function (response) {
            if (response.status) {
                let tr = "";
                let enrollments = response.data.enrollments;

                // Update Stats
                if (response.data.stats) {
                    const stats = response.data.stats;
                    $("#totalEnrollments").text(stats.total || 0);
                    $("#pendingEnrollments").text(stats.pending || 0);
                    $("#approvedEnrollments").text(stats.approved || 0);
                    $("#blockedEnrollments").text((stats.blocked || 0));
                }

                if (enrollments.length > 0) {
                    enrollments.forEach((item) => {
                        let status = item.status || "Pending";
                        let statusClass = "bg-orange-50 text-orange-500 border-orange-100";
                        if (status === 'Approved') statusClass = "bg-green-50 text-green-600 border-green-100";
                        if (status === 'Blocked') statusClass = "bg-slate-100 text-slate-600 border-slate-200";

                        tr += `
                        <tr class="hover:bg-slate-50/50 transition-all">
                            <td class="px-8 py-5">
                                <span class="font-bold text-slate-800">${item.student_name || 'N/A'}</span>
                            </td>
                            <td class="px-8 py-5 text-slate-600">${item.course_title || 'N/A'}</td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase border ${statusClass}">
                                    ${status}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-slate-400 text-xs font-bold">
                                ${(item.enrolled_at || '').split(' ')[0] || 'N/A'}
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <button onclick="updateStatus(${item.enrollment_id}, 'Approved')" title="Approve"
                                        class="p-2.5 bg-green-50 text-green-600 rounded-xl hover:bg-green-600 hover:text-white transition-all cursor-pointer">
                                        <span class="material-symbols-outlined text-sm">check_circle</span>
                                    </button>
                                
                                    <button onclick="updateStatus(${item.enrollment_id}, 'Blocked')" title="Block"
                                        class="p-2.5 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-800 hover:text-white transition-all cursor-pointer">
                                        <span class="material-symbols-outlined text-sm">block</span>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                    });
                } else {
                    tr = '<tr><td colspan="5" class="text-center py-20 text-slate-400 font-bold italic">Ma jiraan codsiyo la helay!</td></tr>';
                }
                $("#EnrollmentsTable tbody").html(tr);
            } else {
                Swal.fire("Cillad!", response.message, "error");
            }
        }
    });
}


function updateStatus(id, status) {
  let actionText = "";
  let confirmColor = "";

  if (status === "Approved") {
    actionText = "aqbasho";
    confirmColor = "#10B981";
  } else if (status === "Blocked") {
    actionText = "xirto (block)";
    confirmColor = "#64748b";
  }else {
    actionText = "Pending";
    confirmColor = "#f59e0b";
  }

  Swal.fire({
    title: "Ma hubtaa?",
    text: `Ma rabaa inaad ${actionText} ardaygan koorsada?`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: confirmColor,
    cancelButtonColor: "#6B7280",
    confirmButtonText: `Haa, ${status}!`,
    cancelButtonText: "Jooji",
    customClass: {
        confirmButton: 'rounded-xl px-6 py-3 text-xs font-black uppercase tracking-widest',
        cancelButton: 'rounded-xl px-6 py-3 text-xs font-black uppercase tracking-widest'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        method: "POST",
        dataType: "JSON",
        url: api_url,
        data: {
            action: "update_enrollment_status",
            id: id,
            status: status,
        },
        success: function (response) {
            if (response.status) {
                Swal.fire({
                    title: "Waa lagu guuleystay!",
                    text: response.message,
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                });
                loadPendingEnrollments();
            } else {
                Swal.fire("Cillad!", response.message, "error");
            }
        }
      });
    }
  });
}
