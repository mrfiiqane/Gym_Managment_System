<?php
require_once '../../config/init.php';

if (!is_logged_in()) {
    redirect('views/auth/login.php');
}

if (!has_role('Admin')) {
    redirect('views/dashboard/index.php');
}


include '../../reusable/header.php';
include '../../reusable/loader.php';
include '../../reusable/sidebar.php';
?>

<link href="<?php echo BASE_URL; ?>src/output.css" rel="stylesheet">

<script>
    const USER_UPLOAD_URL = "<?php echo USER_UPLOAD_URL; ?>";
</script>


<div class="space-y-8 animate-fadeIn  max-w-7xl md:w-full px-2 py-4 md:p-4">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">User Management</h1>
            <p class="text-gray-500 font-medium">Manage students, instructors, and admin accounts.</p>
        </div>
        <button onclick="openModal()"
            class="px-8 py-4 bg-blue-600 text-white font-black rounded-2xl shadow-lg shadow-blue-600/30 hover:bg-blue-700 transition-all cursor-pointer flex items-center gap-2 text-sm uppercase tracking-widest">
            <span class="material-symbols-outlined">add</span>
            Add New User
        </button>

    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-blue-50  rounded-2xl text-blue-500">
                    <span class="material-symbols-outlined">group</span>
                </div>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Total Users</p>
            <h3 class="text-3xl font-black text-gray-900" id="total_users_count">0</h3>
        </div>

        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-green-50  rounded-2xl text-green-500">
                    <span class="material-symbols-outlined">school</span>
                </div>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Students</p>
            <h3 class="text-3xl font-black text-blue-600" id="student_count">0</h3>
        </div>

        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-purple-50  rounded-2xl text-purple-500">
                    <span class="material-symbols-outlined">person_celebrate</span>
                </div>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Teachers</p>

            <h3 class="text-3xl font-black text-gray-900" id="teacher_count">0</h3>
        </div>

        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-orange-50 dark:bg-orange-500/10 rounded-2xl text-orange-500">
                    <span class="material-symbols-outlined">pending</span>
                </div>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Pending</p>

            <h3 class="text-3xl font-black text-gray-900" id="pending_count">0</h3>
        </div>

    </div>

    <!-- Users Table -->
    <div
        class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100  overflow-hidden transition-all duration-300">
        <div class="p-6 border-b border-slate-100  flex flex-col md:flex-row gap-4 justify-between items-center /50 ">
            <div class="flex flex-wrap gap-2">
                <button
                    class="filter-btn active px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest cursor-pointer bg-blue-600 text-white shadow-lg shadow-blue-600/20"
                    data-status="all">All</button>
                <!-- <button
                    class="filter-btn px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest text-slate-400 hover:bg-slate-100 dark:hover:bg-white/10"
                    data-status="Student">Students</button>
                <button
                    class="filter-btn px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest text-slate-400 hover:bg-slate-100 dark:hover:bg-white/10"
                    data-status="Teacher">Teachers</button> -->
                <button
                    class="filter-btn px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest text-slate-400 hover:bg-slate-100 cursor-pointer dark:hover:bg-white/10"
                    data-status="pending">Pending</button>
            </div>

            <div class="relative w-full md:w-72 group">
                <span
                    class="material-symbols-outlined absolute left-4 top-3 text-slate-400 group-focus-within:text-blue-600 transition-colors">search</span>
                <input type="text" id="pass_search" placeholder="Search users..."
                    class="w-full pl-12 pr-4 py-3 bg-white  border border-slate-200  rounded-2xl outline-none focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600/30 transition-all text-sm font-semibold text-slate-700 ">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="userTable" class="w-full text-left border-collapse">
                <thead>

                </thead>
                <tbody
                    class="text-sm font-semibold text-slate-600 dark:text-slate-300 divide-y divide-slate-50 dark:divide-white/5">
                </tbody>
            </table>
        </div>
    </div>


    <!-- Add/Edit User Modal -->
    <div id="userModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center backdrop-blur-md p-4 transition-all">
        <div class="bg-white  p-8 rounded-[2.5rem] w-full max-w-[550px] relative shadow-2xl border border-white/20">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-black text-slate-800  tracking-tight" id="modalTitle">User
                    Details
                </h2>
                <button onclick="closeModal()"
                    class="w-10 h-10 rounded-full bg-slate-100  flex items-center justify-center text-slate-500 cursor-pointer hover:text-red-800 transition-all">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>



            <div id="success"
                class="hidden p-4 mb-4 text-sm text-green-800 rounded-2xl bg-green-50 border border-green-100"
                role="alert"></div>
            <div id="error" class="hidden p-4 mb-4 text-sm text-red-800 rounded-2xl bg-red-50 border border-red-100"
                role="alert"></div>

            <form id="userForm" class="space-y-4" enctype="multipart/form-data">


                <input type="hidden" name="id" id="update_id">

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Full
                        Name</label>
                    <input type="text" id="full_name" name="full_name"
                        class="w-full p-3   border border-slate-200  rounded-xl outline-none focus:ring-2 focus:ring-blue-600/20 text-slate-800  font-bold text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Username</label>
                        <input type="text" id="username" name="username"
                            class="w-full p-3   border border-slate-200  rounded-xl outline-none focus:ring-2 focus:ring-blue-600/20 text-slate-800  font-bold text-sm">
                    </div>
                    <div class="space-y-1">
                        <label
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Phone</label>
                        <input type="text" id="phone" name="phone"
                            class="w-full p-3   border border-slate-200  rounded-xl outline-none focus:ring-2 focus:ring-blue-600/20 text-slate-800  font-bold text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email</label>
                        <input type="email" id="email" name="email"
                            class="w-full p-3   border border-slate-200  rounded-xl outline-none focus:ring-2 focus:ring-blue-600/20 text-slate-800  font-bold text-sm">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Role</label>
                        <select id="role_id" name="role_id"
                            class="w-full p-3   border border-slate-200  rounded-xl outline-none focus:ring-2 focus:ring-blue-600/20 text-slate-800  font-bold text-sm appearance-none cursor-pointer">
                            <option value="0">Select Role</option>
                            <option value="3">Student</option>
                            <option value="2">Teacher</option>
                            <!-- <option value="1">Admin</option> -->
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-6 py-2">
                    <div class="relative group">
                        <div
                            class="w-20 h-20 rounded-full border-4 border-slate-50  shadow-lg overflow-hidden bg-slate-100">
                            <img id="show" src="<?php echo BASE_URL; ?>uploads/User_profile/default.png"
                                class="w-full h-full object-cover">
                        </div>
                        <label
                            class="absolute -bottom-1 -right-1 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:bg-blue-700 transition-all border-2 border-white ">
                            <span class="material-symbols-outlined text-white text-xs">photo_camera</span>
                            <input type="file" name="image" id="image" class="hidden" accept="image/*">
                        </label>
                    </div>
                    <div class="flex-1">
                        <label
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Password</label>
                        <input type="password" id="password" name="password" placeholder="Default: 123456"
                            class="w-full p-3   border border-slate-200  rounded-xl outline-none focus:ring-2 focus:ring-blue-600/20 text-slate-800  font-bold text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 ">
                    <button type="button" onclick="closeModal()"
                        class="px-6 py-3 text-slate-500 font-bold text-sm hover:bg-slate-100 rounded-xl transition-all cursor-pointer">Cancel</button>
                    <button type="submit" id="btnSave"
                        class="flex-1 cursor-pointer py-4 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 shadow-xl shadow-blue-600/20 transition-all text-xs uppercase tracking-[0.2em]">
                        Save User
                    </button>
                </div>
            </form>
        </div>

        <script>
            function openModal() {
                $("#userForm")[0].reset();
                btnAction = "Insert";
                $("#update_id").val("");
                $("#modalTitle").text("Add New User");
                $("#btnSave").text("Save User");
                $("#userModal").removeClass("hidden").addClass("flex");
            }

            function closeModal() {
                $("#userModal").removeClass("flex").addClass("hidden");
            }
        </script>



        <script src="<?php echo BASE_URL; ?>js/users/users.js"></script>
        <?php include '../../reusable/footer.php'; ?>