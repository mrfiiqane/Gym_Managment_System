const api_url = "../../api/Courses/my_courses.php";

$(document).ready(function () {
  if ($("#myCoursesGrid").length) {
    fetchMyCourses();
  }
});

function fetchMyCourses() {
  const formData = new FormData();
  formData.append("action", "Read_All_Courses");

  AJAX.post(api_url, formData, function (res) {
    let cards = "";
    if (!res.status || res.data.length === 0) {
      cards = `<div class="col-span-full py-20 text-center animate-fadeIn">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-50 rounded-full mb-4">
                            <span class="material-symbols-outlined text-4xl text-gray-300">school</span>
                        </div>
                        <p class="text-gray-400 font-bold italic">You haven't enrolled in any courses yet.</p>
                        <a href="browse.php" class="inline-block mt-6 px-8 py-4 bg-black text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-blue-600 transition-all">Explore Marketplace</a>
                     </div>`;
    } else {
      res.data.forEach((course) => {
        let finalImg = getImgPath(course.thumbnail);
        const progress = course.progress || 0;
        cards += `
                <div class="group bg-white max-w-7xl rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-blue-600/10 transition-all duration-500 overflow-hidden animate-fadeIn">
                <div class="h-44 w-full relative overflow-hidden spring-200 space-y-2">

                  <img src="${finalImg}" 
                       onerror="this.src='${COURSE_UPLOAD_URL}course_default.png'" 
                       class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                    <div class="px-4 py-2">
                        <div class="flex gap-2 items-center justify-between mb-4">
                            <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1 rounded-lg">Enrolled</span>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">${progress}% Complete</span>
                        </div>
                        

                        <h3 class="text-lg font-black text-gray-900 mb-2 group-hover:text-blue-600 transition-colors line-clamp-1">
                        ${course.title}
                    </h3>
                    
                    <p class="text-gray-500 text-[13px] font-medium mb-4 line-clamp-2 leading-relaxed opacity-80">
                        ${course.description}
                    </p>
                    <div class="glass-effect px-3 py-1.5 rounded-full flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-pulse"></span>
                            <span class="text-[10px] font-black text-gray-900 uppercase tracking-widest">${course.total_lessons || 0} Lessons</span>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="relative w-full h-2 bg-gray-100 rounded-full mb-8 overflow-hidden">
                            <div class="absolute top-0 left-0 h-full bg-blue-600 rounded-full transition-all duration-1000" style="width: ${progress}%"></div>
                        </div>
                        
                        <a href="lessons.php?id=${course.course_id}" class="w-full py-4 my-2 bg-black text-white text-[10px] font-black uppercase tracking-widest rounded-xl text-center inline-block group-hover:bg-blue-600 transition-all">
                            Continue Learning
                        </a>
                    </div>
                </div>`;
      });
    }
    $("#myCoursesGrid").html(cards);
  });
}



const getImgPath = (thumbnail) => {
  if (!thumbnail) return COURSE_UPLOAD_URL + "course_default.png";
  if (thumbnail.includes("http")) return thumbnail;

  // split('/').pop() waxay soo qabanaysaa magaca sawirka oo kaliya (e.g. course_123.jpg)
  // waxayna iska tuuraysaa folder kasta oo database-ka ku dhex jiray si uusan labo jeer u noqon
  return COURSE_UPLOAD_URL + thumbnail.split("/").pop();
};
