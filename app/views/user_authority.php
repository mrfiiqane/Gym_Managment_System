<?php
require_once '../config/init.php';
// Use reusable components as requested
include '../reusable/header.php';
include '../reusable/sidebar.php';
include '../reusable/loader.php';
?>

<main
    class="space-y-4 animate-fadeIn  dark:bg-darkPanel w-[350px] md:w-full px-2 py-4 md:p-4 rounded-3xl shadow-sm border border-primary/5">

    <div class="bg-white shadow rounded-lg p-4 max-w-4xl mx-auto my-4">
        <!-- Header -->
        <header class="flex items-center justify-between px-2 backdrop-blur-md border-b border-primary/5">
            <div class="flex items-center gap-4">
                <h1 class="text-start text-3xl font-black text-blue-700 dark:text-white tracking-tight"> User Authority</h1>
            </div>
        </header>
        <form id="userForm">
            <div id="success"
                class="hidden p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100 border border-green-200 shadow-lg"
                role="alert"> success message </div>
            <div id="error"
                class="hidden p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100 border border-red-200 shadow-lg"
                role="alert">this is errors message </div>
            <input type="hidden" name="id" id="action_id">

            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-medium Text ml-2">
                    All Users Authority Management<br>
                </h3>
            </div>

            <select name="user_id" id="user_id"
                class="w-full my-3 rounded-lg border border-gray-300 bg-white px-5 py-2 text-md focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </select>


            <div class="w-full">
                <fieldset class="rounded-lg border border-gray-400 p-4">
                    <legend class="px-2 text-lg font-bold Text flex items-center gap-1.5">
                        <input type="checkbox" id="all_authority" name="all_authority"
                            class="h-4 w-4 rounded-lg text-blue-600  focus:ring-blue-500">
                        All Authorities
                    </legend>

                    <!-- Authority Area -->
                    <div id="authorityArea" class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Dynamic Content Here -->
                    </div>
                </fieldset>
            </div>

            <button id="submit" type="submit"
                class="mx-auto my-2 h-10 whitespace-nowrap rounded-lg bg-primary px-4 py-1 font-bold text-white shadow-xl shadow-primary/20 transition hover:bg-primary/90">
                + Authorize User
            </button>
        </form>
    </div>




</main>


<script src="../js/user_authority.js"></script>

<?php include '../reusable/footer.php'; ?>