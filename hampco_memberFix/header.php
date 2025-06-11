<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HAMPCO</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- <link rel="icon" type="image/png" href="customer/assets/logo1.png"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/css/alertify.css" integrity="sha512-MpdEaY2YQ3EokN6lCD6bnWMl5Gwk7RjBbpKLovlrH6X+DRokrPRAF3zQJl1hZUiLXfo2e9MrOt+udOnHCAmi5w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/alertify.min.js" integrity="sha512-JnjG+Wt53GspUQXQhc+c4j8SBERsgJAoHeehagKHlxQN+MtCCmFDghX9/AcbkkNRZptyZU4zC8utK59M5L45Iw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</head>

<body class="bg-gray-50 bg-cover bg-center" >
  <?php include "function/PageSpinner.php"; ?>

  <!-- Header -->
  <header class="bg-white shadow">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <!-- Logo/Brand Name -->
      <div class="flex items-center space-x-3">
        <!-- <img src="admin/assets/logo1.png" alt="Logo" class="w-12 h-12 hidden sm:block"> -->
        <div class="text-xl font-bold text-gray-800">
          <a href="#" class="text-gray-700 hover:text-blue-600 transition">HAMPCO</a>
        </div>
      </div>

      <!-- Navigation Links -->
      <div class="flex items-center space-x-6">
        <a href="login_customer" class="flex items-center text-gray-700 hover:text-blue-600 transition">
          <span class="material-icons mr-1">person</span> Customer
        </a>

        <div class="w-px h-6 bg-gray-300"></div> <!-- Stylish Separator -->

        <a href="login_member" class="flex items-center text-gray-700 hover:text-blue-600 transition">
          <span class="material-icons mr-1">groups</span> Member
        </a>
      </div>
    </div>
  </header>
