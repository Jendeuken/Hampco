<?php
session_start();
include('backend/class.php');

$db = new global_class();

if (isset($_SESSION['id'])) {
    $id = intval($_SESSION['id']);
    $On_Session = $db->check_account($id, 'member');
    // echo "<pre>";
    // print_r($On_Session); 
    // echo "</pre>";
    
    if (!empty($On_Session)) {
        if($_SESSION['user_type']=="admin"){

          header('location: ../admin/dashboard');

        }else  if($_SESSION['user_type']=="customer"){
            header('location: ../customer/dashboard');
        }else  if($_SESSION['user_type']=="member"){
           
        }else{
            header('location: ../');
        }
    } else {
       header('location: ../');
    }
} else {
   header('location: ../');
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HAMPCO</title>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/css/alertify.css" integrity="sha512-MpdEaY2YQ3EokN6lCD6bnWMl5Gwk7RjBbpKLovlrH6X+DRokrPRAF3zQJl1hZUiLXfo2e9MrOt+udOnHCAmi5w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/alertify.min.js" integrity="sha512-JnjG+Wt53GspUQXQhc+c4j8SBERsgJAoHeehagKHlxQN+MtCCmFDghX9/AcbkkNRZptyZU4zC8utK59M5L45Iw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

  <style>
    .swal2-popup {
      font-family: 'Inter', sans-serif;
    }
    .swal2-confirm, .swal2-cancel {
      font-weight: 500 !important;
      padding: 0.625rem 1.5rem !important;
      border-radius: 0.5rem !important;
    }
    .swal2-confirm:focus, .swal2-cancel:focus {
      box-shadow: none !important;
    }
  </style>

</head>
<body class="bg-gray-100 font-sans antialiased">

<input type="text" id="user_id" value="<?=$On_Session[0]['id']?>" hidden>

<?php include "../function/PageSpinner.php"; ?>





  <div class="min-h-screen flex flex-col lg:flex-row">
    
  <!-- Sidebar -->
<aside id="sidebar" class="bg-white shadow-lg w-64 lg:w-1/5 xl:w-1/6 p-6 space-y-6 lg:static fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
  <!-- Hide Sidebar Button -->
    <div class="flex items-center space-x-4 p-4 bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User Role Icon" class="w-20 h-20 rounded-full border-2 border-gray-300 shadow-sm transform transition-transform duration-300 hover:scale-105"> <!-- Flaticon Logo -->
        <h1 class="text-lg font-bold text-gray-800 tracking-tight text-left lg:text-left hover:text-indigo-600 transition-colors duration-300">
            <?= strtoupper($On_Session[0]['role']) ?>
        </h1>
    </div>



    <nav class="space-y-2">
        <?php if ($On_Session[0]['status'] == 1) { ?>
            <!-- Dashboard -->
            <a href="member_dashboard" class="flex items-center space-x-3 text-gray-700 p-3 rounded-lg hover:bg-gray-100 transition-all duration-200">
                <span class="material-icons text-gray-500">home</span>
                <span class="font-medium">Home</span>
            </a>

            <!-- Production Line -->
            <a href="production" class="flex items-center space-x-3 text-gray-700 p-3 rounded-lg hover:bg-gray-100 transition-all duration-200">
                <span class="material-icons text-gray-500">precision_manufacturing</span>
                <span class="font-medium">Production Line</span>
            </a>

            <!-- Settings -->
            <a href="settings" class="flex items-center space-x-3 text-gray-700 p-3 rounded-lg hover:bg-gray-100 transition-all duration-200">
                <span class="material-icons text-gray-500">settings</span>
                <span class="font-medium">Settings</span>
            </a>
        <?php } ?>

        <!-- Logout -->
        <a href="logout.php" class="flex items-center space-x-3 text-red-600 p-3 rounded-lg hover:bg-red-50 transition-all duration-200 mt-4">
            <span class="material-icons">logout</span>
            <span class="font-medium">Logout</span>
        </a>
    </nav>

</aside>



    <!-- Overlay for Mobile Sidebar -->
    <div id="overlay" class="fixed inset-0 bg-black opacity-50 hidden lg:hidden z-40"></div>

    <!-- Main Content -->
    <main class="flex-1 bg-gray-50 p-8 lg:p-12">
      <!-- Mobile menu button -->
      <button id="menuButton" class="lg:hidden text-gray-700 mb-4">
        <span class="material-icons">menu</span> 
      </button>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to set active menu item
            function setActiveMenuItem() {
                const currentPath = window.location.pathname;
                const menuItems = document.querySelectorAll('nav a');
                
                menuItems.forEach(item => {
                    const href = item.getAttribute('href');
                    if (currentPath.includes(href) && href !== '' && !href.includes('logout')) {
                        item.classList.add('bg-gray-100');
                    }
                });
            }

            // Set active menu item on page load
            setActiveMenuItem();

            // Handle mobile menu
            const menuButton = document.getElementById('menuButton');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            menuButton.addEventListener('click', function() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            });

            overlay.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        });
    </script>

   

     