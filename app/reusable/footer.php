<footer class="mt-auto px-4 sm:px-8 py-6 w-full shrink-0">
    <div class="bg-white rounded-[20px] border border-slate-200/60 p-5 sm:p-6 flex flex-col md:flex-row justify-between items-center gap-4 shadow-[0_4px_30px_rgba(0,0,0,0.03)] transition-all hover:shadow-[0_8px_40px_rgba(0,0,0,0.05)]">
        
        <div class="text-slate-500 text-[13px] font-medium flex items-center gap-1.5 flex-wrap justify-center md:justify-start">
            &copy; <?php echo date('Y'); ?> 
            <span class="font-black text-blue-600 tracking-tight text-[15px] whitespace-nowrap ml-1 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                <?php echo defined('APP_NAME') ? APP_NAME : 'FitCore Gym'; ?>
            </span>. 
            <span class="hidden sm:inline ml-1">All digital rights reserved.</span>
        </div>
        
        <div class="flex items-center gap-4 text-[13px] font-semibold text-slate-500">
            <a href="#" class="hover:text-blue-600 hover:underline transition-all">Privacy</a>
            <span class="w-[4px] h-[4px] rounded-full bg-slate-300"></span>
            <a href="#" class="hover:text-blue-600 hover:underline transition-all">Terms</a>
            <span class="w-[4px] h-[4px] rounded-full bg-slate-300"></span>
            <div class="flex items-center gap-1.5 bg-slate-50 text-slate-600 px-3 py-1.5 rounded-full border border-slate-200 font-bold tracking-wide shadow-sm">
                <span class="material-symbols-rounded text-[16px] text-blue-500">verified</span>
                v<?php echo defined('APP_VERSION') ? APP_VERSION : '1.2.0'; ?>
            </div>
        </div>
        
    </div>
</footer>

<script src="<?php echo BASE_URL; ?>js/sidebar.js"></script>

