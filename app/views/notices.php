<?php
include 'header.php';
include 'sidebar.php';

$role = $_SESSION['role'] ?? "";
?>

<div class="space-y-8 animate-fadeIn">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-text-main dark:text-white tracking-tight">School Notices</h1>
            <p class="text-text-soft font-medium mt-1">Stay updated with the latest announcements</p>
        </div>
        
        <?php if ($role === 'Admin' || $role === 'Teacher'): ?>
        <button id="addNoticeBtn" class="px-6 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined">add_comment</span>
            Post New Notice
        </button>
        <?php endif; ?>
    </div>

    <!-- Notices Container -->
    <div id="noticesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Dynamic Content -->
        <div class="col-span-full py-20 flex flex-col items-center justify-center text-text-soft">
            <div class="w-20 h-20 bg-panel-soft dark:bg-dark-bg rounded-full flex items-center justify-center mb-4 border border-primary/10 dark:border-dark-border">
                <span class="material-symbols-outlined text-4xl">campaign</span>
            </div>
            <p class="font-medium">Loading announcements...</p>
        </div>
    </div>
</div>

<!-- Add Notice Modal -->
<?php if ($role === 'Admin' || $role === 'Teacher'): ?>
<div id="noticeModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm modal-overlay"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg p-6">
        <div class="bg-panel dark:bg-dark-panel rounded-[2.5rem] max-h-[85vh] overflow-y-auto shadow-2xl border border-primary/10 p-8 animate-slideUp">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-2xl font-black text-text-main dark:text-white tracking-tight">Post Notice</h2>
                    <p class="text-[10px] text-text-soft font-bold uppercase tracking-widest mt-1">Broadcast to all students</p>
                </div>
                <button class="close-modal w-10 h-10 rounded-full bg-panel-soft dark:bg-dark-bg flex items-center justify-center text-text-soft hover:text-red-500 transition-all border border-primary/10 dark:border-dark-border">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="noticeForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Title</label>
                        <input type="text" name="title" required
                            class="w-full p-4 bg-panel-soft dark:bg-dark-bg border border-primary/10 dark:border-dark-border rounded-xl outline-none focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm"
                            placeholder="e.g. Exam Schedule Update">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Category</label>
                        <select name="category" required class="w-full p-4 bg-panel-soft dark:bg-dark-bg border border-primary/10 dark:border-dark-border rounded-xl outline-none focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm appearance-none">
                            <option value="Announcement">Announcement</option>
                            <option value="Holiday">Holiday</option>
                            <option value="Event">Event</option>
                            <option value="Update">Update</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Expiration Date (Optional)</label>
                    <input type="date" name="expires_at"
                        class="w-full p-4 bg-panel-soft dark:bg-dark-bg border border-primary/10 dark:border-dark-border rounded-xl outline-none focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Content</label>
                    <textarea name="content" required rows="5"
                        class="w-full p-4 bg-panel-soft dark:bg-dark-bg border border-primary/10 dark:border-dark-border rounded-xl outline-none focus:ring-2 focus:ring-primary/20 transition-all font-medium text-sm"
                        placeholder="Write your announcement here..."></textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-primary text-white font-black rounded-2xl hover:bg-primary/90 shadow-2xl shadow-primary/30 transition-all text-sm uppercase tracking-widest">
                        Post Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    const currentUserId = <?php echo $_SESSION['user_id']; ?>;
    const currentRole = '<?php echo $role; ?>';
</script>
<script src="<?php echo BASE_URL; ?>js/notices.js"></script>
<?php include 'footer.php'; ?>

