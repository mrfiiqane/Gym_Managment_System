// 1. Modern Toast Component (Muuqaal aad u qurxoon)
const Toast = {
  show(status, message) {
    let container = document.getElementById("modern-toast-container");
    if (!container) {
      container = document.createElement("div");
      container.id = "modern-toast-container";
      container.className = "fixed top-8 right-8 z-[9999] flex flex-col gap-4";
      document.body.appendChild(container);

      // Styles for progressive bar
      const style = document.createElement("style");
      style.id = "modern-toast-style";
      style.innerHTML = `
        @keyframes shrinkWidth { from { width: 100%; } to { width: 0%; } }
        .animate-progress-bar { animation: shrinkWidth 4s linear forwards; }
      `;
      document.head.appendChild(style);
    }

    const id = "toast-" + Math.random().toString(36).substr(2, 9);
    
    // Naqshadaynta (Styling)
    const icon = status ? 'check_circle' : 'error';
    const accentColor = status ? 'text-emerald-500' : 'text-rose-500';
    const bgColor = status ? 'bg-emerald-500/10' : 'bg-rose-500/10';
    const barColor = status ? 'bg-emerald-500' : 'bg-rose-500';
    const title = status ? 'Success' : 'Error';

    const toastHtml = `
      <div id="${id}" class="relative overflow-hidden flex items-center gap-4 bg-white/90 backdrop-blur-[8px] border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-4 rounded-2xl min-w-[320px] max-w-sm transform transition-all duration-500 translate-x-full opacity-0 pointer-events-auto hover:scale-[1.02]">
          
          <div class="flex items-center justify-center w-12 h-12 rounded-full ${bgColor} ${accentColor} flex-shrink-0 shadow-sm">
             <span class="material-symbols-outlined text-3xl">${icon}</span>
          </div>

          <div class="flex-1 pb-1">
             <h4 class="text-[14px] font-bold text-gray-800 tracking-wide">${title}</h4>
             <p class="text-[13px] font-medium text-gray-500 leading-snug mt-[2px]">${message}</p>
          </div>

          <!-- Progress Bar Indicator -->
          <div class="absolute bottom-0 left-0 h-1 border-b border-white ${barColor} animate-progress-bar opacity-80" style="width: 100%;"></div>
      </div>
    `;

    $(container).append(toastHtml);

    // Fade In Animation
    setTimeout(() => {
      $(`#${id}`).removeClass("translate-x-full opacity-0").addClass("translate-x-0 opacity-100");
    }, 10);

    // Fade Out Animation & Remove
    setTimeout(() => {
      $(`#${id}`).removeClass("translate-x-0 opacity-100").addClass("translate-x-6 opacity-0 scale-95");
      setTimeout(() => $(`#${id}`).remove(), 500);
    }, 5000);
  },
};

// 2. AJAX Wrapper (Wuxuu isticmaalayaa Toast)
const AJAX = {
  post: function (url, data, Success, Error) {
    if (typeof showLoader === "function") showLoader();

    let token =
      typeof csrfToken !== "undefined"
        ? csrfToken
        : $('meta[name="csrf-token"]').attr("content");
    let AjaxSettings = {
      url: url,
      method: "POST",
      data: data,
      dataType: "JSON",
    };

    if (token) {
      if (data instanceof FormData) data.append("csrf_token", token);
      else data.csrf_token = token;
    }

    if (data instanceof FormData) {
      AjaxSettings.processData = false;
      AjaxSettings.contentType = false;
    }

    $.ajax({
      ...AjaxSettings,
      success: function (response) {
        if (typeof hideLoader === "function") hideLoader();

        if (response && response.status) {
          Success(response);
        } else {
          let errorMsg =
            response && response.message
              ? response.message
              : "Cilad aan la garanayn ayaa dhacday.";

          Toast.show(false, errorMsg);
          if (Error) Error(errorMsg);
        }
      },
      error: function (xhr) {
        if (typeof hideLoader === "function") hideLoader();
        let errorMsg = "server connection error occurred.";

        Toast.show(false, errorMsg);
        if (Error) Error(errorMsg);
      },
    });
  },
};

// 3. Modern Confirm Component (Waxaa lagu beddelay Swal)
const ConfirmBox = {
  // Mode 1: Center Modal with Blur (Sleek & Professional)
  show(options) {
    let title = options.title || "Ma hubtaa?";
    let text = options.text || "Xogtan dib looma soo celin karo.";
    let confirmText = options.confirmText || "Haa, tirtir!";
    let cancelText = options.cancelText || "Iska daa";
    let onConfirm = options.onConfirm || function () {};

    let container = document.getElementById("confirm-box-container");
    if (!container) {
      container = document.createElement("div");
      container.id = "confirm-box-container";
      container.className = "fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300 pointer-events-none";
      document.body.appendChild(container);
    }

    const html = `
      <div id="confirm-modal-content" class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl p-8 max-w-sm w-full mx-4 transform scale-95 opacity-0 transition-all duration-300">
        <div class="flex justify-center mb-6">
          <div class="w-16 h-16 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-500 flex items-center justify-center">
             <span class="material-symbols-outlined text-4xl">warning</span>
          </div>
        </div>
        <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 text-center mb-2">${title}</h3>
        <p class="text-slate-500 dark:text-slate-400 text-center text-sm mb-8">${text}</p>
        <div class="flex gap-3">
          <button id="confirm-box-cancel" class="flex-1 py-3 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-200 text-slate-700 font-semibold rounded-xl transition-colors">
            ${cancelText}
          </button>
          <button id="confirm-box-accept" class="flex-1 py-3 px-4 bg-rose-500 hover:bg-rose-600 text-white font-semibold rounded-xl shadow-lg shadow-rose-500/30 transition-all hover:shadow-rose-500/40 hover:-translate-y-0.5">
            ${confirmText}
          </button>
        </div>
      </div>
    `;

    container.innerHTML = html;
    
    // Animation In
    requestAnimationFrame(() => {
      container.classList.remove("opacity-0", "pointer-events-none");
      let content = document.getElementById("confirm-modal-content");
      content.classList.remove("scale-95", "opacity-0");
      content.classList.add("scale-100", "opacity-100");
    });

    const closeBox = () => {
      let content = document.getElementById("confirm-modal-content");
      content.classList.remove("scale-100", "opacity-100");
      content.classList.add("scale-95", "opacity-0");
      container.classList.add("opacity-0", "pointer-events-none");
      setTimeout(() => { container.innerHTML = ''; }, 300);
    };

    document.getElementById("confirm-box-cancel").onclick = closeBox;
    document.getElementById("confirm-box-accept").onclick = () => {
      onConfirm();
      closeBox();
    };
  },

  // Mode 2: Flyout/Slide-over (Soft Delete Alternative)
  danger(options) {
    let title = options.title || "Delete Item?";
    let text = options.text || "You will lose this data for good.";
    let confirmText = options.confirmText || "Delete";
    let cancelText = options.cancelText || "Cancel";
    let onConfirm = options.onConfirm || function () {};

    let id = "flyout-" + Math.random().toString(36).substr(2, 9);
    let container = document.createElement("div");
    container.id = id;
    container.className = "fixed bottom-8 left-1/2 -translate-x-1/2 z-[9999] opacity-0 translate-y-10 transition-all duration-500 ease-out pointer-events-auto transform";
    
    container.innerHTML = `
      <div class="bg-slate-900 text-white px-6 py-5 rounded-2xl shadow-[0_20px_40px_rgba(0,0,0,0.3)] flex items-center gap-5 max-w-md w-full border border-slate-700/50">
        <span class="material-symbols-outlined text-rose-500 bg-rose-500/10 p-2 rounded-full">delete_forever</span>
        <div class="flex-1">
          <h4 class="font-semibold text-sm">${title}</h4>
          <p class="text-slate-400 text-[12px] mt-0.5">${text}</p>
        </div>
        <div class="flex items-center gap-2 border-l border-slate-700 pl-5 ml-2">
           <button class="flyout-cancel text-sm font-medium text-slate-400 hover:text-white px-2 py-1 transition-colors">${cancelText}</button>
           <button class="flyout-accept text-sm font-semibold bg-rose-500 text-white px-4 py-1.5 rounded-lg shadow hover:bg-rose-600 transition-colors">${confirmText}</button>
        </div>
      </div>
    `;
    
    document.body.appendChild(container);

    requestAnimationFrame(() => {
      container.classList.remove("opacity-0", "translate-y-10");
      container.classList.add("opacity-100", "translate-y-0");
    });

    const closeBox = () => {
      container.classList.remove("opacity-100", "translate-y-0");
      container.classList.add("opacity-0", "translate-y-10");
      setTimeout(() => container.remove(), 400);
    };

    container.querySelector(".flyout-cancel").onclick = closeBox;
    container.querySelector(".flyout-accept").onclick = () => {
      onConfirm();
      closeBox();
    };
  }
};
