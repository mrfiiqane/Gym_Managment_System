<?php
require_once '../../../config/init.php';

if (!is_logged_in()) {
    redirect('views/Auth/login.php');
    exit;
}

include '../../../reusable/header.php';
include '../../../reusable/sidebar.php';
include '../../../reusable/loader.php';
?>




<div class="animate-fadeIn p-6 lg:p-10 space-y-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Student Hub</h1>
            <p class="text-gray-500 font-medium">Welcome back, <span
                    class="text-blue-600 font-bold"><?= explode(' ', $_SESSION['full_name'])[0]; ?>!</span> 👋</p>
        </div>
        <div class="flex gap-3">
            <button onclick="location.href='../../Course/browse.php'"
                class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-2xl font-bold hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-sm">search</span>
                Browse
            </button>
            <button onclick="location.href='../../Course/my_course.php'"
                class="px-6 py-3 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-sm">play_circle</span>
                Continue Learning
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div
            class="group bg-gradient-to-br from-blue-600 to-indigo-800 p-8 rounded-[2.5rem] text-white shadow-2xl shadow-blue-200 relative overflow-hidden transition-transform hover:scale-[1.02]">
            <div class="relative z-10">
                <div
                    class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-xl mb-6 group-hover:rotate-12 transition-transform">
                    <span class="material-symbols-outlined text-3xl">local_library</span>
                </div>
                <h3 id="total-enrolled" class="text-6xl font-black mb-1">0</h3>
                <p class="text-xs font-bold uppercase tracking-[0.2em] opacity-70">Enrolled Courses</p>
            </div>
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        <div
            class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 rounded-[2rem] text-white shadow-xl shadow-blue-100 relative overflow-hidden">

            <div class="flex items-center justify-between mb-8 relative z-10">

                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">

                    <span class="material-symbols-outlined">auto_stories</span>

                </div>

                <span class="text-xs font-black uppercase tracking-widest opacity-60">Enrolled</span>

            </div>

            <h3 id="total-enrolled" class="text-5xl font-black mb-2 relative z-10">0</h3>

            <p class="font-bold opacity-80 uppercase text-[10px] tracking-widest relative z-10">Active Courses</p>

            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full"></div>

        </div>

        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden group">
            <div
                class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                <span class="material-symbols-outlined text-3xl">task_alt</span>
            </div>
            <h3 id="completed-lessons" class="text-5xl font-black text-gray-900 mb-1">0</h3>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Completed Lessons</p>
        </div>

        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden group">
            <div
                class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600 mb-6 group-hover:bg-orange-600 group-hover:text-white transition-all duration-500">
                <span class="material-symbols-outlined text-3xl">workspace_premium</span>
            </div>
            <h3 id="total-certificates" class="text-5xl font-black text-gray-900 mb-1">0</h3>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Certificates Earned</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <div class="lg:col-span-8 bg-white p-10 rounded-[3rem] shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-10">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Current Progress</h3>
                <a href="#" class="text-blue-600 font-bold text-sm hover:underline">View All</a>
            </div>

            <div id="enrolled-courses-list" class="space-y-8">
                <div class="animate-pulse flex space-x-4 p-4">
                    <div class="rounded-2xl bg-gray-100 h-16 w-16"></div>
                    <div class="flex-1 space-y-4 py-1">
                        <div class="h-4 bg-gray-100 rounded w-3/4"></div>
                        <div class="h-2 bg-gray-100 rounded"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-8">
            <div class="bg-gray-900 p-10 rounded-[3rem] shadow-2xl text-white relative overflow-hidden">
                <h3 class="text-xl font-black mb-8 flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-ping"></span>
                    Recent Activity
                </h3>
                <div id="recent-activity-list" class="space-y-6 relative z-10">
                    <p class="text-sm text-gray-500 italic">Tracking your updates...</p>
                </div>
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <span class="material-symbols-outlined text-9xl">history</span>
                </div>
            </div>

            <div class="bg-white p-10 rounded-[3rem] border border-gray-100 text-center shadow-sm">
                <p class="text-gray-900 font-black text-lg mb-4">Learning Consistency</p>
                <div id="studentProgressChart" class="w-full flex justify-center"></div>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo BASE_URL; ?>js/dashboard/student.js"></script>
<?php include '../../../reusable/footer.php'; ?>
