<?php 
$fetch_verified_members = $db->fetch_members_by_status(1);

if ($fetch_verified_members->num_rows > 0) {
    while ($row = $fetch_verified_members->fetch_assoc()) {
?>
    <tr class="border-b border-gray-200 hover:bg-gray-50">
        <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row['umid_number']); ?></td>
        <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row['umfullname']); ?></td>
        <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row['umemail']); ?></td>
        <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row['umphone']); ?></td>
        <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row['umrole']); ?></td>
        <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($row['umsex']); ?></td>
        <td class="py-3 px-6 text-left">Verified</td>
        <td class="py-3 px-6">
            <button 
                class="removeBtn bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded-full text-xs flex items-center shadow"
                data-id="<?php echo htmlspecialchars($row['umid']); ?>" 
                data-name="<?php echo htmlspecialchars($row['umfullname']); ?>">
                <span class="material-icons text-sm mr-1">delete</span> Remove
            </button>
        </td>
    </tr>
<?php
    }
} else {
?>
    <tr>
        <td colspan="8" class="py-3 px-6 text-center">No verified members found.</td>
    </tr>
<?php
}
?> 