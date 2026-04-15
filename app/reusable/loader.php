<!-- Loader Framework -->
<div id="global-loader" class="fixed inset-0 z-[9999] bg-slate-900/40 backdrop-blur-[4px] hidden transition-all duration-300">
    
    <!-- Style 1: Modern Spin (Default) -->
    <div id="loader-style-1" class="loader-content hidden flex-col items-center justify-center w-full h-full">
        <div class="relative flex justify-center items-center">
            <div class="absolute w-16 h-16 border-4 border-blue-200 border-dashed rounded-full animate-spin"></div>
            <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin shadow-lg shadow-blue-500/50"></div>
        </div>
        <p class="mt-6 text-[13px] font-black text-blue-900 tracking-[0.25em] uppercase drop-shadow-sm">Loading</p>
    </div>

    <!-- Style 2: Bouncing Gradient Dots -->
    <div id="loader-style-2" class="loader-content hidden flex-col items-center justify-center w-full h-full">
        <div class="flex space-x-3 bg-white/90 p-5 rounded-2xl shadow-2xl shadow-purple-500/20 border border-purple-50">
            <div class="w-4 h-4 bg-gradient-to-tr from-blue-500 to-cyan-400 rounded-full animate-bounce" style="animation-delay: -0.3s"></div>
            <div class="w-4 h-4 bg-gradient-to-tr from-purple-500 to-indigo-400 rounded-full animate-bounce" style="animation-delay: -0.15s"></div>
            <div class="w-4 h-4 bg-gradient-to-tr from-pink-500 to-rose-400 rounded-full animate-bounce"></div>
        </div>
        <p class="mt-5 text-sm font-bold text-gray-800 tracking-[0.15em] uppercase">Processing...</p>
    </div>

    <!-- Style 3: Gym Theme Pulse -->
    <div id="loader-style-3" class="loader-content hidden flex-col items-center justify-center w-full h-full">
        <div class="relative flex items-center justify-center">
            <div class="absolute w-24 h-24 bg-emerald-500 rounded-full animate-ping opacity-30"></div>
            <div class="relative flex w-16 h-16 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-2xl rotate-45 items-center justify-center shadow-xl shadow-emerald-500/40">
               <span class="material-symbols-outlined text-white text-3xl -rotate-45 animate-pulse">fitness_center</span>
            </div>
        </div>
        <p class="mt-8 text-sm font-bold text-emerald-800 tracking-widest uppercase">Fetching Data...</p>
    </div>
</div>

<script>
    /**
     * Wac Loaderka cusub adiga oo parameter siinaya si aad ushaqaysiiso design kala duwan
     * showLoader(1) -> Spinning Circle (Default)
     * showLoader(2) -> Bouncing Dots
     * showLoader(3) -> Gym Dumbbell Pulse
     */
    function showLoader(style = 1) {
        // Qari dhamaan
        $('.loader-content').addClass('hidden').removeClass('flex');
        
        // Daar midka la doortay
        const loader = $(`#loader-style-${style}`);
        if(loader.length) {
            loader.removeClass('hidden').addClass('flex');
        } else {
            $('#loader-style-1').removeClass('hidden').addClass('flex'); // Fallback
        }
        
        $('#global-loader').removeClass('hidden');
    }

    function hideLoader() {
        $('#global-loader').addClass('hidden');
    }
</script>
