<?php
require_once '../../../config/init.php';
require_once '../../../reusable/variables.php';

if (!is_logged_in() || $current_role !== 'Admin') {
    redirect('views/Auth/login.php');
    exit;
}

include '../../../reusable/header.php';
include '../../../reusable/sidebar.php';
include '../../../reusable/loader.php';
?>

<script>
    const USER_UPLOAD_URL = "<?php echo USER_UPLOAD_URL; ?>";
</script>

<div class="animate-fadeIn">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Admin Dashboard</h1>
            <p class="text-gray-500 font-medium">Gym Overview & Management</p>
        </div>
        <div class="flex gap-4">
            <button onclick="location.reload()"
                class="p-4 bg-white rounded-2xl shadow-sm border border-gray-100 text-gray-600 hover:text-blue-600 cursor-pointer transition-all">
                <span class="material-symbols-rounded">refresh</span>
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 hover:border-blue-500 transition-all group">
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:bg-blue-600 group-hover:text-white cursor-pointer transition-all">
                <span class="material-symbols-rounded">groups</span>
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Members</p>
            <h3 id="total-members" class="text-3xl font-black text-gray-900">0</h3>
        </div>

        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 hover:border-purple-300 transition-all group">
            <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 mb-6 group-hover:bg-purple-600 group-hover:text-white transition-all cursor-pointer">
                <span class="material-symbols-rounded">sports_kabaddi</span>
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Trainers</p>
            <h3 id="total-trainers" class="text-3xl font-black text-gray-900">0</h3>
        </div>

        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 hover:border-emerald-300 transition-all group">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-all cursor-pointer">
                <span class="material-symbols-rounded">fitness_center</span>
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Active Classes</p>
            <h3 id="total-classes" class="text-3xl font-black text-gray-900">0</h3>
        </div>

        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 hover:border-orange-300 transition-all group">
            <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600 mb-6 group-hover:bg-orange-600 group-hover:text-white transition-all cursor-pointer">
                <span class="material-symbols-rounded">payments</span>
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Revenue</p>
            <h3 id="total-revenue" class="text-3xl font-black text-gray-900">$0</h3>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
            <h3 class="text-xl font-black text-gray-900 mb-4">Monthly Membership Trend</h3>
            <div id="membershipTrendChart" class="w-full"></div>
        </div>
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
            <h3 class="text-xl font-black text-gray-900 mb-4">Member Distribution</h3>
            <div id="memberDistributionChart" class="w-full flex justify-center items-center"></div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black text-gray-900">Recent Members</h3>
                <a href="<?php echo BASE_URL; ?>views/users/members.php" class="text-xs font-bold text-blue-600 hover:underline">View All</a>
            </div>
            <div id="recent-members-list" class="space-y-4">
                <p class="text-sm text-gray-400 italic">Fetching latest activity...</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <h3 class="text-xl font-black text-gray-900 mb-6">Quick Links</h3>
            <div class="space-y-3">
                <a href="<?php echo BASE_URL; ?>views/users/members.php" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-all">
                    <span class="material-symbols-rounded text-blue-500">groups</span>
                    <span class="font-bold text-gray-700">Manage Members</span>
                </a>
                <a href="<?php echo BASE_URL; ?>views/users/trainers.php" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-all">
                    <span class="material-symbols-rounded text-purple-500">sports_kabaddi</span>
                    <span class="font-bold text-gray-700">Manage Trainers</span>
                </a>
                <a href="<?php echo BASE_URL; ?>views/classes/index.php" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-all">
                    <span class="material-symbols-rounded text-emerald-500">fitness_center</span>
                    <span class="font-bold text-gray-700">Gym Classes</span>
                </a>
                <a href="<?php echo BASE_URL; ?>views/payments/index.php" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-all">
                    <span class="material-symbols-rounded text-orange-500">payments</span>
                    <span class="font-bold text-gray-700">Payments</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>js/dashboard/admin.js"></script>
<?php include '../../../reusable/footer.php'; ?>