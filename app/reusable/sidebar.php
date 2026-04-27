<?php
require_once __DIR__ . '/../config/init.php';
?>



<style>
    .submenu {
        display: none;
        animation: slideDown 0.3s ease-out forwards;
    }
    .submenu.show {
        display: block;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #475569;
    }
    
    /* Navigation Link Hover Effect */
    .nav-btn {
        position: relative;
        overflow: hidden;
    }
    .nav-btn::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background: #3b82f6; /* blue-500 */
        transform: scaleY(0);
        transition: transform 0.2s ease;
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }
    .nav-btn:hover::before, .nav-btn.active::before, .bg-white\\/5::before {
        transform: scaleY(1);
    }
</style>

<!-- Mobile Overlay -->
<div id="mobile-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 hidden opacity-0 transition-opacity duration-300 lg:hidden"></div>

<!-- Sidebar Container -->
<aside id="sidebar" class="fixed left-0 top-0 h-full w-72 bg-slate-900 border-r border-slate-800 text-slate-300 z-40 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 flex flex-col shadow-2xl overflow-hidden">
    
    <!-- Logo Area -->
    <div class="h-20 flex items-center px-8 border-b border-white/5 shrink-0 bg-slate-900/50 backdrop-blur z-10 sticky top-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                <span class="material-symbols-rounded text-white font-black text-xl leading-none">fitness_center</span>
            </div>
            <h2 class="text-xl font-bold text-white tracking-tight">FitCore <span class="text-blue-400">Gym</span></h2>
        </div>
        <button onclick="toggleSidebar()" class="lg:hidden ml-auto text-slate-400 hover:text-white transition-colors bg-white/5 p-1.5 rounded-lg active:scale-95">
            <span class="material-symbols-rounded text-xl">close</span>
        </button>
    </div>

    <!-- Scrollable Content -->
    <div class="flex-grow overflow-y-auto custom-scrollbar p-5 flex flex-col gap-6">
        
        <!-- User Profile (Redesigned) -->
        <div class="flex flex-col items-center gap-3 bg-slate-800/40 p-5 rounded-[20px] border border-white/5 shadow-inner">
            <div class="relative group">
                <div class="w-[72px] h-[72px] rounded-full p-1 bg-gradient-to-tr from-blue-500 to-purple-500 shadow-xl transition-transform group-hover:scale-105 duration-300">
                    <img id="sidebar_avatar" src="<?php echo defined('USER_UPLOAD_URL') ? USER_UPLOAD_URL . $user_image : BASE_URL . 'uploads/User_profile/' . $user_image; ?>" alt="User" class="w-full h-full rounded-full object-cover border-[3px] border-slate-900">
                </div>
                <label class="absolute -bottom-1 -right-1 w-[26px] h-[26px] bg-blue-600 hover:bg-blue-500 rounded-full flex items-center justify-center cursor-pointer shadow-lg shadow-blue-500/40 transition-transform border-[2px] border-slate-900">
                    <span class="material-symbols-rounded text-white text-[12px]">photo_camera</span>
                    <input type="file" name="image" id="sidebar_user_image" class="hidden" accept="image/*">
                </label>
            </div>
            <div class="text-center">
                <h3 class="font-bold text-white text-[14px] truncate max-w-[200px]"><?php echo $full_name; ?></h3>
                <div class="flex items-center justify-center gap-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.15em] text-emerald-400/90">
                        <?php echo htmlspecialchars($current_role); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="space-y-1 w-full" id="user_menu">
            <div class="px-3 mb-3 flex items-center justify-between">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Menu</p>
            </div>
            
            <!-- Dynamic elements loaded via sidebar.js -->
            <div class="flex flex-col gap-3 py-4 animate-pulse px-4">
                <div class="h-10 bg-white/5 rounded-xl"></div>
                <div class="h-10 bg-white/5 rounded-xl"></div>
                <div class="h-10 bg-white/5 rounded-xl"></div>
            </div>
        </nav>
    </div>

    <!-- Sign Out Action -->
    <div class="p-5 border-t border-white/5 shrink-0 bg-slate-900/50">
        <a href="<?php echo BASE_URL; ?>views/Auth/logout.php" class="flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-slate-800/80 hover:bg-rose-500/90 text-slate-300 hover:text-white transition-all font-semibold text-[13px] group shadow-sm active:scale-95">
            <span class="material-symbols-rounded text-[18px] text-slate-400 group-hover:text-white transition-colors">logout</span>
            <span>Sign Out</span>
        </a>
    </div>
</aside>

<!-- Main Wrapper -->
<div id="main-content" class="lg:ml-72 min-h-screen transition-all duration-300 ease-in-out flex flex-col w-full lg:w-[calc(100%-18rem)] relative">
    
    <!-- Top Glass Header -->
    <header class="h-20 bg-white/70 backdrop-blur-xl border-b border-slate-200/60 flex items-center justify-between px-4 sm:px-8 sticky top-0 z-30 shrink-0 shadow-[0_4px_30px_rgba(0,0,0,0.03)]">
        
        <!-- Navbar Left (Hamburger & Breadcrumb) -->
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="text-slate-500 hover:text-blue-600 hover:bg-blue-50/80 p-2 rounded-xl transition-all shrink-0 active:scale-95">
                <span class="material-symbols-rounded text-[24px]">menu</span>
            </button>
            <div class="hidden sm:block">
                <h1 class="text-[16px] font-bold text-slate-800 tracking-tight">Welcome back, <?php echo isset($full_name) ? explode(' ', $full_name)[0] : 'User'; ?>! 👋</h1>
                <p class="text-[12px] font-medium text-slate-500 mt-0.5">Manage your fitness activities smoothly.</p>
            </div>
        </div>

        <!-- Navbar Right (Date, Actions) -->
        <div class="flex items-center gap-3 sm:gap-5 shrink-0">
            <!-- Search Mockup -->
            <button class="w-[38px] h-[38px] flex items-center justify-center text-slate-500 hover:text-blue-600 bg-slate-100/80 hover:bg-blue-50 rounded-full transition-all hidden sm:flex active:scale-95">
                <span class="material-symbols-rounded text-[20px]">search</span>
            </button>
            
            <!-- Notification Mockup -->
            <button class="relative w-[38px] h-[38px] flex items-center justify-center text-slate-500 hover:text-blue-600 bg-slate-100/80 hover:bg-blue-50 rounded-full transition-all active:scale-95">
                <span class="material-symbols-rounded text-[20px]">notifications</span>
                <span class="absolute top-[8px] right-[10px] w-2 h-2 bg-rose-500 rounded-full border border-white animate-pulse"></span>
            </button>

            <!-- Date Display -->
            <div class="hidden md:flex items-center gap-2 bg-slate-50 border border-slate-200/80 px-3.5 py-1.5 rounded-full shadow-sm">
                <span class="material-symbols-rounded text-slate-400 text-[18px]">calendar_today</span>
                <p class="text-[12px] font-semibold text-slate-600 tracking-wide"><?php echo date('F j, Y'); ?></p>
            </div>
        </div>
    </header>

    <!-- Main Content Area Wrapper -->
    <main class="flex-grow p-4 sm:p-8 animate-fadeIn w-full max-w-8xl mx-auto">