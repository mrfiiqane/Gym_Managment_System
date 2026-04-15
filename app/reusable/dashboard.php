<!DOCTYPE html>
<html lang="en" class="light">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Professional Gym Dashboard</title>

  <link href="<?= BASE_URL ?>src/output.css" rel="stylesheet">
  

  

  <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/style/classes.css">
  <link rel="stylesheet" href="../assets/style/style.css">
  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Lexend', 'sans-serif']
          },
          colors: {
            primary: '#89986D',
            secondary: '#9CAB84',
            darkBg: '#12140e',
            darkPanel: '#1a1d15'
          }
        }
      }
    }
  </script>


  <style>
    .sidebar-transition {
      transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .content-transition {
      transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
  </style>
</head>

<body class="body_app dark:bg-darkBg antialiased text-slate-800 dark:text-slate-200 ">

  <aside id="sidebar" class="sidebar-transition fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-darkPanel border-r border-primary/10 flex flex-col shadow-xl">

    <div class="h-20 flex items-center px-6 gap-3 border-b border-primary/5">
      <div class="bg-primary p-2 rounded-lg text-white shrink-0"> <span class="material-symbols-outlined">fitness_center</span> </div> <span id="logo-text" class="ease-in-out font-bold text-lg tracking-tight truncate text-slate-800 dark:text-white">GYM SYSTEM</span>
    </div>

    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
      <a href="charts.php" class="flex items-center gap-4 px-4 py-3 rounded-xl ts5 dark:text-slate-400 hover:bg-secondary/10 hover:text-primary transition-all">
        <span class="material-symbols-outlined">analytics</span>
        <span class="nav-text font-medium">analaysis</span> </a>
      

      <!-- Members Menu -->
      <div class="space-y-1">
        <button type="button" onclick="toggleMenu(this, 'students-menu','students-arrow')" data-menu="students" class="nav-item w-full flex items-center justify-between px-4 py-3 rounded-xl ts5 dark:text-slate-400 hover:bg-primary/10 hover:text-primary transition-all">
          <div class="flex items-center gap-4">
            <span class="material-symbols-outlined">group</span>
            <span class="nav-text font-medium">Members</span>
          </div>
          <span id="students-arrow" class="material-symbols-outlined text-sm transition-transform"> expand_more </span>
        </button>
        <!-- Sub Menu -->
        <div id="students-menu" class="ml-10 mt-1 space-y-1 hidden">
          <a href="students.php" class="nav-sub block px-4 py-2 rounded-lg text-sm ts5 dark:text-slate-400 hover:bg-primary/10 hover:text-primary"> All Members </a>
          <a href="student_profile.php" class="nav-sub block px-4 py-2 rounded-lg text-sm ts5 dark:text-slate-400 hover:bg-primary/10 hover:text-primary"> Member Profile </a>
        </div>

      </div>
      
      
      <!-- Trainers Menu -->
      <div class="space-y-1">
        <button type="button" onclick="toggleMenu(this, 'teacher-menu', 'teacher-arrow')" data-menu="teachers" class="nav-item w-full flex items-center justify-between px-4 py-3 rounded-xl ts5 dark:text-slate-400 hover:bg-primary/10 hover:text-primary transition-all">
          <div class="flex items-center gap-4">
            <span class="material-symbols-outlined">person</span>
            <span class="nav-text font-medium">Trainers</span>
          </div>
          <span id="teacher-arrow" class="material-symbols-outlined text-sm transition-transform"> expand_more </span>
        </button>
        <div id="teacher-menu" class="ml-10 mt-1 space-y-1 hidden">
          <a href="teachers.php" class="nav-sub block px-4 py-2 rounded-lg text-sm ts5 dark:text-slate-400 hover:bg-primary/10 hover:text-primary"> All Trainers </a>
          <a href="teacher_profile.php" class="nav-sub block px-4 py-2 rounded-lg text-sm ts5 dark:text-slate-400 hover:bg-primary/10 hover:text-primary"> Trainer Profile </a>
        </div>
      </div>
      <a href="" class="flex items-center gap-4 px-4 py-3 rounded-xl ts5 dark:text-slate-400 hover:bg-secondary/10 hover:text-primary transition-all">
        <span class="material-symbols-outlined">payments</span>
        <span class="nav-text font-medium">Fee Status</span>
      </a>
      <a href="info.php" class="flex items-center gap-4 px-4 py-3 rounded-xl ts5 dark:text-slate-400 hover:bg-secondary/10 hover:text-primary transition-all">
        <span class="material-symbols-outlined">info</span>
        <span class="nav-text font-medium">Info</span>
      </a>
    </nav>

    <div class="p-4 border-t border-primary/10">
      <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-white/5 relative group"> 
        <img src="../uploads/<?php echo $_SESSION['image'] ?? 'default.png'; ?>" class="w-10 h-10 rounded-full border-2 border-primary/20 shrink-0 object-cover">
        <div id="user-info" class="overflow-hidden">
          <p class="text-xs font-bold truncate"><?php echo $_SESSION['full_name'] ?? 'User'; ?></p>
          <p class="text-[10px] text-primary font-medium capitalize"><?php echo (isset($_SESSION['role']) ? ($_SESSION['role'] === 'Teacher' ? 'Trainer' : ($_SESSION['role'] === 'Student' ? 'Member' : $_SESSION['role'])) : 'Guest'); ?></p>
        </div>
        <div class="absolute bottom-full left-0 mb-2 w-full bg-white dark:bg-darkPanel rounded-xl shadow-2xl border border-primary/10 opacity-0 group-hover:opacity-100 transition-opacity p-2 pointer-events-none group-hover:pointer-events-auto"> 
            <a href="profile.php" class="flex items-center gap-2 p-2 hover:bg-primary/10 rounded-lg text-xs font-medium"> <span class="material-symbols-outlined text-sm">settings</span> Profile </a>
            <hr class="my-1 border-primary/5"> 
            <a href="logout.php" class="flex items-center gap-2 p-2 hover:bg-red-50 text-red-500 rounded-lg text-xs font-medium"> <span class="material-symbols-outlined text-sm">logout</span> Logout </a>
        </div>
      </div>
    </div>
  </aside>

