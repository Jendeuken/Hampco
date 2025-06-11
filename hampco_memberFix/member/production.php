<?php 
include "components/header.php";

// Fetch assigned tasks for the current member
$member_id = $On_Session[0]['id'];
$query = "SELECT ta.*, pl.product_name, pl.length_m as length, pl.width_m as width, pl.weight_g as weight, pl.quantity,
          um.fullname as member_name 
          FROM task_assignments ta 
          JOIN production_line pl ON ta.prod_line_id = pl.prod_line_id 
          JOIN user_member um ON ta.member_id = um.id
          WHERE ta.member_id = $member_id 
          ORDER BY ta.created_at DESC";
$result = mysqli_query($db->conn, $query);

// Fetch raw materials for each product
function getRawMaterials($db, $product_name, $role) {
    $query = "SELECT DISTINCT 
                     CASE 
                         WHEN rm.raw_materials_name = 'Piña Loose' AND prm.raw_material_category = 'Bastos' AND ? = 'weaver' THEN 'Knotted Bastos'
                         WHEN rm.raw_materials_name = 'Silk' AND ? = 'weaver' THEN 'Warped Silk'
                         ELSE rm.raw_materials_name
                     END as name,
                     prm.consumption_rate as quantity,
                     prm.consumption_unit as unit,
                     prm.raw_material_category as category
              FROM product_raw_materials prm 
              JOIN raw_materials rm ON rm.raw_materials_name = prm.raw_material_name 
              WHERE prm.product_name = ?";
              
    // Add role-specific filters
    if ($role === 'knotter') {
        $query .= " AND prm.raw_material_name = 'Piña Loose' AND prm.raw_material_category = 'Bastos'";
    } elseif ($role === 'warper') {
        $query .= " AND prm.raw_material_name = 'Silk'";
    }
    
    $query .= " GROUP BY rm.raw_materials_name, prm.raw_material_category";
    
    $stmt = mysqli_prepare($db->conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $role, $role, $product_name);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}
?>

<!-- Top bar with user profile -->
<div class="max-w-12xl mx-auto flex justify-between items-center bg-white p-4 mb-6 rounded-md shadow-md">
    <h2 class="text-lg font-semibold text-gray-700">Production</h2>
</div>

<?php 
if($On_Session[0]['status']==1){ 
?>

<!-- Production Tasks Section -->
<div class="bg-white rounded-md shadow-md p-6">
    <h3 class="text-xl font-semibold text-gray-700 mb-6">Assigned Tasks</h3>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="grid gap-6">
            <?php while($task = mysqli_fetch_assoc($result)): ?>
                <div class="border rounded-lg p-4 <?php echo $task['status'] === 'Pending' ? 'bg-yellow-50' : ($task['status'] === 'In Progress' ? 'bg-blue-50' : 'bg-green-50'); ?>">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="text-lg font-semibold"><?php echo htmlspecialchars($task['product_name']); ?></h4>
                            <p class="text-gray-600">Task: <?php 
                                $roleTask = '';
                                switch($task['role']) {
                                    case 'knotter':
                                        $roleTask = 'Knotting';
                                        break;
                                    case 'warper':
                                        $roleTask = 'Warping';
                                        break;
                                    case 'weaver':
                                        $roleTask = 'Weaving';
                                        break;
                                    default:
                                        $roleTask = ucfirst($task['role']);
                                }
                                echo htmlspecialchars($roleTask); 
                            ?></p>
                            <p class="text-gray-600">Assigned to: <?php echo htmlspecialchars($task['member_name']); ?></p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                <?php echo $task['status'] === 'Pending' ? 'bg-yellow-200 text-yellow-800' : 
                                           ($task['status'] === 'In Progress' ? 'bg-blue-200 text-blue-800' : 
                                           'bg-green-200 text-green-800'); ?>">
                                <?php echo $task['status']; ?>
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <h5 class="font-medium mb-2">Measurements:</h5>
                            <div class="space-y-1">
                                <?php if($task['length'] && $task['width']): ?>
                                    <p>Length: <?php echo $task['length']; ?>m</p>
                                    <p>Width: <?php echo $task['width']; ?>m</p>
                                <?php endif; ?>
                                <?php if($task['weight']): ?>
                                    <p>Weight: <?php echo $task['weight']; ?>g</p>
                                <?php endif; ?>
                                <p>Quantity: <?php echo $task['quantity']; ?></p>
                            </div>
                        </div>
                        <div>
                            <h5 class="font-medium mb-2">Raw Materials to Use:</h5>
                            <div class="space-y-1">
                                <?php 
                                $raw_materials = getRawMaterials($db, $task['product_name'], $task['role']);
                                while($material = mysqli_fetch_assoc($raw_materials)): 
                                    // Calculate total quantity needed based on area
                                    $area = floatval($task['length']) * floatval($task['width']) * floatval($task['quantity']);
                                    $total_quantity = round($area * floatval($material['quantity']));
                                ?>
                                    <p class="text-gray-700">
                                        <?php echo htmlspecialchars($material['name']); ?>
                                        <?php if($material['category'] && $material['name'] !== 'Knotted Bastos'): ?>
                                            (<?php echo htmlspecialchars($material['category']); ?>)
                                        <?php endif; ?>:
                                        <span class="font-medium"><?php echo $total_quantity; ?>g</span>
                                    </p>
                                <?php endwhile; ?>
                                <p class="text-sm text-gray-500 mt-2">* Only showing materials relevant to your role</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <p class="text-gray-600">
                            Deadline: <?php echo date('F j, Y', strtotime($task['deadline'])); ?>
                        </p>
                        
                        <div class="space-x-2">
                            <?php if($task['status'] === 'Pending'): ?>
                                <button onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'In Progress')" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Accept Task
                                </button>
                            <?php elseif($task['status'] === 'In Progress'): ?>
                                <button onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'Completed')"
                                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Mark as Completed
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
    <div class="text-center py-8">
            <p class="text-gray-600">No tasks assigned yet.</p>
        </div>
    <?php endif; ?>
</div>

<?php 
} else {
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

<?php include "components/footer.php"; ?>

<!-- Add JavaScript for handling task actions -->
<script>
function updateTaskStatus(taskId, status) {
    Swal.fire({
        title: status === 'In Progress' ? 'Accept Task?' : 'Complete Task?',
        html: `Are you sure you want to ${status === 'In Progress' ? 'accept' : 'complete'} this task?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: status === 'In Progress' ? 'Yes, Accept it!' : 'Yes, Complete it!',
        cancelButtonText: 'Cancel',
        background: '#fff',
        customClass: {
            confirmButton: 'bg-blue-500 hover:bg-blue-600 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 mx-2',
            cancelButton: 'bg-red-500 hover:bg-red-600 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 mx-2',
            popup: 'rounded-xl shadow-2xl',
            title: 'text-xl font-semibold text-gray-800',
            content: 'text-gray-600'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('backend/update_task_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    taskId: taskId,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: `Task ${status === 'In Progress' ? 'accepted' : 'completed'} successfully!`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-xl shadow-2xl'
                        }
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error updating task status: ' + data.message,
                        icon: 'error',
                        customClass: {
                            popup: 'rounded-xl shadow-2xl'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Error updating task status',
                    icon: 'error',
                    customClass: {
                        popup: 'rounded-xl shadow-2xl'
                    }
                });
            });
        }
    });
}
</script>

