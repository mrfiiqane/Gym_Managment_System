<?php
require_once '../config/init.php';
// Use reusable components as requested
include '../reusable/header.php';
include '../reusable/sidebar.php';
include '../reusable/loader.php';
?>

<div class="space-y-4 animate-fadeIn w-full">

  <!-- Header -->
  <header class="flex items-center justify-between p-2 backdrop-blur-md border-b border-primary/5">
    <div class="flex items-center gap-4">
      <h1 class="text-3xl font-black text-text-main tracking-tight">All Category's Management</h1>
    </div>
  </header>
  <div class="bg-panel rounded-3xl shadow-lg border border-primary/5 overflow-hidden">
    <div class="overflow-x-auto">
      <div class="p-4 border-b border-primary/5 flex justify-between items-center">
        <div class="flex items-center gap-2">
          <span class="p-2 bg-primary/10 rounded-lg">
            <span class="material-symbols-outlined text-primary text-sm">groups</span>
          </span>
          <p class="text-sm font-medium text-text-soft">
            Total category's: <span id="totalCount" class="font-bold text-text-main">0</span>
          </p>
        </div>
        <button onclick="openModal()" class="bg-primary text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-primary/90 transition shadow-lg shadow-primary/20">+ Add category</button>
      </div>

      <div class="m-2 p-2 w-full max-w-sm md:block relative">
        <span class="material-symbols-outlined absolute left-3 top-6 text-text-soft">search</span>
        <input type="text" id="categorySearch" placeholder="Search by name or contact..."
          class="w-full pl-10 pr-4 py-2 bg-slate-100 border-none rounded-xl focus:ring-2 focus:ring-primary/20 outline-none transition-all">
      </div>
     
      <div class="overflow-x-auto">
        <table id="category_table" class="w-full text-left">
          <thead class="bg-panel-soft text-[11px] font-bold text-text-soft uppercase tracking-wider">

          </thead>
          <tbody class="divide-y divide-primary/5">

          </tbody>
        </table>
      </div>
    </div>

    <div class="p-6 border-t border-primary/10 flex items-center justify-between bg-panel-soft/50">
      <p id="paginationInfo" class="text-xs font-medium text-text-soft">Showing 0 of 0 categorys</p>
      <div class="flex items-center gap-2">
        <button id="prevPage" class="p-2 rounded-lg hover:bg-panel:bg-panel/10 shadow-sm border border-transparent hover:border-primary/10 transition-all disabled:opacity-30">
          <span class="material-symbols-outlined text-sm">chevron_left</span>
        </button>
        <button id="nextPage" class="p-2 rounded-lg hover:bg-panel:bg-panel/10 shadow-sm border border-transparent hover:border-primary/10 transition-all disabled:opacity-30">
          <span class="material-symbols-outlined text-sm">chevron_right</span>
        </button>
      </div>
    </div>
  </div>
</div>

<div id="modal" class="fixed inset-0 hidden items-center justify-center z-50 backdrop-blur-sm transition-all">
  <div class="bg-white p-8 rounded-[2rem] w-full max-w-[580px] max-h-[90vh] overflow-y-auto relative shadow-2xl border border-primary/10 m-4">

    <div class="flex justify-between items-center mb-2">
      <div>
        <h3 id="modalTitle" class="text-2xl font-black text-text-main">Add New category</h3>
        <p class="text-xs text-text-soft font-medium mt-1 uppercase tracking-widest">category Information System</p>
      </div>
      <button onclick="closeModal()" class="p-2 hover:bg-slate-100:bg-panel/5 rounded-full text-text-soft">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <form id="category_form" class="space-y-4" method="post">
      <div id="success" class="hidden p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100 border border-green-200 shadow-lg" role="alert"> success message </div>
      <div id="error" class="hidden p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100 border border-red-200 shadow-lg" role="alert">this is errors message </div>
      <input type="hidden" name="id" id="update_id">

      <div class="space-y-1.5">
        <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Name</label>
        <input type="text" name="name" id="name" placeholder="E.g. user, Admin" required
          class="w-full p-3.5 bg-panel-soft border border-transparent focus:border-primary/30 focus:bg-panel:bg-darkPanel rounded-2xl outline-none transition-all shadow-inner">
      </div>
      <div class="space-y-1.5">
        <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">icon</label>
        <input type="text" name="icon" id="icon" placeholder="E.g. Home" required
          class="w-full p-3.5 bg-panel-soft border border-transparent focus:border-primary/30 focus:bg-panel:bg-darkPanel rounded-2xl outline-none transition-all shadow-inner">
      </div>
      <div class="space-y-1.5">
        <label class="text-[10px] font-bold text-text-soft uppercase tracking-widest ml-1">Select Type</label>
        <select name="role" id="role" required
          class="w-full p-3.5 bg-panel-soft border border-transparent focus:border-primary/30 focus:bg-panel:bg-darkPanel rounded-2xl outline-none transition-all shadow-inner appearance-none">
          <option value="" disabled selected>Select a Category...</option>
          <option value="Dashboard" class="">Dashboard</option>
          <!-- <option value="Academic" class="">Academic Management</option> -->
          <option value="Reports" class="">Reports & Analytics</option>
          <option value="Finance" class="">Finance</option>
          <option value="News" class="">News</option>
          <option value="SystemManagement" class="">System Management</option>
          <option value="SupportLegal" class="">Support & Legal</option>
        </select>

        <!-- Contact Us Privacy Policy Terms & Conditions FAQ -->
      </div>


      <div class="flex justify-end gap-3 pt-6">
        <button type="button" onclick="closeModal()" class="px-6 py-3 text-text-soft font-bold text-sm hover:bg-slate-100:bg-panel/5 rounded-2xl transition-all">Cancel</button>
        <button type="submit" id="btnSave" class="px-10 py-3 bg-primary text-white font-bold rounded-2xl hover:bg-primary/90 shadow-xl shadow-primary/20 transition-all text-sm">Save category</button>
      </div>
    </form>
  </div>
</div>
<script>
  $(document).ready(function() {
      $("body").append($("#modal"));
  });

  function openModal() {
    $("#category_form")[0].reset();
    $("#update_id").val("");
    $("#action").val("Insert");
    $("#modalTitle").text("Add New category");
    $("#btnSave").text("Save category");
    $("#modal").removeClass("hidden").addClass("flex");
  }

  function closeModal() {
    $("#modal").removeClass("flex").addClass("hidden");
  }
</script>

<script src="../js/category.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include '../reusable/footer.php'; ?>
