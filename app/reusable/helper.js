
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
          // Haddii aad rabto inaad Toast guul ah muujiso mar kasta:
          // Toast.show(true, response.message);
          Success(response);
        } else {
          let errorMsg =
            response && response.message
              ? response.message
              : "Cilad aan la garanayn ayaa dhacday.";

          // --- CENTRALIZATION: Halkan ayuu Toast si toos ah ugu shaqaynayaa ---
          Toast.show(false, errorMsg);

          if (Error) Error(errorMsg);
        }
      },
      error: function (xhr) {
        if (typeof hideLoader === "function") hideLoader();
        let errorMsg = "server connection error occurred.";

        // --- CENTRALIZATION: Ciladaha Server-ka (404, 500, etc) ---
        Toast.show(false, errorMsg);

        if (Error) Error(errorMsg);
      },
    });
  },
};
