// API URL points using window.BASE_URL for consistency across folder depths
const MENU_API_URL = window.BASE_URL + "api/user_authorities.php";
const USERS_API_URL = window.BASE_URL + "api/users.php";
const PROFILE_API_URL = window.BASE_URL + "api/sidebar.php";
const defaultImage = window.BASE_URL + "uploads/User_profile/default.png";

$(document).ready(function () {
  load_user_menus();
  init_profile_upload();
});

/**
 * Load Dynamic Menus for current user
 */
function load_user_menus() {
  // Using AJAX.post from reusable/helper.js as requested
  AJAX.post(
    MENU_API_URL,
    { action: "get_User_Menus" },
    function (response) {
      if (response.status && response.data) {
        render_sidebar_menu(response.data);
        setActiveMenu();
      } else {
        $("#user_menu").html(
          `<p class="text-xs text-rose-400 px-4">No menus assigned.</p>`,
        );
      }
    },
    function (errorMsg) {
      $("#user_menu").html(
        `<p class="text-xs text-rose-500 px-4">Failed to load menus.</p>`,
      );
    },
  );
}

/**
 * Render Menu HTML
 */
function render_sidebar_menu(data) {
  let menuHtml = `<div class="px-3 mb-3 flex items-center justify-between">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Menu</p>
                        </div>`;
  let currentCategory = "";

  data.forEach((menu) => {
    let targetUrl = window.BASE_URL + `views/${menu["link"]}`;

    if (menu["category_name"] !== currentCategory) {
      if (currentCategory !== "") {
        menuHtml += `</div></div>`;
      }

      menuHtml += `
                    <div class="mb-2">
                        <button onclick="toggleSubMenu(this)" class="nav-btn w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-white/5 transition-all font-medium text-[14px] group text-slate-300 hover:text-white">
                            <div class="flex items-center gap-4">
                                <span class="material-symbols-rounded text-[20px] text-slate-400 group-hover:text-blue-400 transition-colors">${menu["category_icon"] || "apps"}</span>
                                <span>${menu["category_name"]}</span>
                            </div>
                            <span class="material-symbols-rounded chevron text-[18px] text-slate-500 transition-transform duration-300">expand_more</span>
                        </button>
                        <div class="submenu mt-1 ml-4 pl-4 border-l border-slate-700/50 flex flex-col gap-1 hidden">
                    `;
      currentCategory = menu["category_name"];
    }

    menuHtml += `
                    <a href="${targetUrl}" data-link="${menu["link"]}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/5 transition-all text-slate-400 hover:text-blue-400 text-[13px] font-medium group sub-item">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-600 group-hover:bg-blue-400 transition-colors"></span>
                        ${menu["link_name"]}
                    </a>
                `;
  });

  if (currentCategory !== "") {
    menuHtml += `</div></div>`;
  }

  $("#user_menu").html(menuHtml);
}

/**
 * Highlights the active menu item based on URL
 */
function setActiveMenu() {
  let currentUrlPath = window.location.pathname;

  $("#user_menu .sub-item").each(function () {
    let linkData = $(this).attr("data-link");

    if (currentUrlPath.includes(linkData)) {
      $(this).addClass("text-blue-400 bg-white/5");
      $(this).find("span").addClass("bg-blue-400").removeClass("bg-slate-600");

      let submenu = $(this).closest(".submenu");
      submenu.addClass("show").removeClass("hidden");

      let btn = submenu.prev("button");
      btn.addClass("bg-white/5");
      btn.find(".chevron").addClass("rotate-180");
    }
  });
}

/**
 * Profile Image Upload Sync (Moving logic from sidebar.php to here)
 */
function init_profile_upload() {
  const fileImage = document.querySelector("#sidebar_user_image");
  const showInput = document.querySelector("#sidebar_avatar");

  if (fileImage && showInput) {
    fileImage.addEventListener("change", function (e) {
      let file = this.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = (event) => {
        showInput.src = event.target.result;
      };
      reader.readAsDataURL(file);

      let formData = new FormData();
      formData.append("action", "update_profile_image");
      formData.append("image", file);

      AJAX.post(PROFILE_API_URL, formData, function (data) {
        if (data.status && typeof Toast !== "undefined") {
          Toast.show(true, "Profile image updated successfully!");
        }
      });
    });
  }
}

/**
 * Sidebar Actions
 */
window.toggleSidebar = function () {
  const sidebar = document.getElementById("sidebar");
  const mainContent = document.getElementById("main-content");
  const overlay = document.getElementById("mobile-overlay");
  const isDesktop = window.innerWidth >= 1024;

  if (isDesktop) {
    sidebar.classList.toggle("lg:translate-x-0");
    sidebar.classList.toggle("lg:-translate-x-full");
    mainContent.classList.toggle("lg:ml-72");
    mainContent.classList.toggle("lg:ml-0");
    mainContent.classList.toggle("lg:w-[calc(100%-18rem)]");
  } else {
    sidebar.classList.toggle("-translate-x-full");
    overlay.classList.toggle("hidden");
    setTimeout(() => overlay.classList.toggle("opacity-0"), 10);
  }
};

window.toggleSubMenu = function (button) {
  let submenu = button.nextElementSibling;

  if (!submenu.classList.contains("show")) {
    closeAllSubMenus();
  }

  submenu.classList.toggle("show");
  submenu.classList.toggle("hidden");
  button.classList.toggle("bg-white/5");
  let chev = button.querySelector(".chevron");
  if (chev) chev.classList.toggle("rotate-180");
};

function closeAllSubMenus() {
  document.querySelectorAll(".submenu").forEach((ul) => {
    ul.classList.remove("show");
    ul.classList.add("hidden");
    ul.previousElementSibling.classList.remove("bg-white/5");
    let chev = ul.previousElementSibling.querySelector(".chevron");
    if (chev) chev.classList.remove("rotate-180");
  });
}

window.edit_profile = function (id) {
  const formData = new FormData();
  formData.append("action", "read_single_user");
  formData.append("id", id);

  AJAX.post(USERS_API_URL, formData, function (response) {
    if (response.status) {
      const data = response.data;
      $("#update_id").val(data.id);

      let imageFile = data.image
        ? defaultImage.replace("default.png", data.image)
        : defaultImage;
      $("#show").attr("src", imageFile);
      if (typeof showModel === "function") showModel();
    }
  });
};
