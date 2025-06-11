<?php 
include "components/header.php";

// Fetch existing production line items
$query = "SELECT * FROM production_line ORDER BY date_created DESC";
$result = mysqli_query($db->conn, $query);
?>

<!-- Top bar with user profile -->
<div class="flex justify-between items-center bg-white p-4 mb-6 rounded-md shadow-md">
    <h2 class="text-lg font-semibold text-gray-700">Production Line</h2>
    <button id="createProductBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
        Create a Product
    </button>
</div>

<!-- Create Product Form Modal -->
<div id="productFormModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg p-8 w-full max-w-md mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Create Production Item</h2>
            <button type="button" class="closeProductModal text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <form id="productForm" method="POST" class="space-y-6">
            <div class="space-y-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="product_name">
                        Product Name
                    </label>
                    <select id="product_name" name="product_name" required
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="" disabled selected>Select a product</option>
                        <option value="Piña Seda">Piña Seda</option>
                        <option value="Pure Piña Cloth">Pure Piña Cloth</option>
                        <option value="Knotted Liniwan">Knotted Liniwan</option>
                        <option value="Knotted Bastos">Knotted Bastos</option>
                        <option value="Warped Silk">Warped Silk</option>
                    </select>
                </div>
                <div id="dimensionsFields" class="hidden">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="length">
                            Length (m)
                        </label>
                        <input type="number" id="length" name="length" min="0.01" step="0.01"
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter length in meters">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="width">
                            Width (m)
                        </label>
                        <input type="number" id="width" name="width" min="0.01" step="0.01"
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter width in meters">
                    </div>
                </div>
                <div id="weightField" class="hidden mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="weight">
                        Weight (g)
                    </label>
                    <input type="number" id="weight" name="weight" min="1" step="0.01"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter weight in grams">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="quantity">
                        Quantity
                    </label>
                    <input type="number" id="quantity" name="quantity" required min="1"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter quantity">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button type="button" class="closeProductModal px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Create Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Task Assignment Modal -->
<div id="taskAssignmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-[9999]">
    <div class="bg-white p-8 rounded-lg shadow-lg w-[600px] relative z-[10000]">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold">Assign Tasks</h3>
            <button id="closeTaskModal" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="taskAssignmentForm">
            <input type="hidden" id="identifier" name="identifier">
            <input type="hidden" id="product_details" name="product_details">
            <input type="hidden" id="prod_line_id" name="prod_line_id">
            
            <!-- Knotter Assignment -->
            <div class="mb-6" id="knotterSection">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-semibold">Assign Knotter(s)</h4>
                    <button type="button" id="addKnotterBtn" class="text-blue-500 hover:text-blue-600">
                        Add Knotter
                    </button>
                </div>
                <div id="knotterSelections" class="space-y-3">
                    <div class="knotter-selection">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="relative flex-grow">
                                <select name="knotter_id[]" class="knotter-select form-select block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <option value="" disabled selected hidden>Select a Knotter</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <button type="button" class="remove-knotter text-red-500 hover:text-red-600 hidden whitespace-nowrap px-2">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Estimated Time</label>
                        <div class="text-sm text-gray-500" id="knotter_estimated_time">Calculating...</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1" for="knotter_deadline">Deadline</label>
                        <input type="date" id="knotter_deadline" name="knotter_deadline" class="w-full border border-gray-300 rounded-md px-3 py-1.5">
                    </div>
                </div>
            </div>

            <!-- Warper Assignment -->
            <div class="mb-6" id="warperSection">
                <h4 class="font-semibold mb-2">Assign Warper</h4>
                <div class="space-y-3">
                    <div class="relative">
                        <select name="warper_id" class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="" disabled selected hidden>Select a Warper</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Estimated Time</label>
                            <div class="text-sm text-gray-500" id="warper_estimated_time">Calculating...</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1" for="warper_deadline">Deadline</label>
                            <input type="date" id="warper_deadline" name="warper_deadline" class="w-full border border-gray-300 rounded-md px-3 py-1.5">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weaver Assignment -->
            <div class="mb-6" id="weaverSection">
                <h4 class="font-semibold mb-2">Assign Weaver</h4>
                <div class="space-y-3">
                    <div class="relative">
                        <select name="weaver_id" class="block w-full px-4 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="" disabled selected hidden>Select a Weaver</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Estimated Time</label>
                            <div class="text-sm text-gray-500" id="weaver_estimated_time">Calculating...</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1" for="weaver_deadline">Deadline</label>
                            <input type="date" id="weaver_deadline" name="weaver_deadline" class="w-full border border-gray-300 rounded-md px-3 py-1.5">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelTaskAssignment" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors duration-200">
                    Assign Tasks
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Production Line Table -->
<div class="bg-white rounded-md shadow-md p-4 mb-8">
    <div class="mb-4">
        <input type="text" id="productSearchInput" placeholder="Search products..." 
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-center">Production ID</th>
                    <th class="px-4 py-2 text-center">Product Name</th>
                    <th class="px-4 py-2 text-center dimensions-header hidden">Length (m)</th>
                    <th class="px-4 py-2 text-center dimensions-header hidden">Width (m)</th>
                    <th class="px-4 py-2 text-center weight-header hidden">Weight (g)</th>
                    <th class="px-4 py-2 text-center">Quantity</th>
                    <th class="px-4 py-2 text-center">Raw Materials</th>
                    <th class="px-4 py-2 text-center">Date Added</th>
                    <th class="px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="productTable">
                <?php while($row = mysqli_fetch_assoc($result)) { 
                    $display_id = 'PL' . str_pad($row['prod_line_id'], 4, '0', STR_PAD_LEFT);
                    $action_id = $row['prod_line_id'];
                    $is_dimensions_product = in_array($row['product_name'], ['Piña Seda', 'Pure Piña Cloth']);
                    
                    // Calculate raw material consumption
                    $materials = [];
                    try {
                        require_once 'backend/raw_material_calculator.php';
                        $calculator = new RawMaterialCalculator($db);
                        
                        // Debug log input values
                        error_log("DEBUG: Starting material calculation for row:");
                        error_log(json_encode($row, JSON_PRETTY_PRINT));
                        error_log(sprintf(
                            "Calculating materials for: Product=%s, Quantity=%d, Length=%f, Width=%f, Weight=%f",
                            $row['product_name'],
                            $row['quantity'],
                            $row['length_m'],
                            $row['width_m'],
                            $row['weight_g']
                        ));
                        
                        $materials = $calculator->calculateMaterialsNeeded(
                            $row['product_name'],
                            $row['quantity'],
                            $row['length_m'],
                            $row['width_m'],
                            $row['weight_g']
                        );
                        
                        // Debug log results
                        error_log("DEBUG: Calculation results:");
                        error_log(json_encode($materials, JSON_PRETTY_PRINT));
                        
                    } catch (Exception $e) {
                        error_log("Error calculating materials: " . $e->getMessage());
                        error_log("Stack trace: " . $e->getTraceAsString());
                        
                        // If it's a Knotted Liniwan product, calculate materials without deducting from inventory
                        if ($row['product_name'] === 'Knotted Liniwan' && $row['weight_g'] !== null) {
                            $totalProductionWeight = $row['weight_g'] * $row['quantity'];
                            $pinaLooseAmount = $totalProductionWeight * 1.4; // Using the constant rate
                            
                            $materials = [
                                [
                                    'name' => 'Piña Loose',
                                    'category' => 'Liniwan/Washout',
                                    'amount' => $pinaLooseAmount,
                                    'details' => [
                                        'rate' => 1.4,
                                        'unit' => 'g/g',
                                        'quantity' => $row['quantity'],
                                        'weight' => sprintf('%.2fg', $row['weight_g']),
                                        'total_weight' => sprintf('%.2fg', $totalProductionWeight)
                                    ]
                                ]
                            ];
                        }
                    }
                ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2 text-center font-mono text-sm"><?php echo htmlspecialchars($display_id); ?></td>
                        <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <?php if ($is_dimensions_product): ?>
                        <td class="px-4 py-2 text-center dimensions-cell"><?php 
                            $length = (float)$row['length_m'];
                            echo $length == floor($length) ? number_format($length, 0) : number_format($length, 2); 
                        ?></td>
                        <td class="px-4 py-2 text-center dimensions-cell"><?php 
                            $width = (float)$row['width_m'];
                            echo $width == floor($width) ? number_format($width, 0) : number_format($width, 2); 
                        ?></td>
                        <td class="px-4 py-2 text-center weight-cell hidden">-</td>
                        <?php else: ?>
                        <td class="px-4 py-2 text-center dimensions-cell hidden">-</td>
                        <td class="px-4 py-2 text-center dimensions-cell hidden">-</td>
                        <td class="px-4 py-2 text-center weight-cell"><?php echo number_format((float)$row['weight_g'], 0); ?></td>
                        <?php endif; ?>
                        <td class="px-4 py-2 text-center"><?php echo $row['quantity']; ?></td>
                        <td class="px-4 py-2 text-center">
                            <?php if (!empty($materials)): ?>
                            <button onclick="showMaterialsModal(<?php echo htmlspecialchars(json_encode($materials, JSON_NUMERIC_CHECK)); ?>, <?php echo htmlspecialchars(json_encode([
                                'name' => $row['product_name'],
                                'length' => $row['length_m'],
                                'width' => $row['width_m'],
                                'weight' => $row['weight_g'],
                                'quantity' => $row['quantity']
                            ])); ?>)" 
                                    class="bg-blue-100 text-blue-700 px-2 py-1 rounded-md hover:bg-blue-200 transition-colors">
                                View Materials
                            </button>
                            <?php else: ?>
                            <span class="text-gray-500">No materials data</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2 text-center"><?php echo date('Y-m-d H:i', strtotime($row['date_created'])); ?></td>
                        <td class="px-4 py-2 text-center">
                            <div class="flex justify-center space-x-2">
                                <button onclick="assignTask('<?php echo $action_id; ?>')" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-sm">
                                    Assign Task
                                </button>
                                <button onclick="editProduct('<?php echo $action_id; ?>')"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-sm">
                                    Edit
                                </button>
                                <button onclick="deleteProduct('<?php echo $action_id; ?>')"
                                    class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-sm">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Assigned Tasks Section -->
<div class="mt-12">
    <h2 class="text-lg font-semibold text-gray-700 mb-6">Assigned Tasks</h2>
    
    <!-- Task filters -->
    <div class="bg-white p-4 rounded-md shadow-md mb-6">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                <select id="statusFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="taskSearchInput" placeholder="Search by product name..." 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-md shadow-md p-4 overflow-x-auto">
        <table class="min-w-full table-auto" id="tasksTable">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm leading-normal">
                    <th class="py-3 px-6 text-center">Production ID</th>
                    <th class="py-3 px-6 text-center">Product Name</th>
                    <th class="py-3 px-6 text-center">Status</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm" id="assignedTasksTableBody">
                <!-- Tasks will be loaded via AJAX -->
                <tr><td colspan="4" class="py-3 px-6 text-center text-gray-500">Loading tasks...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold">Product Details</h3>
            <button id="closeDetailsModal" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="detailsContent" class="space-y-4">
            <!-- Content will be populated by JavaScript -->
        </div>
        <div class="flex justify-end mt-6">
            <button id="closeDetailsBtn" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Assigned Members Modal -->
<div id="membersModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-[9999] backdrop-blur-sm">
    <div class="bg-white p-8 rounded-lg shadow-lg w-[600px] relative z-[10000]">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold">Assigned Members</h3>
            <button id="closeMembersModal" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="membersContent" class="space-y-4 max-h-[70vh] overflow-y-auto">
            <!-- Content will be populated by JavaScript -->
        </div>
        <div class="flex justify-end mt-6">
            <button id="closeMembersBtn" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Materials Modal -->
<div id="materialsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-[9999] backdrop-blur-sm">
    <div class="bg-white p-8 rounded-lg shadow-lg w-[600px] relative z-[10000]">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold">Materials Used</h3>
            <button onclick="closeMaterialsModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="materialsContent" class="space-y-4 max-h-[70vh] overflow-y-auto">
            <!-- Content will be populated by JavaScript -->
        </div>
        <div class="flex justify-end mt-6">
            <button onclick="closeMaterialsModal()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- View Assigned Member Modal -->
<div id="viewAssignedMemberModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg p-8 w-full max-w-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Assigned Members</h2>
            <button type="button" class="closeViewAssignedModal text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div id="assignedMembersContent">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<?php include "components/footer.php";?>

<script src="assets/js/app.js"></script>

<script>
function calculateTimeEstimates(productDetails) {
    console.log('Calculating time estimates for:', productDetails);
    const estimates = {
        knotter: 0,
        warper: 0,
        weaver: 0
    };

    // Calculate time based on product type
    switch (productDetails.product_name) {
        case 'Piña Seda':
            // Find Piña Loose (Bastos) material
            const pinaBastosAmount = productDetails.materials?.find(
                m => m.name === 'Piña Loose' && m.category === 'Bastos'
            )?.amount || 0;

            // Find Silk material
            const silkAmount = productDetails.materials?.find(
                m => m.name === 'Silk'
            )?.amount || 0;

            // Knotter time estimation: 30 days per 7g of Piña Loose (Bastos)
            if (pinaBastosAmount > 0) {
                estimates.knotter = Math.ceil((pinaBastosAmount / 7) * 30);
            }

            // Warper time estimation: 14 days per 500g of silk
            if (silkAmount > 0) {
                estimates.warper = Math.ceil((silkAmount / 500) * 14);
            }

            // Weaver time estimation: 1 day per 1m × 1m area
            if (productDetails.length_m && productDetails.width_m) {
                const area = productDetails.length_m * productDetails.width_m;
                estimates.weaver = Math.ceil(area * productDetails.quantity);
            }
            break;

        case 'Pure Piña Cloth':
            // Find Piña Loose (Liniwan/Washout) material
            const pinaLiniwanAmount = productDetails.materials?.find(
                m => m.name === 'Piña Loose' && m.category === 'Liniwan/Washout'
            )?.amount || 0;

            // Knotter time estimation: 30 days per 7g of Piña Loose (Liniwan/Washout)
            if (pinaLiniwanAmount > 0) {
                estimates.knotter = Math.ceil((pinaLiniwanAmount / 7) * 30);
            }

            // Weaver time estimation: 1 day per 1m × 1m area
            if (productDetails.length_m && productDetails.width_m) {
                const area = productDetails.length_m * productDetails.width_m;
                estimates.weaver = Math.ceil(area * productDetails.quantity);
            }
            break;

        case 'Knotted Liniwan':
        case 'Knotted Bastos':
            // Find Piña Loose material based on category
            const category = productDetails.product_name === 'Knotted Liniwan' ? 'Liniwan/Washout' : 'Bastos';
            const pinaAmount = productDetails.materials?.find(
                m => m.name === 'Piña Loose' && m.category === category
            )?.amount || 0;

            // Knotter time estimation: 30 days per 7g of Piña Loose
            if (pinaAmount > 0) {
                estimates.knotter = Math.ceil((pinaAmount / 7) * 30);
            }
            break;

        case 'Warped Silk':
            // Find Silk material
            const warpedSilkAmount = productDetails.materials?.find(
                m => m.name === 'Silk'
            )?.amount || 0;

            // Warper time estimation: 14 days per 500g of silk
            if (warpedSilkAmount > 0) {
                estimates.warper = Math.ceil((warpedSilkAmount / 500) * 14);
            }
            break;
    }

    return estimates;
}

function updateTimeEstimates(estimates) {
    console.log('Updating time estimates:', estimates);
    // Update the displayed estimates
    const knotterElement = document.getElementById('knotter_estimated_time');
    const warperElement = document.getElementById('warper_estimated_time');
    const weaverElement = document.getElementById('weaver_estimated_time');

    if (knotterElement) {
        knotterElement.textContent = estimates.knotter > 0 ? `${estimates.knotter} days` : 'N/A';
    }
    if (warperElement) {
        warperElement.textContent = estimates.warper > 0 ? `${estimates.warper} days` : 'N/A';
    }
    if (weaverElement) {
        weaverElement.textContent = estimates.weaver > 0 ? `${estimates.weaver} days` : 'N/A';
    }

    // Set dates for deadlines sequentially
    const today = new Date();
    let currentDate = new Date(today);
    
    // Knotter deadline (starts from today)
    if (estimates.knotter > 0) {
        const knotterDeadline = new Date(currentDate.getTime() + (estimates.knotter * 24 * 60 * 60 * 1000));
        const knotterInput = document.getElementById('knotter_deadline');
        if (knotterInput) {
            knotterInput.min = knotterDeadline.toISOString().split('T')[0];
            knotterInput.value = knotterDeadline.toISOString().split('T')[0];
            currentDate = knotterDeadline; // Next deadline starts from here
        }
    }
    
    // Warper deadline (starts after knotter)
    if (estimates.warper > 0) {
        const warperDeadline = new Date(currentDate.getTime() + (estimates.warper * 24 * 60 * 60 * 1000));
        const warperInput = document.getElementById('warper_deadline');
        if (warperInput) {
            warperInput.min = warperDeadline.toISOString().split('T')[0];
            warperInput.value = warperDeadline.toISOString().split('T')[0];
            currentDate = warperDeadline; // Next deadline starts from here
        }
    }
    
    // Weaver deadline (starts after warper)
    if (estimates.weaver > 0) {
        const weaverDeadline = new Date(currentDate.getTime() + (estimates.weaver * 24 * 60 * 60 * 1000));
        const weaverInput = document.getElementById('weaver_deadline');
        if (weaverInput) {
            weaverInput.min = weaverDeadline.toISOString().split('T')[0];
            weaverInput.value = weaverDeadline.toISOString().split('T')[0];
        }
    }
}

function assignTask(prodLineId) {
    console.log('Assigning task for production line ID:', prodLineId);
    
    // Set the production line ID in the form
    document.getElementById('identifier').value = prodLineId;
    document.getElementById('prod_line_id').value = prodLineId;
    
    // Get select elements
    const knotterSelects = document.querySelectorAll('.knotter-select');
    const warperSelect = document.querySelector('select[name="warper_id"]');
    const weaverSelect = document.querySelector('select[name="weaver_id"]');
    
    // Load members for each role
    knotterSelects.forEach(select => loadMembers('knotter', select));
    if (warperSelect) loadMembers('warper', warperSelect);
    if (weaverSelect) loadMembers('weaver', weaverSelect);

    // Reset estimated times display
    document.getElementById('knotter_estimated_time').textContent = 'Calculating...';
    document.getElementById('warper_estimated_time').textContent = 'Calculating...';
    document.getElementById('weaver_estimated_time').textContent = 'Calculating...';

    // Fetch product details for time estimation
    $.ajax({
        url: 'backend/end-points/get_product_details.php',
        type: 'GET',
        data: { prod_line_id: prodLineId },
        success: function(response) {
            if (response.success) {
                const productDetails = response.data;
                document.getElementById('product_details').value = JSON.stringify(productDetails);
                
                // Show/hide worker sections based on product type
                const knotterSection = document.getElementById('knotterSection');
                const warperSection = document.getElementById('warperSection');
                const weaverSection = document.getElementById('weaverSection');
                
                // Reset all sections to hidden first
                knotterSection.style.display = 'none';
                warperSection.style.display = 'none';
                weaverSection.style.display = 'none';
                
                // Show relevant sections based on product type
                switch (productDetails.product_name) {
                    case 'Piña Seda':
                        knotterSection.style.display = 'block';
                        warperSection.style.display = 'block';
                        weaverSection.style.display = 'block';
                        break;
                    case 'Pure Piña Cloth':
                        knotterSection.style.display = 'block';
                        weaverSection.style.display = 'block';
                        break;
                    case 'Knotted Liniwan':
                    case 'Knotted Bastos':
                        knotterSection.style.display = 'block';
                        break;
                    case 'Warped Silk':
                        warperSection.style.display = 'block';
                        break;
                }
                
                // Calculate and update time estimates
                console.log('Product details received:', productDetails);
                const estimates = calculateTimeEstimates(productDetails);
                console.log('Calculated estimates:', estimates);
                updateTimeEstimates(estimates);
            } else {
                console.error('Failed to fetch product details:', response.message);
                // Show error in the estimated time fields
                document.getElementById('knotter_estimated_time').textContent = 'Error calculating';
                document.getElementById('warper_estimated_time').textContent = 'Error calculating';
                document.getElementById('weaver_estimated_time').textContent = 'Error calculating';
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching product details:', error);
            // Show error in the estimated time fields
            document.getElementById('knotter_estimated_time').textContent = 'Error calculating';
            document.getElementById('warper_estimated_time').textContent = 'Error calculating';
            document.getElementById('weaver_estimated_time').textContent = 'Error calculating';
        }
    });
    
    // Initialize knotter management
    handleKnotterManagement();
    
    // Show the modal
    const modal = document.getElementById('taskAssignmentModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function trackProgress(prodLineId) {
    fetch(`backend/get_task_progress.php?prod_line_id=${prodLineId}`)
        .then(response => response.json())
        .then(data => {
            const content = document.getElementById('progressContent');
            let html = '<div class="space-y-6">';
            
            if (data.progress.length > 0) {
                let currentRole = '';
                
                data.progress.forEach(item => {
                    if (currentRole !== item.role) {
                        if (currentRole !== '') {
                            html += '</div>';
                        }
                        currentRole = item.role;
                        html += `
                            <div class="border-t pt-4 first:border-t-0 first:pt-0">
                                <h3 class="text-lg font-semibold mb-3">${item.role}</h3>
                        `;
                    }
                    
                    const statusClass = item.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                      item.status === 'In Progress' ? 'bg-blue-100 text-blue-800' :
                                      item.status === 'Completed' ? 'bg-green-100 text-green-800' :
                                      item.status === 'Declined' ? 'bg-red-100 text-red-800' :
                                      'bg-gray-100 text-gray-800';
                    
                    html += `
                        <div class="mb-4 last:mb-0">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-medium">${item.member_name}</p>
                                    <p class="text-sm text-gray-600">Last Update: ${item.last_update}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm font-medium ${statusClass}">
                                    ${item.status}
                                </span>
                            </div>
                            ${item.completion_percentage !== null ? `
                                <div class="relative pt-1">
                                    <div class="flex mb-2 items-center justify-between">
                                        <div>
                                            <span class="text-xs font-semibold inline-block text-blue-600">
                                                Progress
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs font-semibold inline-block text-blue-600">
                                                ${item.completion_percentage}%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
                                        <div style="width:${item.completion_percentage}%" 
                                             class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500">
                                        </div>
                                    </div>
                                </div>
                            ` : ''}
                            ${item.latest_note ? `
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600">Latest Update: ${item.latest_note}</p>
                                </div>
                            ` : ''}
                        </div>
                    `;
                });
                
                html += '</div>';
            } else {
                html += '<p class="text-center text-gray-600">No progress updates available.</p>';
            }
            
            html += '</div>';
            content.innerHTML = html;
            document.getElementById('trackProgressModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading progress data');
    });
}

function editProduct(prodLineId) {
    // Implement edit functionality
    Swal.fire({
        title: 'Coming Soon!',
        text: 'Edit feature is under development.',
        icon: 'info'
    });
}

function deleteProduct(prodLineId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'backend/delete_production_item.php',
                type: 'POST',
                data: {
                    prod_line_id: prodLineId
                },
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        if (result.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: result.message || 'Production item has been deleted.',
                                icon: 'success'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(result.message || 'Failed to delete production item');
                        }
                    } catch (error) {
                        Swal.fire({
                            title: 'Error!',
                            text: error.message || 'Failed to delete production item',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseText || 'Something went wrong',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

// Function to delete assigned task
function deleteAssignedTask(prodLineId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete all task assignments for this production item. This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'backend/end-points/delete_assigned_task.php',
                type: 'POST',
                data: { prod_line_id: prodLineId },
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        if (result.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: result.message || 'Task assignments have been deleted.',
                                icon: 'success'
                            }).then(() => {
                                // Refresh the assigned tasks table
                                loadTasks();
                            });
                        } else {
                            throw new Error(result.message || 'Failed to delete task assignments');
                        }
                    } catch (error) {
                        Swal.fire({
                            title: 'Error!',
                            text: error.message || 'Failed to delete task assignments',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseText || 'Something went wrong',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

// Function to load tasks via AJAX
function loadTasks() {
    $.ajax({
        url: 'backend/end-points/list_assigned_tasks.php',
        type: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            $('#assignedTasksTableBody').html(response);
        },
        error: function() {
            $('#assignedTasksTableBody').html('<tr><td colspan="4" class="py-3 px-6 text-center text-red-500">Error loading tasks. Please try again later.</td></tr>');
        }
    });
}

$(document).ready(function() {
    // Load tasks initially
    loadTasks();
    
    // Initialize modals
    const modal = document.getElementById('materialsModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeMaterialsModal();
            }
        });
    }

    // Search functionality
    $("#productSearchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#productTable tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Task search functionality
    $("#taskSearchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#assignedTasksTableBody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Status filter
    $("#statusFilter").on("change", function() {
        var value = $(this).val().toLowerCase();
        $("#assignedTasksTableBody tr").filter(function() {
            var status = $(this).find("td:eq(2)").text().toLowerCase();
            $(this).toggle(value === "" || status.includes(value));
        });
    });

    // Status update modal
    const statusModal = document.getElementById('statusModal');
    const closeStatusModalBtn = document.getElementById('closeStatusModal');
    const cancelStatusBtn = document.getElementById('cancelStatus');
    const updateStatusBtn = document.getElementById('updateStatus');

    // Update status button click handler
    $(document).on('click', '.updateStatusBtn', function() {
        const taskId = $(this).data('task-id');
        const currentStatus = $(this).data('current-status');
        
        $('#taskId').val(taskId);
        $('#newStatus').val(currentStatus);
        
        statusModal.classList.remove('hidden');
        statusModal.classList.add('flex');
    });

    // Close status modal
    closeStatusModalBtn.addEventListener('click', () => {
        statusModal.classList.remove('flex');
        statusModal.classList.add('hidden');
    });

    // Cancel status update
    cancelStatusBtn.addEventListener('click', () => {
        statusModal.classList.remove('flex');
        statusModal.classList.add('hidden');
    });

    // Reload tasks after status update
    updateStatusBtn.addEventListener('click', () => {
        const taskId = $('#taskId').val();
        const newStatus = $('#newStatus').val();

        $.ajax({
            url: 'backend/end-points/update_task_status.php',
            type: 'POST',
            data: {
                task_id: taskId,
                status: newStatus
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Status updated successfully!',
                        icon: 'success'
                    }).then(() => {
                        loadTasks(); // Reload tasks instead of full page refresh
                        statusModal.classList.remove('flex');
                        statusModal.classList.add('hidden');
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Failed to update status',
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong',
                    icon: 'error'
                });
            }
        });
    });

    // Update task assignment form submission
    $('#taskAssignmentForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const productDetails = JSON.parse(document.getElementById('product_details').value);
        const estimates = calculateTimeEstimates(productDetails);
        
        // Add estimated times to form data
        if (formData.has('knotter_id[]')) {
            formData.append('knotter_estimated_time', estimates.knotter);
        }
        if (formData.has('warper_id')) {
            formData.append('warper_estimated_time', estimates.warper);
        }
        if (formData.has('weaver_id')) {
            formData.append('weaver_estimated_time', estimates.weaver);
        }

        // Disable submit button
        const submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true);
        submitButton.text('Assigning...');

        $.ajax({
            url: 'backend/end-points/assign_tasks.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Close modal first
                    const taskModal = document.getElementById('taskAssignmentModal');
                    taskModal.classList.remove('flex');
                    taskModal.classList.add('hidden');
                    
                    // Reset form
                    $('#taskAssignmentForm')[0].reset();
                    
                    // Show success message
                    Swal.fire({
                        title: 'Success!',
                        text: response.message || 'Tasks assigned successfully!',
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Failed to assign tasks',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong while assigning tasks',
                    icon: 'error'
                });
            },
            complete: function() {
                // Re-enable submit button
                submitButton.prop('disabled', false);
                submitButton.text('Assign Tasks');
            }
        });
    });
});

// Product creation modal functionality
document.addEventListener('DOMContentLoaded', function() {
    const createBtn = document.getElementById('createProductBtn');
    const modal = document.getElementById('productFormModal');
    const closeButtons = document.querySelectorAll('.closeProductModal');
    const productForm = document.getElementById('productForm');
    const productNameSelect = document.getElementById('product_name');
    const dimensionsFields = document.getElementById('dimensionsFields');
    const weightField = document.getElementById('weightField');
    const lengthInput = document.getElementById('length');
    const widthInput = document.getElementById('width');
    const weightInput = document.getElementById('weight');

    // Function to toggle measurement fields based on product type
    function toggleMeasurementFields() {
        const selectedProduct = productNameSelect.value;
        const isDimensionsProduct = ['Piña Seda', 'Pure Piña Cloth'].includes(selectedProduct);
        
        dimensionsFields.classList.toggle('hidden', !isDimensionsProduct);
        weightField.classList.toggle('hidden', isDimensionsProduct);
        
        // Update required attributes
        lengthInput.required = isDimensionsProduct;
        widthInput.required = isDimensionsProduct;
        weightInput.required = !isDimensionsProduct;
        
        // Clear values when switching
        if (isDimensionsProduct) {
            weightInput.value = '';
        } else {
            lengthInput.value = '';
            widthInput.value = '';
        }
    }

    // Add event listener for product selection
    productNameSelect.addEventListener('change', toggleMeasurementFields);

    // Update table headers based on products
    function updateTableHeaders() {
        const dimensionsHeaders = document.querySelectorAll('.dimensions-header');
        const weightHeader = document.querySelector('.weight-header');
        const dimensionsCells = document.querySelectorAll('.dimensions-cell');
        const weightCells = document.querySelectorAll('.weight-cell');
        
        // Show all headers initially
        dimensionsHeaders.forEach(header => header.classList.remove('hidden'));
        weightHeader.classList.remove('hidden');
        
        // Show all cells initially
        dimensionsCells.forEach(cell => cell.classList.remove('hidden'));
        weightCells.forEach(cell => cell.classList.remove('hidden'));
    }

    // Call updateTableHeaders on page load
    updateTableHeaders();

    function resetFormAndModal() {
        productForm.reset();
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    createBtn.addEventListener('click', function() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    closeButtons.forEach(button => {
        button.addEventListener('click', resetFormAndModal);
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            resetFormAndModal();
        }
    });

    // Handle form submission
    productForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = productForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating...';

        const formData = new FormData(this);

        $.ajax({
            url: 'backend/create_production_item.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (result.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: result.message || 'Production item created successfully!',
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(result.message || 'Failed to create production item');
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Failed to create production item',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: xhr.responseText || 'Something went wrong',
                    icon: 'error'
                });
            },
            complete: function() {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
                resetFormAndModal();
            }
        });
    });
});

function showMaterialsModal(materials, productDetails) {
    const modal = document.getElementById('materialsModal');
    const content = document.getElementById('materialsContent');
    
    if (!modal || !content) {
        console.error('Modal elements not found');
        return;
    }

    let html = `
        <div class="mb-6 bg-white rounded-lg p-4 border">
            <h2 class="font-semibold text-xl mb-3">${productDetails.name}</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">`;

    // Add dimensions or weight based on product type
    if (productDetails.length && productDetails.width) {
        html += `
            <div>
                <span class="text-gray-600">Dimensions:</span>
                <span class="font-medium">${productDetails.length}m × ${productDetails.width}m</span>
            </div>`;
    } else if (productDetails.weight) {
        html += `
            <div>
                <span class="text-gray-600">Weight:</span>
                <span class="font-medium">${productDetails.weight}g</span>
            </div>`;
    }

    html += `
            <div>
                <span class="text-gray-600">Quantity:</span>
                <span class="font-medium">${productDetails.quantity} piece${productDetails.quantity > 1 ? 's' : ''}</span>
            </div>
            </div>
        </div>
        <div class="space-y-4">`;

    if (Array.isArray(materials) && materials.length > 0) {
        materials.forEach(material => {
            html += `
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-medium text-lg">${material.name}</h3>
                            ${material.category ? 
                            `<p class="text-gray-500 text-sm">Category: ${material.category}</p>` : 
                            ''}
                        </div>
                        <span class="text-blue-600 font-medium text-lg">${Math.round(material.amount)}g</span>
                    </div>
                </div>`;
        });
    } else {
        html += `
            <div class="text-center text-gray-500 py-4">
                No materials data available
            </div>`;
    }

    html += '</div>';
    content.innerHTML = html;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeMaterialsModal() {
    const modal = document.getElementById('materialsModal');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('materialsModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeMaterialsModal();
            }
        });
    }
});

// Add this function to handle multiple knotters
let knotterManagementInitialized = false;

function handleKnotterManagement() {
    console.log('handleKnotterManagement function called');
    
    // Prevent multiple initializations
    if (knotterManagementInitialized) {
        console.log('Knotter management already initialized');
        return;
    }
    
    const addKnotterBtn = document.getElementById('addKnotterBtn');
    const knotterSelections = document.getElementById('knotterSelections');
    
    console.log('Found addKnotterBtn:', addKnotterBtn);
    console.log('Found knotterSelections:', knotterSelections);

    if (!addKnotterBtn || !knotterSelections) {
        console.error('Required elements not found');
        return;
    }

    // Add knotter button click handler
    addKnotterBtn.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent any default button behavior
        console.log('Add Knotter button clicked');
        const newSelection = knotterSelections.querySelector('.knotter-selection').cloneNode(true);
        const select = newSelection.querySelector('select');
        select.value = ''; // Reset selection
        
        // Show remove button
        const removeBtn = newSelection.querySelector('.remove-knotter');
        removeBtn.classList.remove('hidden');
        
        knotterSelections.appendChild(newSelection);
        console.log('New knotter selection added');
        
        // Load members for the new select
        loadMembers('knotter', select);
        
        // Get product details and update time estimates
        const productDetailsStr = document.getElementById('product_details').value;
        if (productDetailsStr) {
            try {
                const productDetails = JSON.parse(productDetailsStr);
                updateEstimatesForKnotters(productDetails);
            } catch (e) {
                console.error('Error parsing product details:', e);
            }
        }
    });

    // Remove knotter button click handler
    knotterSelections.addEventListener('click', function(e) {
        if (e.target.closest('.remove-knotter')) {
            console.log('Remove knotter button clicked');
            const selection = e.target.closest('.knotter-selection');
            selection.remove();
            console.log('Knotter selection removed');
            
            // Get product details and update time estimates
            const productDetailsStr = document.getElementById('product_details').value;
            if (productDetailsStr) {
                try {
                    const productDetails = JSON.parse(productDetailsStr);
                    updateEstimatesForKnotters(productDetails);
                } catch (e) {
                    console.error('Error parsing product details:', e);
                }
            }
        }
    });
    
    knotterManagementInitialized = true;
    console.log('Knotter management event listeners initialized');
}

// Function to update estimates based on number of knotters
function updateEstimatesForKnotters(productDetails) {
    const baseEstimates = calculateTimeEstimates(productDetails);
    const numKnotters = document.querySelectorAll('.knotter-selection').length;
    
    console.log('Updating estimates for', numKnotters, 'knotters');
    console.log('Base estimates:', baseEstimates);
    
    // Only adjust knotter time if there are knotters and knotter time exists
    if (numKnotters > 0 && baseEstimates.knotter > 0) {
        // Calculate adjusted time per knotter
        const adjustedKnotterTime = Math.ceil(baseEstimates.knotter / numKnotters);
        baseEstimates.knotter = adjustedKnotterTime;
        console.log('Adjusted knotter time per person:', adjustedKnotterTime);
    }
    
    // Update time estimates and deadlines
    updateTimeEstimates(baseEstimates);
    
    // Update the knotter deadline based on the adjusted time
    const today = new Date();
    if (baseEstimates.knotter > 0) {
        const knotterDeadline = new Date(today.getTime() + (baseEstimates.knotter * 24 * 60 * 60 * 1000));
        const knotterInput = document.getElementById('knotter_deadline');
        if (knotterInput) {
            knotterInput.min = knotterDeadline.toISOString().split('T')[0];
            knotterInput.value = knotterDeadline.toISOString().split('T')[0];
        }
    }
}

// Function to show product details
function showDetails(details) {
    const detailsModal = document.getElementById('detailsModal');
    const detailsContent = document.getElementById('detailsContent');
    
    let content = `
        <div class="space-y-3">
            <div>
                <span class="font-semibold">Product:</span> ${details.product_name}
            </div>`;
    
    if (details.length && details.width) {
        content += `
            <div>
                <span class="font-semibold">Length:</span> ${details.length} m
            </div>
            <div>
                <span class="font-semibold">Width:</span> ${details.width} m
            </div>`;
    }
    
    if (details.weight) {
        content += `
            <div>
                <span class="font-semibold">Weight:</span> ${details.weight} g
            </div>`;
    }
    
    content += `
        <div>
            <span class="font-semibold">Quantity:</span> ${details.quantity}
        </div>
    </div>`;
    
    detailsContent.innerHTML = content;
    detailsModal.classList.remove('hidden');
    detailsModal.classList.add('flex');
}

// Function to view assigned members
function viewAssignedMembers(prodLineId) {
    console.log('Fetching assigned members for production line ID:', prodLineId);
    
    fetch(`backend/get_assigned_members.php?prod_line_id=${prodLineId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            const content = document.getElementById('membersContent');
            let html = '<div class="space-y-4">';
            
            if (data.members && data.members.length > 0) {
                data.members.forEach(member => {
                    const statusClass = member.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                      member.status === 'In Progress' ? 'bg-blue-100 text-blue-800' :
                                      member.status === 'Completed' ? 'bg-green-100 text-green-800' :
                                      member.status === 'Declined' ? 'bg-red-100 text-red-800' :
                                      'bg-gray-100 text-gray-800';
                    
                    html += `
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-lg font-semibold">${member.name}</h4>
                                    <p class="text-gray-600">Role: ${member.role}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm font-medium ${statusClass}">
                                    ${member.status}
                                </span>
                            </div>
                            <p class="text-gray-600 mt-2">Deadline: ${member.deadline}</p>
                        </div>
                    `;
                });
            } else {
                html += '<p class="text-center text-gray-600">No members assigned yet.</p>';
            }
            
            html += '</div>';
            content.innerHTML = html;
            document.getElementById('membersModal').classList.remove('hidden');
            document.getElementById('membersModal').classList.add('flex');
        })
        .catch(error => {
            console.error('Error fetching assigned members:', error);
            const content = document.getElementById('membersContent');
            content.innerHTML = `
                <div class="text-center text-red-600">
                    <p>Error loading assigned members.</p>
                    <p class="text-sm mt-2">Please try again later or contact support if the problem persists.</p>
                </div>
            `;
            document.getElementById('membersModal').classList.remove('hidden');
        });
}

function confirmTaskCompletion(prodLineId, memberId, role, isConfirmed) {
    const action = isConfirmed ? 'verify' : 'reject';
    Swal.fire({
        title: `${isConfirmed ? 'Verify' : 'Reject'} Task Completion`,
        text: `Are you sure you want to ${action} the completion of this task?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isConfirmed ? '#10B981' : '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: isConfirmed ? 'Yes, Verify' : 'Yes, Reject',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('backend/admin_confirm_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    prod_line_id: prodLineId,
                    member_id: memberId,
                    role: role,
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        // Refresh the assigned members view
                        viewAssignedMembers(prodLineId);
                    });
                } else {
                    throw new Error(data.message || 'Failed to update task status');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error'
                });
            });
        }
    });
}

// Add event listeners for closing modals
document.querySelector('.closeViewAssignedModal').addEventListener('click', () => {
    document.getElementById('viewAssignedMemberModal').classList.add('hidden');
});

// Modal close handlers
document.addEventListener('DOMContentLoaded', function() {
    // Details modal
    const detailsModal = document.getElementById('detailsModal');
    const closeDetailsModal = document.getElementById('closeDetailsModal');
    const closeDetailsBtn = document.getElementById('closeDetailsBtn');
    
    function closeDetails() {
        detailsModal.classList.remove('flex');
        detailsModal.classList.add('hidden');
    }
    
    closeDetailsModal.addEventListener('click', closeDetails);
    closeDetailsBtn.addEventListener('click', closeDetails);
    
    // Members modal
    const membersModal = document.getElementById('membersModal');
    const closeMembersModal = document.getElementById('closeMembersModal');
    const closeMembersBtn = document.getElementById('closeMembersBtn');
    
    function closeMembers() {
        membersModal.classList.remove('flex');
        membersModal.classList.add('hidden');
        // Restore body scrolling
        document.body.style.overflow = '';
    }
    
    closeMembersModal.addEventListener('click', closeMembers);
    closeMembersBtn.addEventListener('click', closeMembers);
    
    // Close modal when clicking outside
    membersModal.addEventListener('click', function(e) {
        if (e.target === membersModal) {
            closeMembers();
        }
    });
});
</script>

<style>
    select option[value=""] {
        display: none;
    }
    
    select option {
        color: #374151;
        padding: 8px;
    }

    select:focus option:checked {
        background: linear-gradient(0deg, #4F46E5 0%, #4F46E5 100%);
        color: white;
    }

    .knotter-selection {
        position: relative;
        width: 100%;
    }

    .knotter-select {
        width: 100% !important;
        min-width: 250px !important;
        max-width: 100% !important;
    }

    .remove-knotter {
        min-width: 80px;
        flex-shrink: 0;
    }

    #knotterSelections {
        max-width: 100%;
    }

    .flex.items-center.gap-2 {
        flex-wrap: nowrap;
        width: 100%;
        align-items: center;
    }

    .relative.flex-grow {
        flex: 1;
        min-width: 0;
    }

    /* Ensure consistent appearance across all select elements */
    select.form-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-color: white;
    }

    /* Add these styles for table alignment */
    #tasksTable td {
        text-align: center;
        vertical-align: middle;
    }

    #tasksTable td .flex.space-x-2 {
        justify-content: center;
    }

    /* Make status badge centered */
    #tasksTable td .rounded-full {
        display: inline-block;
    }

    /* Add these styles for modal backdrop */
    #membersModal::before {
        content: '';
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: -1;
    }

    /* Ensure content behind modal is not interactive */
    #membersModal.flex ~ * {
        pointer-events: none;
        user-select: none;
    }

    /* Make modal content interactive */
    #membersModal .bg-white {
        pointer-events: auto;
        user-select: auto;
    }
</style>
