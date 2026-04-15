const api_url = "../../api/Courses/browse.php";

$(document).ready(function () {
  fetchCourses();

  // Live raadin (Search)
  $("#courseSearch").on("keyup", function () {
    fetchCourses($(this).val());
  });
});

function fetchCourses(search = "") {
  $.post(
    api_url,
    {
      action: "get_available_courses",
      search: search,
    },
    function (res) {
      if (res.status) {
        renderCourses(res.data);
      } else {
        Toast.show(false, res.message);
      }
    },
    "json",
  );
}

 

function renderCourses(courses) {
  let html = "";
  if (courses.length === 0) {
    html = `
            <div class="col-span-full py-20 text-center">
                <div class="inline-flex p-6 bg-gray-50 rounded-full mb-4">
                    <span class="material-symbols-outlined text-4xl text-gray-300">search_off</span>
                </div>
                <p class="text-gray-400 font-bold italic text-lg">Wali ma jiraan koorsooyin la helay...</p>
            </div>`;
    $("#browseCoursesGrid").html(html);
    return;
  }

  courses.forEach((course) => {
    let btnHtml = "";

    // 1. Status Logic (Sidiisii hore)
    if (course.enrollment_status === "Not Enrolled") {
      btnHtml = `<button onclick="enrollNow('${course.id}')" class="group/btn relative w-full p-4 mb-2 bg-gray-900 text-white font-black rounded-2xl overflow-hidden transition-all duration-300 hover:bg-blue-600 active:scale-95 cursor-pointer">
                    <span class="relative z-10 flex items-center justify-center gap-2 text-[10px] uppercase tracking-[0.2em]">
                        <span class="material-symbols-outlined text-sm">add_shopping_cart</span> Ka mid noqo
                    </span>
                 </button>`;
    } else if (course.enrollment_status === "Pending") {
      btnHtml = `<div class="w-full py-4 bg-amber-50 text-amber-600 font-black rounded-2xl border border-amber-100 flex items-center justify-center gap-2 text-[10px] uppercase tracking-[0.2em]">
                    <span class="material-symbols-outlined text-sm animate-spin">progress_activity</span> Wait minutes until the administrator approves your enrollment
                 </div>`;
    } else if (course.enrollment_status === "Blocked") {
      btnHtml = `<div class="w-full py-4 bg-red-50 text-red-600 font-black rounded-2xl border border-red-100 flex items-center justify-center gap-2 text-[10px] uppercase tracking-[0.2em]">
                    <span class="material-symbols-outlined text-sm animate-spin">progress_activity</span>Blocked Course Contact the Administrator to unblock it
                 </div>`;
    } else {
      btnHtml = `<button onclick="goToCourse('${course.id}')" class="w-full py-4 bg-green-50 text-green-600 font-black rounded-2xl border border-green-100 hover:bg-green-600 hover:text-white transition-all duration-300 flex items-center justify-center gap-2 text-[10px] uppercase tracking-[0.2em] cursor-pointer">
                    <span class="material-symbols-outlined text-sm text-green-500 group-hover:text-white">play_circle</span> Bilaw Barashada
                 </button>`;
    }

    let finalImg = getImgPath(course.thumbnail);

    const priceDisplay =
      course.access_type === "free" || course.price == "0.00"
        ? '<span class="text-green-600 font-black text-lg">FREE</span>'
        : `<span class="text-blue-600 font-black text-lg">$${course.price}</span>`;

    html += `
            <div class="course-card group bg-white rounded-[2.5rem] border border-gray-100 p-2 transition-all duration-500 hover:-translate-y-3 shadow-sm hover:shadow-xl">
                <div class="h-48 relative overflow-hidden rounded-[2rem] mb-4">
                  <img src="${finalImg}" 
                       onerror="this.src='${COURSE_UPLOAD_URL}course_default.png'" 
                       class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                  
                    <div class="absolute top-2 right-2 flex justify-between items-center">
                      
                    
                        <div class="bg-white/90 backdrop-blur-md px-2 py-1  rounded-full shadow-sm">
                             <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest">${course.status}</span>
                        </div>
                    </div>
                </div>

                <div class="px-2">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-5 h-5 rounded-full bg-blue-50 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[12px] text-blue-500">person</span>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">${course.instructor_name || "Expert"}</span>
                    </div>
                    
                    <h3 class="text-lg font-black text-gray-900 mb-2 group-hover:text-blue-600 transition-colors line-clamp-1">
                        ${course.title}
                    </h3>
                    
                    <p class="text-gray-500 text-[13px] font-medium mb-4 line-clamp-2 leading-relaxed opacity-80">
                        ${course.description}
                    </p>

                     <div class="flex items-center justify-between mb-6 p-3 bg-gray-50 rounded-2xl">
                    <div class="glass-effect px-3 py-1.5 rounded-full flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-pulse"></span>
                            <span class="text-[10px] font-black text-gray-900 uppercase tracking-widest">${course.total_lessons || 0} Lessons</span>
                        </div>
                        <div class="bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-full shadow-sm">
                             <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest">${course.level}</span>
                        </div>
                        </div>

                    <div class="flex items-center justify-between mb-6 p-3 bg-gray-50 rounded-2xl">
                        <div class="flex flex-col">
                            <span class="text-[12px] text-gray-400 uppercase font-bold tracking-tighter">Price</span>
                            ${priceDisplay}
                        </div>
                 
                    
                    </div>

                    <div class="mt-auto">
                        ${btnHtml}
                    </div>
                </div>
            </div>
        `;
  });

  $("#browseCoursesGrid").html(html);
}

// 1. Function-ka Enrollment-ka (Kani waa kan ka maqnaa oo ReferenceError bixinayay)
function enrollNow(courseId) {
  $.post(
    api_url,
    {
      action: "enroll_student",
      course_id: courseId,
    },
    function (res) {
      if (res.status) {
        Toast.show(true, res.message);
        fetchCourses(); // Cusboonaysii liiska si badanku u isbeddelo
      } else {
        Toast.show(false, res.message);
      }
    },
    "json",
  );
}

// 2. Logic-ga sawirka (Halkan ku beddel img src-ga dhexdiisa si uu double path-ka u saxo)
// Nuqul ka qaad qaybtan oo kaliya haddii aad rabto inaad img tag-ga dhexdiisa ku beddesho:
const getImgPath = (thumbnail) => {
  if (!thumbnail) return COURSE_UPLOAD_URL + "course_default.png";
  if (thumbnail.includes("http")) return thumbnail;

  // split('/').pop() waxay soo qabanaysaa magaca sawirka oo kaliya (e.g. course_123.jpg)
  // waxayna iska tuuraysaa folder kasta oo database-ka ku dhex jiray si uusan labo jeer u noqon
  return COURSE_UPLOAD_URL + thumbnail.split("/").pop();
};
