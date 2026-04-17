<?php
require_once '../../../config/init.php';

if (!is_logged_in() || $current_role !== 'Trainer') {
    redirect('views/Auth/login.php');
    exit;
}

include '../../../reusable/header.php';
include '../../../reusable/sidebar.php';
include '../../../reusable/loader.php';
?>

<div class="animate-fadeIn">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Trainer Dashboard</h1>
            <p class="text-gray-500 font-medium">Welcome back, <span class="text-blue-600 font-bold"><?= explode(' ', $full_name)[0]; ?>!</span> 💪</p>
        </div>
        <a href="<?php echo BASE_URL; ?>views/classes/my_classes.php"
            class="px-6 py-3 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center gap-2 w-fit cursor-pointer">
            <span class="material-symbols-rounded text-sm">add_circle</span>
            Add New Class
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="group bg-gradient-to-br from-blue-600 to-indigo-700 p-8 rounded-[2.5rem] text-white shadow-2xl shadow-blue-200 relative overflow-hidden transition-transform hover:scale-[1.02]">
            <div class="relative z-10">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-xl mb-6">
                    <span class="material-symbols-rounded text-3xl">sports_gymnastics</span>
                </div>
                <h3 id="total-classes" class="text-6xl font-black mb-1">0</h3>
                <p class="text-xs font-bold uppercase tracking-[0.2em] opacity-70">My Classes</p>
            </div>
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col justify-center group">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                <span class="material-symbols-rounded text-3xl">groups</span>
            </div>
            <h3 id="total-members" class="text-5xl font-black text-gray-900 mb-1">0</h3>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">My Members</p>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col justify-center group">
            <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600 mb-6 group-hover:bg-orange-600 group-hover:text-white transition-all duration-500">
                <span class="material-symbols-rounded text-3xl">schedule</span>
            </div>
            <h3 id="upcoming-sessions" class="text-5xl font-black text-gray-900 mb-1">0</h3>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Upcoming Sessions</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black text-gray-900">My Classes</h3>
                <a href="<?php echo BASE_URL; ?>views/classes/my_classes.php" class="text-xs font-bold text-blue-600 hover:underline">View All</a>
            </div>
            <div id="trainer-classes-list" class="space-y-4">
                <p class="text-sm text-gray-400 italic">Loading classes...</p>
            </div>
        </div>

        <div class="bg-gray-900 p-8 rounded-[2rem] shadow-2xl text-white relative overflow-hidden">
            <h3 class="text-xl font-black mb-6 flex items-center gap-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full animate-ping"></span>
                Recent Activity
            </h3>
            <div id="trainer-activity-list" class="space-y-4 relative z-10">
                <p class="text-sm text-gray-500 italic">Loading activity...</p>
            </div>
            <div class="absolute top-0 right-0 p-4 opacity-5">
                <span class="material-symbols-rounded text-9xl">fitness_center</span>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>js/dashboard/trainer.js"></script>
<?php include '../../../reusable/footer.php'; ?>
