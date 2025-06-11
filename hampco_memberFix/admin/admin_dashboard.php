<?php
require_once "components/header.php";
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Admin Dashboard</h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Customers Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-600 text-sm">Total Customers</h2>
                    <p id="totalCustomers" class="text-2xl font-semibold text-gray-800">0</p>
                </div>
            </div>
        </div>

        <!-- Members Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-600 text-sm">Total Members</h2>
                    <p id="totalMembers" class="text-2xl font-semibold text-gray-800">0</p>
                    <div class="flex space-x-2 text-sm">
                        <span id="activeMembers" class="text-green-600">0 Active</span>
                        <span id="pendingMembers" class="text-yellow-600">0 Pending</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-600 text-sm">Production Items</h2>
                    <p id="totalProducts" class="text-2xl font-semibold text-gray-800">0</p>
                </div>
            </div>
        </div>

        <!-- Tasks Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-600 text-sm">Active Tasks</h2>
                    <p id="activeTasks" class="text-2xl font-semibold text-gray-800">0</p>
                    <div class="flex space-x-2 text-sm">
                        <span id="pendingTasks" class="text-yellow-600">0 Pending</span>
                        <span id="inProgressTasks" class="text-blue-600">0 In Progress</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Tasks -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Tasks</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Product</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Member</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody id="recentTasksList">
                            <!-- Tasks will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Member Distribution -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Member Distribution</h2>
                <div id="memberDistributionChart" class="mb-4"></div>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-sm text-gray-600">Knotters</p>
                        <p id="totalKnotters" class="text-xl font-semibold text-blue-600">0</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Warpers</p>
                        <p id="totalWarpers" class="text-xl font-semibold text-green-600">0</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Weavers</p>
                        <p id="totalWeavers" class="text-xl font-semibold text-yellow-600">0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Raw Materials Overview -->
    <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Raw Materials Overview</h2>
        <div id="rawMaterialsOverview" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Raw materials will be loaded here -->
        </div>
    </div>
</div>

<!-- Include ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<!-- Include dashboard JavaScript -->
<script src="assets/js/dashboard.js"></script>

<?php
require_once "components/footer.php";
?>