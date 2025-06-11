<?php 
include "components/header.php";
?>

<!-- Top bar with user profile -->
<div class="max-w-12xl mx-auto flex justify-between items-center bg-white p-4 mb-6 rounded-md shadow-md">
    <h2 class="text-lg font-semibold text-gray-700">Welcome, <?= ucfirst($On_Session[0]['fullname']) ?></h2>
    <div class="text-sm text-gray-600">
        Role: <?= ucfirst($On_Session[0]['role']) ?>
    </div>
</div>

<?php if($On_Session[0]['status'] == 1): ?>
    <!-- Task Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-gray-700 font-semibold text-lg mb-2">Pending Tasks</h3>
            <p class="text-yellow-500 text-2xl font-bold" id="pendingTasks">0</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-gray-700 font-semibold text-lg mb-2">In Progress</h3>
            <p class="text-blue-500 text-2xl font-bold" id="inProgressTasks">0</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-gray-700 font-semibold text-lg mb-2">Completed Tasks</h3>
            <p class="text-green-500 text-2xl font-bold" id="completedTasks">0</p>
        </div>
    </div>

    <!-- Task Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Tasks -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent Tasks</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Product</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Deadline</th>
                        </tr>
                    </thead>
                    <tbody id="recentTasksList">
                        <!-- Tasks will be loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Task Progress -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Task Progress</h3>
            <div id="taskProgressChart"></div>
        </div>
    </div>

    <!-- Current Task Details -->
    <div class="mt-6 bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Current Task Details</h3>
        <div id="currentTaskDetails">
            <!-- Current task details will be loaded dynamically -->
        </div>
    </div>

<?php else: ?>
    <div class="w-full flex items-center p-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 rounded-2xl shadow-lg">
        <img src="https://cdn-icons-png.flaticon.com/512/564/564619.png" alt="Warning Icon" class="w-12 h-12 mr-4">
        <div>
            <p class="font-bold text-xl mb-1">Account Not Verified</p>
            <p class="text-base">Please wait for Administrator Verification.</p>
        </div>
    </div>
<?php endif; ?>

<!-- Include ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="assets/js/member-dashboard.js"></script>

<?php include "components/footer.php"; ?>