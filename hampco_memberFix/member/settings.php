<?php 
include "components/header.php";
?>


    <!-- Top bar with user profile -->
    <div class="max-w-12xl mx-auto flex justify-between items-center bg-white p-4 mb-6 rounded-md shadow-md">
        <h2 class="text-lg font-semibold text-gray-700">Account Settings</h2>
        <div class="w-10 h-10 ">
           
        </div>
    </div>
    <?php 
    if($On_Session[0]['status']==1){ 
    ?>
   
    <?php 
    }else{
    ?>
    <div class="w-full flex items-center p-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 rounded-2xl shadow-lg">
        <img src="https://cdn-icons-png.flaticon.com/512/564/564619.png" alt="Warning Icon" class="w-12 h-12 mr-4">
        <div>
            <p class="font-bold text-xl mb-1">Account Not Verified</p>
            <p class="text-base">Please wait for Administrator Verification.</p>
        </div>
    </div>

    <?php 
    }
    ?>

<!-- Include ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


<?php include "components/footer.php"; ?>
<script src="assets/js/app.js"></script>