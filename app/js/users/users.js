let btnAction = "Insert";
const api_url = "../../api/users/users.php";
let currentPage = 1;
let currentFilter = "all";
const defaultImage = "../../uploads/User_profile/default.png";
// const USER_UPLOAD_URL = "../../uploads/User_profile/";

$(document).ready(function () {
  loadUsers();
  // Filter Buttons
  $(".filter-btn").on("click", function () {
    $(".filter-btn")
      .removeClass("active bg-primary/10 text-primary bg-blue-600 text-white") // Midabada hore ka saar
      .addClass("text-slate-400");
    $(this)
      .addClass("active bg-blue-600 text-white") // Midabka active ku dar
      .removeClass("text-slate-400");

    currentFilter = $(this).data("status");
    currentPage = 1;
    // console.log("Clicked Filter:", currentFilter);
    loadUsers();
  });

  // Search
  $("#pass_search").on("keyup", function () {
    currentPage = 1;
    loadUsers();
  });

  // Pagination Click
  $("#nextBtn").click(function () {
    if (!$(this).prop("disabled")) {
      currentPage++;
      loadUsers();
    }
  });

  $("#prevBtn").click(function () {
    if (!$(this).prop("disabled")) {
      currentPage--;
      loadUsers();
    }
  });
});

// $(document).ready(function () {
//   loadUsers();
//   // Filter Buttons
//   $(".filter-btn").on("click", function () {
//     $(".filter-btn")
//       .removeClass("active bg-primary/10 text-primary")
//       .addClass("text-slate-400");
//     $(this)
//       .addClass("active bg-primary/10 text-primary")
//       .removeClass("text-slate-400");

//     currentFilter = $(this).data("status");
//     currentPage = 1;
//     loadUsers();
//   });

//   // Search
//   $("#pass_search").on("keyup", function () {
//     currentPage = 1;
//     loadUsers();
//   });

//   // Pagination Click
//   $("#nextBtn").click(function () {
//     if (!$(this).prop("disabled")) {
//       currentPage++;
//       loadUsers();
//     }
//   });

//   $("#prevBtn").click(function () {
//     if (!$(this).prop("disabled")) {
//       currentPage--;
//       loadUsers();
//     }
//   });
// });

function showModel() {
  $("#userModal").removeClass("hidden").addClass("flex");
}

function hideModal() {
  $("#userModal").addClass("hidden").removeClass("flex");
}

function sendResponse(type, message) {
  Swal.fire({
    icon: type,
    title: message,
    showConfirmButton: true,
  });
}

// so jido sawirka iyo box ka    show ga
let fileImage = document.querySelector("#image");
let showInput = document.querySelector("#show");

fileImage.addEventListener("change", (e) => {
  const reader = new FileReader();
  reader.onload = (event) => {
    showInput.src = event.target.result;
  };
  reader.readAsDataURL(e.target.files[0]);
});

// Submit Form (Add/Edit)
$("#userForm").submit(function (e) {
  e.preventDefault();

  let form_data = new FormData(this);
  // Hubi in magacyadan ay la mid yihiin kuwa PHP-ga ku jira
  let action = btnAction === "Insert" ? "register_user" : "update_user";
  form_data.append("action", action);

  $.ajax({
    method: "POST",
    dataType: "JSON",
    url: api_url,
    data: form_data,
    processData: false,
    contentType: false,

    beforeSend: function () {
      showLoader(1, 5000);
    },

    success: function (data) {
      hideLoader(); // Jooji loading-ka marka jawaabta la helo
      let status = data.status;
      let response = data.data;

      if (status) {
        // Kaliya haddii EDIT la sameeyay
        if (btnAction === "Update") {
          let username = response.username;
          let id = response.id;
          let email = response.email;
          let role = response.role_id;

          Swal.fire({
            icon: "success",
            title: "La cusboonaysiiyey ✏️",
            html: `
          <div style="text-align:center; line-height:1.6">
            <p><b>User-kan waa la cusboonaysiiyey si guul leh</b></p>
            <div style="
              background:#eff6ff;
              border:2px solid #93c5fd;
              border-radius:15px;
              padding:10px;
            ">
              <p><b>Username:</b> ${username}</p>
              <p><b>User ID:</b> ${id}</p>
              <p><b>Email:</b> ${email}</p>
            </div>
          </div>
        `,
            confirmButtonColor: "#89986D",
          });
        }

        if (data.new_image_url) {
          $("#show").attr("src", data.new_image_url);
        } else if (btnAction === "Insert") {
          // Kaliya marka INSERT la sameeyo reset garee sawirka
          $("#show").attr("src", defaultImage);
        }

        // Reset caadi ah
        btnAction = "Insert";
        hideModal();
        $("#userForm")[0].reset();

        loadUsers();
      } else {
        Swal.fire("Khalad", data.message, "error");
      }
    },
    complete: function () {
      hideLoader();
    },
  });
});

// Load Users Function
function loadUsers() {
  let search = $("#pass_search").val();
  let role = "all";
  let status = "";

  if (currentFilter === "3" || currentFilter === "2") {
    role = currentFilter;
  } else if (currentFilter === "pending") {
    status = "pending";
  }
  let sendingData = {
    action: "read_all_users",
    search: search,
    role_id: role,
    status: status,
    page: currentPage,
  };

  // console.log("Sending:", sendingData);

  $.ajax({
    method: "POST",
    dataType: "JSON",
    url: api_url,
    data: sendingData,
    // processData: false,
    // contentType: false,

    beforeSend: function () {
      showLoader(1, 5000);
    },

    success: function (data) {
      if (!data) {
        console.error("Server-ka xog ma soo celin!");
        hideLoader(); // Jooji loading-ka xitaa haddii cilad jirto
        return;
      }

      let status = data.status;
      let response = data.data;

      if (status) {
        let th = "";
        let tr = "";
        let users = data.data.users || [];
        let stats = response.stats || {};

        // Update Stats
        $("#total_users_count").text(stats.total);
        $("#student_count").text(stats.students);
        $("#teacher_count").text(stats.teachers);
        $("#pending_count").text(stats.pending);
        $("#showing_count").text(users.length);

        // 1. Qeex Keys-ka aad rabto inaad ka qariso miiska (Exclude List)
        const excludedKeys = [
          // // "id",
          // "role_id",
          "image",
          "email",
          // // "password",
          // // "TotalCount",
          // // "status",
          // // "created_at",
        ];

        if (users.length > 0) {
          // --- DHISIDDA DYNAMIC HEADER ---
          let headerKeys = Object.keys(users[0]);
          th =
            "<tr class='bg-sky-100 text-zinc-500 my-4 text-md font-semibold px-8 py-4'>";

          headerKeys.forEach((key) => {
            if (!excludedKeys.includes(key)) {
              // Magaca u beddel mid qurux badan (e.g. full_name -> FULL NAME)
              let cleanHeader =
                key === "full_name"
                  ? "USER DETAILS"
                  : key.replace("_", " ").toUpperCase();
              th += `<th class="px-8 py-4 text-left">${cleanHeader}</th>`;
            }
          });
          th += "<th class='px-8 py-4 text-center'>ACTIONS</th></tr>";

          // --- DHISIDDA DYNAMIC BODY ---
          users.forEach((user) => {
            // 1. Qeex badhamada caadiga ah (Edit & Delete)
            let actionButtons = `
        <button onclick="editUser('${user.id}')" class="text-blue-500 hover:text-blue-700 transition-colors cursor-pointer " title="Edit">
            <span class="material-symbols-outlined">edit</span>
        </button>
        <button onclick="deleteUser('${user.id}')" class="text-red-500 hover:text-red-700 transition-colors cursor-pointer " title="Delete">
            <span class="material-symbols-outlined">delete</span>
        </button>
    `;

            // 2. Hubi Status-ka si aad ugu darto badhanka saxda ah
            let statusButton = "";
            if (user.status === "Pending") {
              statusButton = `
            <button onclick="approveUser('${user.id}')" class="text-green-500 hover:bg-green-50 rounded-lg p-1 cursor-pointer " title="Approve">
                <span class="material-symbols-outlined">check_circle</span>
            </button>`;
            } else if (user.status === "Active") {
              statusButton = `
            <button onclick="toggleBlock('${user.id}', 'Block')" class="text-orange-500 hover:bg-orange-50 rounded-lg p-1 cursor-pointer " title="Block">
                <span class="material-symbols-outlined">block</span>
            </button>`;
            } else if (user.status === "Block") {
              statusButton = `
            <button onclick="toggleBlock('${user.id}', 'Active')" class="text-green-500 hover:bg-green-50 rounded-lg p-1 cursor-pointer " title="Unblock">
                <span class="material-symbols-outlined">lock_open</span>
            </button>`;
            }

            tr += `<tr class="border-b border-primary/5 hover:bg-slate-50 transition-colors">`;

            headerKeys.forEach((key) => {
              if (!excludedKeys.includes(key)) {
                if (key === "full_name") {
                  tr += `
                    <td class="p-6">
                        <div class="flex items-center gap-3">
                            <img src="${USER_UPLOAD_URL + (user.image || "default.png")}" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <p class="font-bold text-slate-800">${user.full_name}</p>
                                <p class="text-xs text-slate-400">${user.email}</p>
                            </div>
                        </div>
                    </td>`;
                } else if (key === "status") {
                  // Qurxinta midabka Status-ka
                  let statusClass =
                    user.status === "Active"
                      ? "text-green-500 bg-green-50"
                      : user.status === "Pending"
                        ? "text-orange-500 bg-orange-50"
                        : "text-red-500 bg-red-50";
                  tr += `<td class="p-6"><span class="px-3 py-1 rounded-lg text-xs font-bold ${statusClass}">${user.status}</span></td>`;
                } else {
                  tr += `<td class="p-6 text-slate-600">${user[key]}</td>`;
                }
              }
            });

            // 3. Halkan ku dar badhamadii la isku dhafay (Status Button + Edit + Delete)
            tr += `
        <td class="p-6 text-right flex justify-end gap-2 text-xl">
            ${statusButton}
            ${actionButtons}
        </td>
    </tr>`;
          });

          // users.forEach((user) => {
          //   tr += `<tr class="border-b border-primary/5 hover:bg-slate-50 transition-colors">`;

          //   headerKeys.forEach((key) => {
          //     if (!excludedKeys.includes(key)) {
          //       if (key === "full_name") {
          //         // Col1 (Image + Name + Email)
          //         tr += `
          //                       <td class="p-6">
          //                           <div class="flex items-center gap-3">
          //                               <img src="${USER_UPLOAD_URL + (user.image || "default.png")}" class="w-10 h-10 rounded-full object-cover">
          //                               <div>
          //                                   <p class="font-bold text-slate-800">${user.full_name}</p>
          //                                   <p class="text-xs text-slate-400">${user.email}</p>
          //                               </div>
          //                           </div>
          //                       </td>`;
          //       } else if (key === "status") {
          //         // Qurxinta Status-ka
          //         let statusColor =
          //           user.status === "Active"
          //             ? "text-green-500 bg-green-50"
          //             : "text-red-500 bg-red-50";
          //         tr += `<td class="p-6"><span class="px-3 py-1 rounded-lg text-xs font-bold ${statusColor}">${user.status}</span></td>`;
          //       } else {
          //         // Wixii kale oo xog ah (Created_at, Username, iwm)
          //         tr += `<td class="p-6 text-slate-600">${user[key]}</td>`;
          //       }
          //     }
          //   });

          //   // Actions Column (Had iyo jeer u dambeeya)
          //   tr += `
          //           <td class="p-6 text-right flex justify-end gap-1">
          //               <button onclick="editUser('${user.id}')" class="text-blue-500"><span class="material-symbols-outlined">edit</span></button>
          //               <button onclick="deleteUser('${user.id}')" class="text-red-500"><span class="material-symbols-outlined">delete</span></button>
          //           </td>
          //       </tr>`;
          // });
        }

        $("#userTable thead").html(th);
        $("#userTable tbody").html(tr);

        // Pagination State
        if (users.length < 10) {
          $("#nextBtn").prop("disabled", true);
        } else {
          $("#nextBtn").prop("disabled", false);
        }
        if (currentPage === 1) {
          $("#prevBtn").prop("disabled", true);
        } else {
          $("#prevBtn").prop("disabled", false);
        }
      } else {
        // Haddii data.message ay tahay null, isticmaal qoraal kale
        let errorMsg = data.message || "Cilad aan la garanayn ayaa dhacday.";
        Swal.fire("Error", errorMsg);
      }
    },
    error: function (xhr, status, error) {
      sendResponse("error", "An error occurred while fetching users.");
      hideLoader(); // Haddii uu error dhaco, dami loading-ka
    },

    complete: function () {
      hideLoader();
    },
  });
}

function editUser(id) {
  let sendingData = {
    action: "read_single_user",
    id: id,
  };
  $.ajax({
    method: "POST",
    dataType: "JSON",
    url: api_url,
    data: sendingData,
    beforeSend: function () {
      showLoader(1, 5000);
    },
    success: function (data) {
      let status = data.status;
      let response = data.data;

      if (status) {
        btnAction = "Update";

        $("#update_id").val(response.id);
        $("#full_name").val(response.full_name);
        $("#username").val(response.username);
        $("#phone").val(response.phone);
        $("#email").val(response.email);
        $("#role").val(response.role_id);
        // $("#image").val(response.image);
        // $("#password").val(response.password);

        $("#password").val("");

        // Muuji sawirka hadda u yaala
        // Gudaha success function-ka editUser

        let imageFile = response.image
          ? USER_UPLOAD_URL + response.image
          : defaultImage;
        $("#show").attr("src", imageFile);
        showModel();
      }
    },

    complete: function () {
      hideLoader();
    },
  });
}

function deleteUser(id) {
  Swal.fire({
    title: "Ma hubtaa?",
    text: "User-kan gebi ahaanba waa la tirtirayaa!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Haa, tirtir",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: api_url,
        method: "POST",
        dataType: "json",
        data: {
          action: "delete_user",
          id: id,
        },
        beforeSend: function () {
          showLoader(1, 5000);
        },
        success: function (data) {
          if (data.status) {
            let username = data.data.username;
            let userId = data.data.id;

            Swal.fire({
              icon: "success",
              title: "La tirtiray!",
              // html: `
              //   User-kan waa la tirtiray:
              //   <br><b>Username:</b> ${username}
              //   <br><b>ID:</b> ${userId}
              // `,
              html: `
                <div style="text-align:center; line-height:1.6">
                  <p style="font-size:15px; margin-bottom:10px;">
                    <b>User-kan waa la tirtiray si guul leh</b>
                  </p>

                  <div style="
                    background:#f8fafc;
                    border:2px solid #e2e8f0;
                    border-radius:15px;
                    padding:10px;
                  ">
                    <p><b>Username:</b> <span style="color:#0f172a">${username}</span></p>
                    <p><b>User ID:</b> <span style="color:#334155">${userId}</span></p>
                  </div>
                  </div>
                `,
            });

            loadUsers();
          } else {
            Swal.fire("Khalad", data.message, "error");
          }
        },
        complete: function () {
          hideLoader();
        },
      });
    }
  });
}

function approveUser(id) {
  let sendingData = {
    action: "approve_user",
    id: id,
  };
  $.ajax({
    method: "POST",
    dataType: "JSON",
    url: api_url,
    data: sendingData,

    beforeSend: function () {
      showLoader(1, 5000);
    },
    success: function (data) {
      let status = data.status;
      let response = data.data;

      if (status) {
        Swal.fire("User approved successfully!", response, "success");
        loadUsers();
      } else {
        Swal.fire("User approved Error", response, "error");
      }
    },
    complete: function () {
      hideLoader();
    },
  });
}

function toggleBlock(id, status) {
  let actionText = status === "Block" ? "block" : "unblock";
  Swal.fire({
    title: `Are you sure you want to ${actionText} this user?`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: status === "Block" ? "#d33" : "#89986D",
    confirmButtonText: `Yes, ${actionText}!`,
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: api_url,
        method: "POST",
        data: { action: "update_status", id: id, status: status },
        dataType: "json",
        beforeSend: function () {
          showLoader(1, 5000);
        },
        success: function (response) {
          if (response.status) {
            Swal.fire("Updated!", response.message, "success");
            loadUsers();
          } else {
            Swal.fire("Error", response.message, "error");
          }
        },
        complete: function () {
          hideLoader();
        },
      });
    }
  });
}

// Marka la gujiyo Update
$("#userTable").on("click", "button.update_info", function () {
  let id = $(this).attr("update_id"); // Soo qaado attribute-ka update_id
  FetchUser(id);
});

// Marka la gujiyo Delete
$("#userTable").on("click", "button.delete_info", function () {
  let id = $(this).attr("delete_id"); // Soo qaado attribute-ka delete_id

  swal({
    title: "Ma hubtaa?",
    text: "Xogtan dib looma soo celin karo!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  }).then((willDelete) => {
    if (willDelete) {
      DeleteUserInfo(id);
    }
  });
});

// Marka la gujiyo Approve
$("#userTable").on("click", "button.approve_info", function () {
  let id = $(this).attr("approve_id"); // Soo qaado attribute-ka approve_id
  approveUser(id);
});
