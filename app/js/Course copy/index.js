const api_url = "../../api/Courses/index.php";
let btnAction = "Insert";

$(document).ready(function () {
  if ($("#coursesGrid").length) {
    loadManagementCourses();
  }

  // Thumbnail Preview Interaction
  $("#thumbInput").change(function () {
    if (this.files && this.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        $("#thumbPreview").attr("src", e.target.result).removeClass("hidden");
        $("#uploadPlaceholder").addClass("hidden");
      };
      reader.readAsDataURL(this.files[0]);
    }
  });

  // Create Course Form Submission
  $("#createCourseForm").submit(function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    let action = btnAction === "Insert" ? "register_course" : "update_course";
    formData.append("action", action);

    AJAX.post(api_url, formData, function (response) {
      if (response.status) {
        Toast.show(true, response.message);
        // displayMessage("success", response.message);
        closeCourseModal();
        loadManagementCourses();
        $("#createCourseForm")[0].reset();
        resetThumbPreview();
        btnAction = "Insert";
      } else {
        // displayMessage("error", response.message);
      }
    });
  });
});

function editCourse(id) {
  const formData = new FormData();
  formData.append("action", "read_single_course");
  formData.append("id", id);

  AJAX.post(api_url, formData, function (response) {
    if (response.status) {
      const course = response.data;
      // Buuxi Form-ka
      $("#update_id").val(course.id);
      btnAction = "Update";
      // $("#update_id").val("");

      $("#title").val(course.title);
      $("#description").val(course.description);
      $("#level").val(course.level);
      $("#access_type").val(course.access_type);
      $("#price").val(course.price);

      // preview-ga tusi
      if (course.thumbnail) {
        const imgPath = COURSE_UPLOAD_URL + course.thumbnail;
        $("#thumbPreview").attr("src", imgPath).removeClass("hidden");
        // $("#uploadPlaceholder").addClass("hidden");
      }

      // Bedel qoraalka Button-ka iyo Modal-ka
      $("#courseModal h2").text("Edit Course");
      $("#createCourseForm button[type='submit']").text("Update Course");

      openCourseModal();
      // loadManagementCourses();
    }
  });
}

function loadManagementCourses() {
  const formData = new FormData();
  formData.append("action", "Read_All_Courses");

  AJAX.post(api_url, formData, function (response) {
    let cards = "";
    if (!response.status || response.data.length === 0) {
      cards = `<div class="col-span-full py-20 text-center animate-fadeIn">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-50 rounded-full mb-4">
                            <span class="material-symbols-outlined text-4xl text-gray-300">layers_clear</span>
                        </div>
                        <p class="text-gray-400 font-bold italic">No courses found. Start by creating one!</p>
                     </div>`;
    } else {
      response.data.forEach((course) => {
        const levelColors = {
          beginner: "bg-emerald-100 text-emerald-700",
          intermediate: "bg-blue-100 text-blue-700",
          advanced: "bg-purple-100 text-purple-700",
        };
        const levelClass =
          levelColors[course.level] || "bg-gray-100 text-gray-700";

        // 2. Habaynta Price-ka
        const priceDisplay =
          course.access_type === "free"
            ? '<span class="text-gray-700 font-black">FREE</span>'
            : `<span class=" text-emerald-700 font-black">$${parseFloat(course.price).toFixed(2)}</span>`;

        cards += `
                <div class="group bg-white rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-blue-600/10 transition-all duration-500 overflow-hidden animate-fadeIn p-2 ">
                    <div class="h-48 relative overflow-hidden">
                        <img  src="${course.thumbnail ? (course.thumbnail.includes("http") ? course.thumbnail : COURSE_UPLOAD_URL + course.thumbnail.replace("course_thumbnails/", "")) : COURSE_UPLOAD_URL + "course_default.png"}" onerror="this.src='${COURSE_UPLOAD_URL}course_default.png'"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        
                        <div class="absolute flex flex-col inset-0 bg-gradient-to-t from-gray-900/80 via-transparent to-transparent opacity-60"></div>
                        <div class="absolute top-2 right-4 flex gap-2">
                             <span class="px-3 py-1 bg-white/20 backdrop-blur-md text-black text-[10px] font-black uppercase tracking-widest rounded-lg border border-black/20">${course.status}</span>
                        </div>
                        <div class="absolute bottom-2 right-4 flex gap-2 mt-1">
                             <span class="px-3 py-1 ${levelClass} text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm">${course.level}</span>
                        </div>
                    
                        
                    </div>
                    <div class="p-8">
                        <h3 class="text-lg font-black text-gray-900 mb-3 line-clamp-1">${course.title}</h3>
                        <p class="text-gray-500 text-sm font-medium mb-3 line-clamp-2">${course.description || "No description provided."}</p>
                
                         
                        <div class="flex items-center gap-2 mb-2 bg-gray-50 w-fit px-4 py-2 rounded-xl">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Price:</span>
                            ${priceDisplay}
                        </div>
                     
                 
                        <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                            <a href="../../views/Course/lessons.php?id=${course.id}" class="inline-flex items-center gap-2 text-sm font-black text-blue-600 hover:gap-3 transition-all">
                                <span class="material-symbols-outlined text-lg">edit_note</span>
                                Manage Content
                            </a>
                      
                            <div class ="flex items-center gap-2 opacity-50 group-hover:opacity-100 transition-opacity duration-700">
                                <button onclick="editCourse('${course.id}')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-400 hover:bg-blue-600 hover:text-white transition-all duration-300 cursor-pointer shadow-sm mx">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </button>
                                <button onclick="deleteCourse(${course.id})" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-400 hover:bg-red-500 hover:text-white transition-all duration-300 cursor-pointer shadow-sm">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                `;
      });
    }
    $("#coursesGrid").html(cards);
  });
}

function resetCourseForm() {
  $("#createCourseForm")[0].reset();
  $("#update_id").val("");
  btnAction = "Insert";

  // 4. Nadiifi sawirka preview-ga ah
  resetThumbPreview();
}

function openCourseModal() {
  $("#courseModal").removeClass("hidden").addClass("flex");
  $("body").addClass("overflow-hidden");
}

function closeCourseModal() {
  $("#courseModal").addClass("hidden").removeClass("flex");
  $("body").removeClass("overflow-hidden");
  resetCourseForm();
}

function resetThumbPreview() {
  $("#thumbPreview").addClass("hidden").attr("src", "#");
  $("#uploadPlaceholder").removeClass("hidden");
}

function deleteCourse(id) {
  Swal.fire({
    title: "Ma hubtaa?",
    text: "Action-kan dib looma soo celin karo!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3B82F6",
    cancelButtonColor: "#EF4444",
    confirmButtonText: "Haa, tirtir!",
    cancelButtonText: "Iska dhaaf",
    customClass: { popup: "rounded-[2rem]" },
  }).then((result) => {
    if (result.isConfirmed) {
      const formData = new FormData();
      formData.append("action", "delete_course");
      formData.append("id", id);

      // Isticmaalka AJAX Wrapper-ka
      AJAX.post(api_url, formData, function (response) {
        // SUCCESS: Halkan Toast-ka ku muuji
        Toast.show(true, response.message);

        // Cusboonaysii liiska koorsooyinka
        loadManagementCourses();
      });

      //    Uma baahnid inaad halkan ku qorto 'else' ama 'Toast.show(false, ...)'
      //    sababtoo ah helper-ka AJAX ee aan hore u sameynay ayaa si automatic ah
      //    u soo bandhigaya Toast-ka casaan ah haddii ay ciladi dhacdo
    }
  });
}

const accessType = document.getElementById("access_type");
const priceContainer = document.getElementById("priceContainer");

accessType.addEventListener("change", function () {
  if (this.value === "free") {
    priceContainer.style.opacity = "0.5";
    priceContainer.style.border = "1px solid #ccc";
    document.getElementById("price").value = "$0.00";
    document.getElementById("price").readOnly = true;
  } else {
    priceContainer.style.opacity = "1";
    document.getElementById("price").readOnly = false;
  }
});
