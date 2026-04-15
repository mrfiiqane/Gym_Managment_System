<!DOCTYPE html>
<html lang="en" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('APP_NAME') ? APP_NAME : 'Gym Management System'; ?></title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo function_exists('generate_csrf_token') ? generate_csrf_token() : ''; ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Generated via CLI) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>src/output.css">    
    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    
    <!-- charts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Custom Icons if any -->
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/icons/favicon.ico">

    <!-- jQuery -->
    <script src="<?php echo BASE_URL; ?>assets/js/jquery.min.js"></script>
    
    <!-- AJAX Helper (Toasts, Loaders) -->
    <script src="<?php echo BASE_URL; ?>reusable/helper.js"></script>

    <!-- displayMessage -->
    <script src="<?php echo BASE_URL; ?>reusable/displayMessage.js"></script>


    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.02);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc; /* slate-50 */
            color: #0f172a; /* slate-900 */
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.4s ease-out forwards;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 selection:bg-blue-500 selection:text-white overflow-x-hidden">
