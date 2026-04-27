<?php
include 'dashboard.php';
// include 'header.php';
// include 'sidebar.php';
?>

<div class="bg-panel dark:bg-dark-panel rounded-[2.5rem] shadow-sm border border-primary/5 overflow-hidden animate-fadeIn">
    <div class="p-8 border-b border-primary/5 flex flex-col md:flex-row justify-between items-center gap-4">
    <div class="p-8 border-b border-primary/5 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-text-main dark:text-white tracking-tight">Notice Management</h2>
            <p class="text-xs text-text-soft font-bold uppercase tracking-widest mt-1">Post Announcements & Updates</p>
        </div>
        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="relative flex-1 md:w-64">
                <span class="material-symbols-outlined absolute left-4 top-3 text-text-soft">search</span>
                <input type="text" id="noticeSearch" placeholder="Search notices..."
                    class="w-full pl-12 pr-4 py-3 bg-panel-soft dark:bg-dark-bg border border-primary/10 dark:border-dark-border rounded-2xl focus:ring-2 focus:ring-primary/20 outline-none transition-all font-medium">
            </div>
            <button onclick="openModal()" class="bg-primary text-white h-[52px] px-8 rounded-2xl font-bold hover:bg-primary/90 transition shadow-xl shadow-primary/20 whitespace-nowrap">+ Post Notice</button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table id="notice_table" class="w-full text-left">
            <thead class="bg-panel-soft dark:bg-dark-bg">
            </thead>
            <tbody class="divide-y divide-primary/5">
            </tbody>
        </table>
    </div>

    <div class="p-8 border-t border-primary/5 flex items-center justify-between">
        <p id="paginationInfo" class="text-xs font-bold text-text-soft uppercase tracking-widest">Showing 0 of 0 notices</p>
        <div class="flex items-center gap-2">
            <button id="prevPage" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-panel-soft dark:hover:bg-dark-bg disabled:opacity-30 transition-all border border-primary/5">
                <span class="material-symbols-outlined text-sm">chevron_left</span>
            </button>
            <button id="nextPage" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-panel-soft dark:hover:bg-dark-bg disabled:opacity-30 transition-all border border-primary/5">
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            </button>
        </div>
    </div>
</div>

<!-- Notice Modal -->
<div id="modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-panel dark:bg-dark-panel p-10 rounded-[3rem] w-full max-w-[600px] max-h-[85vh] overflow-y-auto relative shadow-2xl m-4 border border-primary/10">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h3 id="modalTitle" class="text-3xl font-black text-text-main dark:text-white tracking-tight">Post New Notice</h3>
                <p class="text-[10px] text-text-soft font-bold uppercase tracking-[0.2em] mt-2">Institutional Announcements</p>
            </div>
            <button onclick="closeModal()" class="w-10 h-10 flex items-center justify-center hover:bg-panel-soft dark:hover:bg-dark-bg rounded-full text-text-soft transition-all border border-primary/5">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="notice_form" class="space-y-6">
            <input type="hidden" name="id" id="update_id">

            <div class="space-y-2">
                <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Notice Title</label>
                <input type="text" name="title" id="title" placeholder="e.g., Holiday Announcement" required
                    class="w-full p-4 bg-panel-soft dark:bg-dark-bg border border-primary/10 dark:border-dark-border rounded-xl outline-none focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Message</label>
                <textarea name="message" id="message" rows="5" placeholder="Write your announcement here..." required
                    class="w-full p-4 bg-panel-soft dark:bg-dark-bg border border-primary/10 dark:border-dark-border rounded-xl outline-none focus:ring-2 focus:ring-primary/20 transition-all font-medium text-sm resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Notice Type</label>
                    <select name="type" id="type" class="w-full p-4 bg-panel-soft dark:bg-dark-bg border border-primary/10 dark:border-dark-border rounded-xl outline-none focus:ring-2 focus:ring-primary/20 transition-all font-black text-sm appearance-none cursor-pointer">
                        <option value="Announcement">Announcement</option>
                        <option value="Holiday">Holiday</option>
                        <option value="Update">Update</option>
                        <option value="Event">Event</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Expires On (Optional)</label>
                    <input type="date" name="expires_at" id="expires_at"
                        class="w-full p-4 bg-panel-soft dark:bg-dark-bg border border-primary/10 dark:border-dark-border rounded-xl outline-none focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm">
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-8">
                <button type="button" onclick="closeModal()" class="px-8 py-4 text-text-soft font-bold text-sm hover:text-text-main transition-all">Cancel</button>
                <button type="submit" id="btnSave" class="px-12 py-4 bg-primary text-white font-black rounded-2xl hover:bg-primary/90 shadow-2xl shadow-primary/30 transition-all text-sm uppercase tracking-widest">Post Notice</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        $("#notice_form")[0].reset();
        $("#update_id").val("");
        btnAction = "create_notice";
        $("#modalTitle").text("Post New Notice");
        $("#btnSave").text("Post Notice");
        $("#modal").removeClass("hidden").addClass("flex");
    }

    function closeModal() {
        $("#modal").removeClass("flex").addClass("hidden");
    }
</script>

<script src="<?php echo BASE_URL; ?>js/notice.js"></script>

<?php include 'footer.php'; ?>

