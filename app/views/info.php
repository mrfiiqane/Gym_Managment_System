<?php
require_once '../config/init.php';
include '../reusable/header.php';
include '../reusable/sidebar.php';
?>


<div class="flex-1 overflow-y-auto bg-panel-soft/50 dark:bg-dark-bg py-4 px-2 transition-all duration-300">

    class="max-w-7xl mx-auto flex items-center justify-between rounded-2xl p-5 bg-panel/50 dark:bg-dark-panel/50 backdrop-blur-md sticky top-0 z-40 border-b border-primary/5">
    <div class="flex items-center gap-4 mt-4">
      <h2 class="font-bold text-xl">About Us</h2>
    </div>
  </header>

  <div class="max-w-7xl mx-auto p-2 md:p-5 ">

  <div
    class="bg-panel dark:bg-dark-panel rounded-4xl border border-primary/5 shadow-xl shadow-primary/5 overflow-hidden">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 p-10">

      <div class="space-y-6">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-lg shadow-primary/20">
            <span class="material-symbols-outlined text-white">school</span>
          </div>
          <h2 class="text-xl font-black tracking-tight">Student<span class="text-primary">MS</span></h2>
        </div>
        <p class="text-sm text-text-soft dark:text-white/60 leading-relaxed">
          Nidaamka Maareynta Ardayda ee ugu casrisan, kaas oo kuu fududeynaya maamulka xogta iyo horumarka waxbarasho.
        </p>
        <div class="flex gap-3">
          <a href="#"
            class="w-9 h-9 rounded-lg bg-panel-soft dark:bg-dark-bg flex items-center justify-center text-text-soft hover:bg-primary hover:text-white transition-all duration-300">
            <i class="fa-brands fa-facebook-f text-sm"></i>
          </a>
          <a href="#"
            class="w-9 h-9 rounded-lg bg-panel-soft dark:bg-dark-bg flex items-center justify-center text-text-soft hover:bg-primary hover:text-white transition-all duration-300">
            <i class="fa-brands fa-twitter text-sm"></i>
          </a>
          <a href="#"
            class="w-9 h-9 rounded-lg bg-panel-soft dark:bg-dark-bg flex items-center justify-center text-text-soft hover:bg-primary hover:text-white transition-all duration-300">
            <i class="fa-brands fa-instagram text-sm"></i>
          </a>
          <a href="#"
            class="w-9 h-9 rounded-lg bg-panel-soft dark:bg-dark-bg flex items-center justify-center text-text-soft hover:bg-primary hover:text-white transition-all duration-300">
            <i class="fa-brands fa-linkedin-in text-sm"></i>
          </a>
        </div>
      </div>

      <div>
        <h4 class="font-bold text-text-main dark:text-white mb-6 flex items-center gap-2">
          <span class="w-1.5 h-1.5 bg-primary rounded-full"></span> Quick Links
        </h4>
        <ul class="space-y-4">
          <li><a href="#"
              class="text-sm text-text-soft dark:text-white/60 hover:text-primary transition-colors flex items-center gap-2">
              Dashboard</a></li>
          <li><a href="#"
              class="text-sm text-text-soft dark:text-white/60 hover:text-primary transition-colors flex items-center gap-2">
              All Students</a></li>
          <li><a href="#"
              class="text-sm text-text-soft dark:text-white/60 hover:text-primary transition-colors flex items-center gap-2">
              Attendance</a></li>
          <li><a href="#"
              class="text-sm text-text-soft dark:text-white/60 hover:text-primary transition-colors flex items-center gap-2">
              Fee Reports</a></li>
        </ul>
      </div>

      <div>
        <h4 class="font-bold text-text-main dark:text-white mb-6 flex items-center gap-2">
          <span class="w-1.5 h-1.5 bg-primary rounded-full"></span> Contact Us
        </h4>
        <ul class="space-y-4">
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-primary text-lg size-6 animate-bounce">call</span>
            <div class="text-sm text-text-soft dark:text-white/60">
              <p>+252 61 XXXXXXX</p>
              <p>+252 61 YYYYYYY</p>
            </div>
          </li>
          <li class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-lg">mail</span>
            <span class="text-sm text-text-soft dark:text-white/60">support@studentms.com</span>
          </li>
          <li class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-lg size-6 animate-bounce">location_on</span>
            <span class="text-sm text-text-soft dark:text-white/60">Mogadishu, Somalia</span>
          </li>
        </ul>
      </div>

      <div class="bg-primary/5 rounded-2xl p-6 border border-primary/10">
        <h4 class="font-bold text-primary mb-3 text-sm uppercase tracking-wider">System Status</h4>
        <div class="flex items-center gap-3 mb-4">
          <div class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
          </div>
          <span class="text-sm font-medium text-green-600">All Systems Operational</span>
        </div>
        <p class="text-[11px] text-text-soft dark:text-white/60 mb-4">Wixii cilad ah oo aad isku aragto fadlan la
          xiriir kooxda farsamada.</p>
        <button
          class="w-full py-2 bg-panel dark:bg-dark-bg text-primary text-xs font-bold rounded-lg border border-primary/20 hover:bg-primary hover:text-white transition-all shadow-sm">
          Get Support
        </button>
      </div>

    </div>

  </div>
</div>


<?php
include '../reusable/footer.php';
?>
