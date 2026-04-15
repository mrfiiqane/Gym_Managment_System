const api_url = "../api/users/users.php";
const defaultImage = "../uploads/User_profile/default.png";

$(document).ready(function () {
    load_user_menus();
});

// Profile Image Upload Preview
let fileImage = document.querySelector("#image");
let showInput = document.querySelector("#show");

if (fileImage && showInput) {
    fileImage.addEventListener("change", (e) => {
        const reader = new FileReader();
        reader.onload = (event) => {
            showInput.src = event.target.result;
        };
        if (e.target.files[0]) {
            reader.readAsDataURL(e.target.files[0]);
        }
    });
}

function load_user_menus() {
    $.ajax({
        method: "POST",
        dataType: "JSON",
        url: "../api/user_authorities.php",
        data: { action: "get_User_Menus" },
        success: function (response) {
            if (response.status && response.data) {
                let menuHtml = `<div class="px-3 mb-3 flex items-center justify-between">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Menu</p>
                                </div>`;
                let currentCategory = "";

                response.data.forEach((menu) => {
                    // Normalize the link path relative to the BASE_URL or root. Based on older code, menu["link"] contains the url e.g. "users.php".
                    // The backend returned only the filename so we append it to the category folder later or if it has the full path, even better.
                    // Assumes menu['link'] provides necessary view path. E.g. "../views/link_name". Wait, older link logic needs to be constructed.
                    let targetUrl = `../../views/${menu["link"]}`; 

                    // Check if category changed
                    if (menu["category_name"] !== currentCategory) {
                        // Close previous category submenu if not first
                        if (currentCategory !== "") {
                            menuHtml += `</div></div>`;
                        }

                        // Start new category
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

                    // Append link item to current category
                    menuHtml += `
                        <a href="${targetUrl}" data-link="${menu["link"]}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/5 transition-all text-slate-400 hover:text-blue-400 text-[13px] font-medium group sub-item">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-600 group-hover:bg-blue-400 transition-colors"></span>
                            ${menu["link_name"]}
                        </a>
                    `;
                });

                // Close the last category
                if (currentCategory !== "") {
                    menuHtml += `</div></div>`;
                }

                $("#user_menu").html(menuHtml);
                setActiveMenu();
            } else {
                $("#user_menu").html(`<p class="text-xs text-rose-400 px-4">No menus assigned.</p>`);
            }
        },
        error: function () {
            $("#user_menu").html(`<p class="text-xs text-rose-400 px-4">Failed to load menus.</p>`);
        }
    });
}

function setActiveMenu() {
    let currentUrlPath = window.location.pathname;
    
    // Check all sub items
    $("#user_menu .sub-item").each(function () {
        let linkHref = $(this).attr("href");
        let linkData = $(this).attr("data-link");
        
        // Match path
        if (currentUrlPath.includes(linkData)) {
            // Make the sub item active
            $(this).addClass("text-blue-400 bg-white/5");
            $(this).find("span").addClass("bg-blue-400").removeClass("bg-slate-600");
            
            // Open the parent category
            let submenu = $(this).closest(".submenu");
            submenu.addClass("show").removeClass("hidden");
            
            let btn = submenu.prev("button");
            btn.addClass("bg-white/5");
            btn.find(".chevron").addClass("rotate-180");
        }
    });
}

// Global scope toggle for sidebar and submenu is already in sidebar.php 
// Just ensuring the "hidden" class from Tailwind works nicely with JS.
window.toggleSubMenu = function (button) {
    let submenu = button.nextElementSibling;
    
    // Close other submenus
    if (!submenu.classList.contains('show')) {
        let allSubmenus = document.querySelectorAll('.submenu');
        allSubmenus.forEach(ul => {
            if(ul !== submenu){
                ul.classList.remove('show');
                ul.classList.add('hidden');
                ul.previousElementSibling.classList.remove('bg-white/5');
                let chev = ul.previousElementSibling.querySelector('.chevron');
                if(chev) chev.classList.remove('rotate-180');
            }
        });
    }

    // Toggle current
    submenu.classList.toggle('show');
    submenu.classList.toggle('hidden');
    button.classList.toggle('bg-white/5');
    let chev = button.querySelector('.chevron');
    if(chev) chev.classList.toggle('rotate-180');
}

function edit_profile(id) {
    const formData = new FormData();
    formData.append("action", "read_single_user");
    formData.append("id", id);

    $.ajax({
        url: api_url,
        method: "POST",
        data: formData,
        dataType: 'JSON',
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.status) {
                const data = response.data;
                $("#update_id").val(data.id);

                let imageFile = data.image ? defaultImage.replace('default.png', data.image) : defaultImage;
                $("#show").attr("src", imageFile);
                if(typeof showModel === "function") showModel();
            }
        }
    });
}