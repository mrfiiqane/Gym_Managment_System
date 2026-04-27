<?php
require_once '../config/init.php';
include '../reusable/header.php';
include '../reusable/sidebar.php';
?>

<div class="space-y-8 animate-fadeIn ">
    <button onclick="toggleTheme()"
        class="w-4.5 flex items-center gap-4 px-4 py-3 rounded-xl text-slate-500 hover:bg-secondary/10 hover:text-primary transition-all mt-6">
        <span class="material-symbols-outlined dark:hidden">dark_mode</span>
        <span class="material-symbols-outlined hidden dark:block">light_mode</span>
        <span class="nav-text font-md">Switch Theme</span>
    </button>
    <!-- Profile Header -->
    <div
        class="relative dark:bg-darkPanel overflow-hidden bg-gradient-to-br from-primary to-secondary rounded-[2rem] md:rounded-[3rem] p-6 md:p-10 lg:p-14 text-white shadow-2xl">
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6 md:gap-8">
            <div class="relative group">
                <div
                    class="w-28 h-28 md:w-36 md:h-36 rounded-[2rem] md:rounded-[2.5rem] bg-white/10 backdrop-blur-md border-4 border-white/30 overflow-hidden shadow-2xl">
                    <img id="profileImage" src="<?php echo BASE_URL; ?>uploads/<?php echo $_SESSION['image'] ?? 'default.png'; ?>"
                        class="w-full h-full object-cover" alt="Profile">
                </div>
                <label
                    class="absolute -bottom-2 -right-2 w-10 h-10 md:w-14 md:h-14 bg-white text-primary rounded-xl md:rounded-2xl flex items-center justify-center cursor-pointer shadow-2xl hover:scale-110 transition-all">
                    <span class="material-symbols-outlined text-lg md:text-xl">photo_camera</span>
                    <input type="file" id="profileImageUpload" class="hidden" accept="image/*">
                </label>
            </div>

            <div class="flex-1 text-center md:text-left">
                <span
                    class="inline-block px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-[0.2em] mb-4">User
                    Profile</span>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black tracking-tight mb-2">
                    <?= htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </h1>
                <p class="text-white/70 text-base md:text-lg font-medium mb-4"><?php echo $_SESSION['email']; ?></p>
                <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                    <span
                        class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-xl text-xs font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">badge</span>
                        ID: <?php echo $_SESSION['user_id']; ?>
                    </span>
                    <span
                        class="px-4 py-2 bg-green-400/30 backdrop-blur-md rounded-xl text-xs font-bold flex items-center gap-2">
                        <div class="w-2 h-2 bg-green-200 rounded-full animate-pulse"></div>
                        <?php echo $_SESSION['status'] ?? 'Active'; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Decorative blobs -->
        <div class="absolute -top-32 -right-32 w-80 h-80 bg-white/10 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-32 -left-32 w-[30rem] h-[30rem] bg-white/5 rounded-full blur-[120px]"></div>
    </div>

    <!-- Edit Profile Section -->
    <div class="bg-panel dark:bg-dark-panel rounded-[3rem] shadow-sm border border-primary/5 overflow-hidden">
        <div class="p-8 border-b border-primary/5">
            <h2 class="text-2xl font-black text-text-main dark:text-white tracking-tight">Edit Profile Information</h2>
            <p class="text-xs text-text-soft dark:text-white/60 font-bold uppercase tracking-widest mt-1">Update your personal details</p>
        </div>

        <div class="p-8">
            <form id="profile_form" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Full Name</label>
                    <input type="text" name="full_name" id="full_name"
                        value="<?php echo $_SESSION['full_name'] ?? ''; ?>" required
                        class="w-full p-4 bg-panel-soft dark:bg-dark-bg rounded-xl border border-primary/10 dark:border-dark-border outline-none focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Phone
                        Number</label>
                    <input type="text" name="phone" id="phone" value="<?php echo $_SESSION['phone'] ?? ''; ?>"
                        class="w-full p-4 bg-panel-soft dark:bg-dark-bg rounded-xl border border-primary/10 dark:border-dark-border outline-none focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Username</label>
                    <input type="text" name="username" id="username" value="<?php echo $_SESSION['username']; ?>"
                        required
                        class="w-full p-4 bg-panel-soft dark:bg-dark-bg rounded-xl border border-primary/10 dark:border-dark-border outline-none focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Email
                        Address</label>
                    <input type="email" name="email" id="email" value="<?php echo $_SESSION['email']; ?>" required
                        class="w-full p-4 bg-panel-soft dark:bg-dark-bg rounded-xl border border-primary/10 dark:border-dark-border outline-none focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">New
                        Password</label>
                    <input type="password" name="password" id="password" placeholder="Leave blank to keep current"
                        class="w-full p-4 bg-panel-soft dark:bg-dark-bg rounded-xl border border-primary/10 dark:border-dark-border outline-none focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Confirm
                        Password</label>
                    <input type="password" name="confirm_password" id="confirm_password"
                        placeholder="Confirm new password"
                        class="w-full p-4 bg-panel-soft dark:bg-dark-bg rounded-xl border border-primary/10 dark:border-dark-border outline-none focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm">
                </div>

                <div class="md:col-span-2 flex justify-end gap-4 pt-4">
                    <button type="reset"
                        class="px-8 py-4 text-text-soft font-bold text-sm hover:text-text-main transition-all">Reset</button>
                    <button type="submit"
                        class="px-12 py-4 bg-primary text-white font-black rounded-2xl hover:bg-primary/90 shadow-2xl shadow-primary/30 transition-all text-sm uppercase tracking-widest">Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Activity -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-panel dark:bg-dark-panel p-8 rounded-[3rem] border border-primary/5 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-4 bg-blue-50 dark:bg-blue-500/10 rounded-2xl text-blue-500">
                    <span class="material-symbols-outlined text-2xl">schedule</span>
                </div>
                <div>
                    <h3 class="text-xs font-black text-text-soft uppercase tracking-widest">Member Since</h3>
                    <p class="text-lg font-bold text-text-main dark:text-white">
                        <?php echo date('M Y', strtotime($_SESSION['created_at'] ?? 'now')); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-panel dark:bg-dark-panel p-8 rounded-[3rem] border border-primary/5 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-4 bg-green-50 dark:bg-green-500/10 rounded-2xl text-green-500">
                    <span class="material-symbols-outlined text-2xl">verified_user</span>
                </div>
                <div>
                    <h3 class="text-xs font-black text-text-soft uppercase tracking-widest">Account Status</h3>
                    <p class="text-lg font-bold text-green-600">Verified</p>
                </div>
            </div>
        </div>

        <div class="bg-panel dark:bg-dark-panel p-8 rounded-[3rem] border border-primary/5 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-4 bg-purple-50 dark:bg-purple-500/10 rounded-2xl text-purple-500">
                    <span class="material-symbols-outlined text-2xl">fingerprint</span>
                </div>
                <div>
                    <h3 class="text-xs font-black text-text-soft uppercase tracking-widest">User ID</h3>
                    <p class="text-lg font-bold text-text-main dark:text-white"><?php echo $_SESSION['user_id']; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>js/profile.js"></script>

<?php include '../reusable/footer.php'; ?>
