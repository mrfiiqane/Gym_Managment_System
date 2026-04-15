const api_url = "../../api/Courses/lessons.php";
let btnAction = "Insert";
let lessonsData = [];
let chaptersData = [];
let completedLessons = [];
let can_manage = false;
let player = null;
let currentLessonId = null;


$(document).ready(function () {
  loadData();

  // Create Lesson Form Submission
  $("#lessonForm").submit(function (e) {
    e.preventDefault();
    const submitBtn = $(this).find('button[type="submit"]');
    const formData = new FormData(this);
    let action = btnAction === "Insert" ? "register_lessons" : "update_lesson";
    formData.append("action", action);
    submitBtn.text("Saving...").prop("disabled", true);

    AJAX.post(api_url, formData, function (response) {
      submitBtn.text("Save Lesson").prop("disabled", false); 
      if (response.status) {
        Toast.show(true, response.message);
        closeLessonModal();
        $("#lessonForm")[0].reset();
        fetchLessons();
        btnAction = "Insert";
      } else {
        Toast.show(false, response.message);
      }
    });
  });

  // Create Chapter Form Submission
  $("#chapterForm").submit(function (e) {
    e.preventDefault();
    const submitBtn = $(this).find('button[type="submit"]');
    const formData = new FormData(this);
    formData.append("action", "register_chapter");
    submitBtn.text("Saving...").prop("disabled", true);

    AJAX.post(api_url, formData, function (response) {
      submitBtn.text("Save Chapter").prop("disabled", false); 
      if (response.status) {
        Toast.show(true, response.message);
        closeChapterModal();
        $("#chapterForm")[0].reset();
        fetchChapters(); // reload chapters
      } else {
        Toast.show(false, response.message);
      }
    });
  });
});


// XSS Protection 
function escapeHTML(str) {
  if (!str) return "";
  return $("<div>").text(str).html();
}

// Load Course iyo Permissions
function loadData() {
  $("#lessonsList").html(`
        <div class="animate-pulse space-y-3 p-4">
            <div class="h-12 bg-gray-100 rounded-xl w-full"></div>
            <div class="h-12 bg-gray-100 rounded-xl w-full"></div>
            <div class="h-12 bg-gray-100 rounded-xl w-full"></div>
        </div>
    `);
    

  AJAX.post(
    api_url,
    {
      action: "get_course_info",
      course_id: COURSE_ID,
      csrf_token: CSRF_TOKEN,
    },
    function (res) {
  
      if (res.status) {
        const info = res.data;
        $("#courseHeaderTitle").text(escapeHTML(info.title));
        can_manage = info.can_manage;

        if (can_manage) {
          $("#manageActions").removeClass("hidden");
        }

        if (!can_manage && !info.is_enrolled) {
          renderAccessDenied();
          return;
        }

        fetchChapters();
      } else {
        Toast.show(false, res.message);
        $("#courseHeaderTitle").text("Error Loading Course");
      }
    },
  );
}

// Fetch Chapters
function fetchChapters() {
  AJAX.post(
    api_url,
    { action: "read_chapters", course_id: COURSE_ID, csrf_token: CSRF_TOKEN },
    function (res) {
      if (res.status) {
        chaptersData = res.data;
        updateChapterDropdown();
        fetchLessons();
      }
    }
  );
}

function updateChapterDropdown() {
    let html = '<option value="" disabled selected>Select a chapter first...</option>';
    chaptersData.forEach(chap => {
        html += `<option value="${chap.id}">${escapeHTML(chap.title)}</option>`;
    });
    $("#chapter_id").html(html);
}

// 3. Fetch Lessons
function fetchLessons() {
  AJAX.post(
    api_url,
    {
      action: "read_lessons",
      course_id: COURSE_ID,
      csrf_token: CSRF_TOKEN,
      limit: 100,
    },
    function (res) {
      if (res.status) {
        lessonsData = res.data;
        // Fetch progress before rendering
        AJAX.post(
            api_url,
            { action: "get_completed_lessons", course_id: COURSE_ID, csrf_token: CSRF_TOKEN },
            function(progRes) {
                if(progRes.status) completedLessons = progRes.data;
                renderLessons();
            }
        );
      }
    },
  );
}

// Render Sidebar List with DB Chapters
function renderLessons() {
  const list = $("#lessonsList");
  list.empty();
  $("#lessonCount").text(lessonsData.length + " Lessons");

  if (chaptersData.length === 0 && lessonsData.length === 0) {
      list.html(`<div class="p-8 text-center text-gray-400 font-medium">No content added yet. Add a chapter first.</div>`);
      return;
  }

  chaptersData.forEach((chapter, chapIndex) => {
      // Find lessons for this chapter
      const chapterLessons = lessonsData.filter(l => l.chapter_id == chapter.id);
      
      let currentModuleHtml = "";
      
      if(chapterLessons.length === 0) {
          currentModuleHtml = `<p class="text-[10px] text-gray-400 italic pl-5 py-2">Empty Chapter. Add lessons here.</p>`;
      } else {
          chapterLessons.forEach((lesson, index) => {
              // Get absolute index for selectLesson referencing lessonsData array
              let globalIndex = lessonsData.findIndex(g => g.id === lesson.id);

              let isCompleted = completedLessons.includes(lesson.id);
              let statusIcon = isCompleted 
                  ? `<span class="material-symbols-outlined text-emerald-500 text-sm bg-emerald-50 rounded-full p-1 border border-emerald-100 shadow-sm" title="Completed">check</span>` 
                  : `<div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0 shadow-inner"><span class="material-symbols-outlined text-sm">play_arrow</span></div>`;

              const item = `
                  <div class="group bg-white border border-gray-100 hover:border-blue-400 hover:shadow-md p-4 rounded-xl transition-all duration-300 cursor-pointer flex items-center justify-between mb-2"
                       onclick="selectLesson(${globalIndex})">
                      <div class="flex items-center gap-3 truncate">
                          ${statusIcon}
                          <h4 class="font-bold text-gray-800 text-[13px] truncate group-hover:text-blue-600 transition-colors">${escapeHTML(lesson.title)}</h4>
                      </div>
                      ${can_manage ? `
                      <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-all shrink-0">
                          <button onclick="event.stopPropagation(); editLesson(${globalIndex})" class="w-7 h-7 flex items-center justify-center bg-orange-50 text-orange-500 hover:bg-orange-600 hover:text-white rounded-lg transition-colors">
                              <span class="material-symbols-outlined text-[14px]">edit</span>
                          </button>
                          <button onclick="event.stopPropagation(); deleteLesson(${lesson.id})" class="w-7 h-7 flex items-center justify-center bg-red-50 text-red-500 hover:bg-red-600 hover:text-white rounded-lg transition-colors">
                              <span class="material-symbols-outlined text-[14px]">delete</span>
                          </button>
                      </div>` : ""}
                  </div>`;
              currentModuleHtml += item;
          });
      }

      let moduleWrap = `
          <div class="mb-5">
              <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest pl-2 mb-3 flex items-center gap-2">
                  <span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span>
                  ${escapeHTML(chapter.title)}
              </h3>
              <div class="space-y-1">
                  ${currentModuleHtml}
              </div>
          </div>
      `;
      list.append(moduleWrap);
  });
}


// Select & Play Video
function selectLesson(index) {
  const lesson = lessonsData[index];
  currentLessonId = lesson.id;

  $("#currentLessonTitle").text(lesson.title);
  
  if (completedLessons.includes(lesson.id)) {
      $("#markCompleteBtn").addClass("hidden");
      $("#completedBadge").removeClass("hidden");
  } else {
      $("#completedBadge").addClass("hidden");
      $("#markCompleteBtn").removeClass("hidden");
      $("#markCompleteBtn").off('click').on('click', function() {
          markCurrentLessonComplete();
      });
  }

  const safeContent = lesson.content
    ? escapeHTML(lesson.content).replace(/\n/g, "<br>")
    : "<em>No additional notes.</em>";
  $("#currentLessonContent").html(safeContent);

  const videoId = extractVideoID(lesson.video_url);
  if (videoId) {
    $("#noVideoState").addClass("hidden");
    $("#videoPlayerContainer").removeClass("hidden");
    
    if (player) {
      player.destroy();
      player = null;
    }

    $("#videoPlayerContainer").html(`<div id="videoPlayer" data-plyr-provider="youtube" data-plyr-embed-id="${videoId}"></div>`);

    player = new Plyr('#videoPlayer', {
      youtube: { 
        noCookie: true, 
        rel: 0, 
        showinfo: 0, 
        iv_load_policy: 3, 
        modestbranding: 1,
        controls: 0,
        disablekb: 1,
        playsinline: 1
      }
    });

    player.on('ready', () => {
      player.play().catch(() => {
        Toast.show(false, "To play the video, click the play button.");
      });
    });
    
    // Auto mark complete when video ends
    player.on('ended', () => {
        if (!completedLessons.includes(currentLessonId)) {
            markCurrentLessonComplete();
        }
    });
    
  } else {
    resetPlayer("Invalid Video Link");
  }
}

function markCurrentLessonComplete() {
    if(!currentLessonId) return;
    AJAX.post(
        api_url,
        { action: "mark_lesson_complete", lesson_id: currentLessonId, csrf_token: CSRF_TOKEN },
        function(res) {
            if(res.status) {
                if (!completedLessons.includes(currentLessonId)) {
                    completedLessons.push(currentLessonId);
                }
                $("#markCompleteBtn").addClass("hidden");
                $("#completedBadge").removeClass("hidden");
                renderLessons();
                Toast.show(true, "Lesson marked as finished!");
            } else {
                Toast.show(false, res.message);
            }
        }
    );
}

function extractVideoID(url) {
  if (!url) return null;
  const regExp =
    /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
  const match = url.match(regExp);
  return match && match[2].length === 11 ? match[2] : null;
}

function resetPlayer(msg) {
  if (player) {
    player.destroy();
    player = null;
  }
  $("#videoPlayerContainer").html(`<div id="videoPlayer"></div>`).addClass("hidden");
  $("#noVideoState").removeClass("hidden");
  $("#playerStatusText").text(msg);
}

function renderAccessDenied() {
  $("#lessonsList").html(`
        <div class="p-8 text-center text-red-500">
            <span class="material-symbols-outlined text-4xl mb-2">lock</span>
            <p class="font-bold uppercase tracking-widest text-xs">Access Denied</p>
            <p class="text-xs text-gray-500 mt-1">Please enroll in this course to view content.</p>
        </div>
    `);
  resetPlayer("No Access (Enroll First)");
}


function deleteLesson(id) {
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
      formData.append("action", "delete_lesson");
      formData.append("lesson_id", id);
      formData.append("course_id", COURSE_ID);
      formData.append("csrf_token", CSRF_TOKEN);

      // Isticmaalka AJAX Wrapper-ka
      AJAX.post(api_url, formData, function (response) {
        if (response.status) {
          Toast.show(true, "Si guul leh ayaa loo tirtiray!");
          fetchLessons();
          resetPlayer("Select a lesson to view");
        }else{
          Toast.show(false, response.message);
        }  
      });

  
    }
  });
}


// 7. Modal Controls
function openLessonModal(isEdit = false) {
  if (chaptersData.length === 0) {
      Toast.show(false, "Please create a Chapter first before adding a lesson.");
      openChapterModal();
      return;
  }
  if (!isEdit) {
    btnAction = "Insert";
    $("#lessonForm")[0].reset();
    $("#form_lesson_id").val("");
    $("#chapter_id").val($("#chapter_id option:first").val());
    $("#modalTitle").text("Add Lesson");
  }

  $("#lessonModal").removeClass("hidden").addClass("flex");
  setTimeout(() => {
    $("#lessonModal").removeClass("opacity-0").addClass("opacity-100");
  }, 10);
}


function editLesson(index) {
  const lesson = lessonsData[index];
  btnAction = "Update";

  $("#form_lesson_id").val(lesson.id);
  $("#chapter_id").val(lesson.chapter_id);
  $("#title").val(lesson.title);
  $("#video_url").val(lesson.video_url);
  $("#content").val(lesson.content);
  $("#modalTitle").text("Edit Lesson");

  openLessonModal(true);
}

// Chapter Modal Controls
function openChapterModal() {
  $("#chapterForm")[0].reset();
  $("#chapterModal").removeClass("hidden").addClass("flex");
  setTimeout(() => { $("#chapterModal").removeClass("opacity-0").addClass("opacity-100"); }, 10);
}
function closeChapterModal() {
  $("#chapterModal").removeClass("opacity-100").addClass("opacity-0");
  setTimeout(() => { $("#chapterModal").addClass("hidden").removeClass("flex"); }, 400);
}

function closeLessonModal() {
  $("#lessonModal").removeClass("opacity-100").addClass("opacity-0");
  setTimeout(() => {
    $("#lessonModal").addClass("hidden").removeClass("flex");
  }, 400);
}