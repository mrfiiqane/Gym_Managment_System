<?php
require_once '../../config/init.php';

$redirect_url = isset($_SESSION['user_id']) ? BASE_URL . 'views/dashboard/index.php' : BASE_URL . 'views/Frontend/index.html';

include '../../reusable/header.php';
?>

<div class="min-h-screen flex items-center justify-center p-6 bg-gray-50">
    <div class="text-center animate-fadeIn">
        <h1 class="text-9xl font-black text-blue-600 mb-4 opacity-20 tracking-tighter">404</h1>
        <h2 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">Page Not Found</h2>
        <p class="text-gray-500 font-medium mb-10 max-w-md mx-auto">The page you're looking for doesn't exist or has been moved. Let's get you back on track.</p>
        <a href="<?php echo $redirect_url; ?>" class="px-10 py-5 bg-gray-900 text-white font-black rounded-3xl shadow-xl hover:bg-gray-800 transition-all uppercase tracking-widest text-sm inline-block">Go Home</a>
    </div>
</div>

<?php include '../../reusable/footer.php'; ?>