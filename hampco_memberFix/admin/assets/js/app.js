// Function to load members for a specific role
async function loadMembers(role, selectElement) {
    try {
        if (!selectElement) {
            console.error('Select element is null for role:', role);
            return;
        }

        const formData = new FormData();
        formData.append('role', role);

        console.log('Fetching members for role:', role);
        console.log('Using select element:', selectElement);
        console.log('Select element name:', selectElement.getAttribute('name'));
        
        const response = await fetch('backend/end-points/get_verified_members.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Raw response data for ' + role + ':', data);
        
        if (data.error) {
            console.error('Server error:', data.error);
            throw new Error(data.error);
        }

        // Clear existing options except the default one
        const defaultOption = '<option value="" disabled selected hidden>Select a ' + role.charAt(0).toUpperCase() + role.slice(1) + '</option>';
        console.log('Setting default option:', defaultOption);
        selectElement.innerHTML = defaultOption;

        // Add new options
        if (Array.isArray(data) && data.length > 0) {
            console.log(`Adding ${data.length} options for ${role}`);
            data.forEach(member => {
                console.log('Creating option for member:', member);
                const option = document.createElement('option');
                option.value = member.id;
                option.textContent = member.name;
                console.log('Option created:', { value: option.value, text: option.textContent });
                selectElement.appendChild(option);
            });
            console.log(`Loaded ${data.length} members for role: ${role}`);
            console.log('Final select element HTML:', selectElement.innerHTML);
        } else {
            console.log(`No members found for role: ${role}`);
            // Add a disabled option to show no members available
            const option = document.createElement('option');
            option.disabled = true;
            option.textContent = `No ${role}s available`;
            selectElement.appendChild(option);
        }
    } catch (error) {
        console.error('Error in loadMembers:', error);
        console.error('Role:', role);
        console.error('Select element:', selectElement);
        if (selectElement) {
            // Add a disabled option to show the error
            selectElement.innerHTML = `<option disabled selected>Error loading ${role}s</option>`;
        }
    }
}

// Function to handle task assignment
function assignTask(prodLineId) {
    console.log('Assigning task for production line ID:', prodLineId);
    
    // Set the production line ID in the form
    document.getElementById('identifier').value = prodLineId;
    
    // Get select elements
    const knotterSelects = document.querySelectorAll('.knotter-select');
    const warperSelect = document.querySelector('select[name="warper_id"]');
    const weaverSelect = document.querySelector('select[name="weaver_id"]');
    
    console.log('Found knotter selects:', knotterSelects);
    console.log('Found warper select:', warperSelect);
    console.log('Found weaver select:', weaverSelect);
    
    // Load members for each role
    knotterSelects.forEach(select => loadMembers('knotter', select));
    if (warperSelect) loadMembers('warper', warperSelect);
    if (weaverSelect) loadMembers('weaver', weaverSelect);
    
    // Show the modal
    const modal = document.getElementById('taskAssignmentModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// Event listeners for modal controls
document.addEventListener('DOMContentLoaded', function() {
    const taskModal = document.getElementById('taskAssignmentModal');
    const closeTaskModal = document.getElementById('closeTaskModal');
    const cancelTaskBtn = document.getElementById('cancelTaskAssignment');
    const taskForm = document.getElementById('taskAssignmentForm');

    // Close modal function
    function closeTaskAssignmentModal() {
        taskModal.classList.remove('flex');
        taskModal.classList.add('hidden');
        // Reset form
        taskForm.reset();
    }

    // Close button click handler
    closeTaskModal.addEventListener('click', closeTaskAssignmentModal);
    cancelTaskBtn.addEventListener('click', closeTaskAssignmentModal);

    // Form submission handler
    taskForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(taskForm);
        const submitButton = taskForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Assigning...';

        try {
            const response = await fetch('backend/end-points/assign_tasks.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    title: 'Success!',
                    text: result.message || 'Tasks assigned successfully!',
                    icon: 'success'
                }).then(() => {
                    closeTaskAssignmentModal();
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: result.message || 'Failed to assign tasks. Please try again.',
                    icon: 'error'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to assign tasks. Please try again.',
                icon: 'error'
            });
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Assign Tasks';
        }
    });
});

// Product creation modal functionality
document.addEventListener('DOMContentLoaded', function() {
    const createBtn = document.getElementById('createProductBtn');
    const modal = document.getElementById('productFormModal');
    const closeBtn = document.getElementById('closeModal');
    const productForm = document.getElementById('productForm');

    createBtn.addEventListener('click', function() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    closeBtn.addEventListener('click', function() {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        // Reset form
        productForm.reset();
    });

    // Product form submission handler
    productForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Creating...';

        try {
            const formData = new FormData(this);
            const response = await fetch('backend/create_production_item.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            console.log('Server response:', result);

            if (result.success) {
                // Format materials used message
                let materialsMessage = '';
                if (result.materials_used && result.materials_used.length > 0) {
                    materialsMessage = '<br><br>Raw materials used:<ul class="mt-2 list-disc pl-5">';
                    result.materials_used.forEach(material => {
                        materialsMessage += `<li>${material.name}${material.category ? ` (${material.category})` : ''}: ${material.amount.toFixed(2)} grams</li>`;
                    });
                    materialsMessage += '</ul>';
                }

                Swal.fire({
                    title: 'Success!',
                    html: `${result.message}${materialsMessage}`,
                    icon: 'success'
                }).then(() => {
                    // Close modal and reset form
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                    productForm.reset();
                    
                    // Reload the page to show the new item
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: result.message || 'Failed to create production item. Please try again.',
                    icon: 'error'
                });
                console.error('Error details:', result.debug_info);
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to create production item. Please try again.',
                icon: 'error'
            });
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        }
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            // Reset form
            productForm.reset();
        }
    });
});
