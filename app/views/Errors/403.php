<?php
require_once '../../config/init.php';

$redirect_url = isset($_SESSION['user_id']) ? BASE_URL . 'views/dashboard/index.php' : BASE_URL . 'views/Frontend/index.html';

include '../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center p-6 bg-gray-50">
    <div class="text-center animate-fadeIn">
        <h1 class="text-9xl font-black text-red-600 mb-4 opacity-20 tracking-tighter">403</h1>
        <h2 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">Access Denied</h2>
        <p class="text-gray-500 font-medium mb-10 max-w-md mx-auto">
            You don't have permission to view this resource. Please contact your administrator if you think this is a
            mistake.
        </p>

        <a href="<?php echo $redirect_url; ?>"
            class="px-10 py-5 bg-gray-900 text-white font-black rounded-3xl shadow-xl hover:bg-gray-800 transition-all uppercase tracking-widest text-sm inline-block">
            Back to Safety
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>