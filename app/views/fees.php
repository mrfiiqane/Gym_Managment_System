<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<main id="main-content" class="content-transition ml-64 min-h-screen flex flex-col bg-panel-soft dark:bg-dark-bg">
    <header class="h-20 flex items-center justify-between px-8 bg-panel/50 dark:bg-dark-panel/50 backdrop-blur-md sticky top-0 z-40 border-b border-primary/5">
        <div class="flex items-center gap-4">
            <h2 class="font-black text-2xl text-text-main dark:text-white tracking-tight">Fee Management</h2>
        </div>
        <?php if ($_SESSION['role'] === 'Admin'): ?>
        <div class="flex items-center gap-4">
             <button onclick="syncFees()" class="px-6 py-2 bg-secondary text-primary rounded-xl font-bold text-sm shadow-sm hover:scale-[1.02] transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">sync</span>
                Sync Fees
             </button>
             <button onclick="openStructureModal()" class="px-6 py-2 bg-primary text-white rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">settings</span>
                Fee Structure
             </button>
        </div>
        <?php endif; ?>
    </header>

    <div class="max-w-[1400px] mx-auto p-6 md:p-8 space-y-8">
        <!-- Tabs -->
        <div class="flex gap-4 border-b border-primary/10 mb-6">
            <button onclick="switchTab('fees')" id="tab-fees" class="px-6 py-3 font-bold text-sm border-b-2 border-primary text-primary transition-all">Student Fees</button>
            <?php if ($_SESSION['role'] === 'Admin'): ?>
            <button onclick="switchTab('payments')" id="tab-payments" class="px-6 py-3 font-bold text-sm border-b-2 border-transparent text-text-soft hover:text-text-main dark:hover:text-white transition-all">Recent Payments</button>
            <?php endif; ?>
        </div>

        <!-- Student Fees Table -->
        <div id="section-fees" class="bg-panel dark:bg-dark-panel rounded-[2.5rem] shadow-sm border border-primary/5 overflow-hidden transition-all">
            <div class="p-8 border-b border-primary/5 flex justify-between items-center">
                <h3 class="font-black text-text-main dark:text-white">Fee Status List</h3>
                <div class="flex items-center gap-4">
                    <div class="relative w-80">
                        <span class="material-symbols-outlined absolute left-4 top-3 text-text-soft">search</span>
                        <input type="text" id="feeSearch" placeholder="Search student..." 
                            class="w-full pl-12 pr-4 py-2.5 bg-panel-soft dark:bg-dark-bg rounded-xl border border-primary/10 dark:border-dark-border outline-none focus:ring-2 focus:ring-primary/20 font-bold text-sm">
                    </div>
                    <a href="<?php echo BASE_URL; ?>api/export_csv.php?type=fees" class="p-2.5 bg-panel-soft dark:bg-dark-bg rounded-xl text-text-soft hover:text-primary transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined">download</span>
                        <span class="text-xs font-bold uppercase tracking-widest hidden md:inline">CSV</span>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-primary/5">
                            <th class="p-8 text-[11px] font-black text-text-soft uppercase tracking-widest">Student</th>
                            <th class="p-8 text-[11px] font-black text-text-soft uppercase tracking-widest">Class</th>
                            <th class="p-8 text-[11px] font-black text-text-soft uppercase tracking-widest">Total</th>
                            <th class="p-8 text-[11px] font-black text-text-soft uppercase tracking-widest">Paid</th>
                            <th class="p-8 text-[11px] font-black text-text-soft uppercase tracking-widest text-red-500">Balance</th>
                            <th class="p-8 text-[11px] font-black text-text-soft uppercase tracking-widest">Status</th>
                            <?php if ($_SESSION['role'] === 'Admin'): ?>
                            <th class="p-8 text-[11px] font-black text-text-soft uppercase tracking-widest text-right">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="feesBody" class="divide-y divide-primary/5">
                        <!-- Loaded via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modals & Templates -->
<?php if ($_SESSION['role'] === 'Admin'): ?>
<!-- Fee Structure Modal -->
<div id="structureModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('structureModal')"></div>
    <div class="bg-panel dark:bg-dark-panel w-full max-w-2xl rounded-[2.5rem] max-h-[90vh] overflow-y-auto relative z-10 shadow-2xl border border-primary/10">
        <div class="p-8 bg-primary-dark dark:bg-dark-bg text-white flex justify-between items-center">
            <div>
                <h3 class="text-2xl font-black tracking-tight">Fee Structure</h3>
                <p class="text-white/70 text-xs font-bold uppercase tracking-widest mt-1">Set base fees per class</p>
            </div>
            <button onclick="closeModal('structureModal')" class="text-white/50 hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <div class="p-8 space-y-8">
            <!-- Add/Edit Form -->
            <form id="structureForm" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end bg-panel-soft dark:bg-dark-bg p-6 rounded-3xl">
                <input type="hidden" id="structure_id">
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-black text-text-soft uppercase tracking-widest mb-2 px-1">Class</label>
                    <select id="struct_class_id" required class="w-full bg-panel dark:bg-dark-panel px-4 py-3 rounded-xl border border-primary/10 dark:border-dark-border outline-none focus:ring-2 focus:ring-primary/20 font-bold text-sm appearance-none">
                        <option value="" disabled selected>Select</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-text-soft uppercase tracking-widest mb-2 px-1">Amount ($)</label>
                    <input type="number" id="struct_amount" required step="0.01" class="w-full bg-panel dark:bg-dark-panel px-4 py-3 rounded-xl border border-primary/10 dark:border-dark-border outline-none focus:ring-2 focus:ring-primary/20 font-bold text-sm">
                </div>
                <button type="submit" class="h-[46px] bg-primary text-white font-black rounded-xl shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all text-xs uppercase tracking-widest">
                    Save
                </button>
            </form>

            <!-- List -->
            <div class="max-h-60 overflow-y-auto rounded-2xl border border-primary/5">
                <table class="w-full text-left text-xs">
                    <thead class="bg-panel-soft dark:bg-dark-bg sticky top-0">
                        <tr>
                            <th class="p-4 font-black text-text-soft uppercase">Class</th>
                            <th class="p-4 font-black text-text-soft uppercase">Amount</th>
                            <th class="p-4 font-black text-text-soft uppercase text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="structureList" class="divide-y divide-primary/5">
                        <!-- Loaded via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?php echo BASE_URL; ?>js/fees.js"></script>
<?php include 'footer.php'; ?>

