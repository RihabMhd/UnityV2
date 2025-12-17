<?php include("./includes/header.php"); ?>

<div class="container" style="margin-top: 20px;">
    <div style="background: #336699; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #DAF7DC;">Patients Management</h2>
            <a href="index.php?controller=patients&action=create" style="padding: 10px 20px; background-color: #8ee481ff; color: #ffffffff; text-decoration: none; border-radius: 4px; font-weight: bold;">
                <i class="fa-regular fa-square-plus fa-beat"></i>
            </a>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div style="color: #ff6b6b; margin-bottom: 20px; padding: 10px; background-color: #3d2020; border: 1px solid #5a2a2a; border-radius: 4px;">
                An error occurred. Please try again.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div style="color: #9EE493; margin-bottom: 20px; padding: 10px; background-color: #2F4858; border: 1px solid #9EE493; border-radius: 4px;">
                Operation completed successfully!
            </div>
        <?php endif; ?>

        <table id="patientsTable" class="display" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($patients && $patients->num_rows > 0): ?>
                    <?php while ($row = $patients->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['patient_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <a href="index.php?controller=patients&action=edit&id=<?php echo $row['patient_id']; ?>"
                                    style="padding: 5px 10px; background-color: #86BBD8; color: #2F4858; text-decoration: none; border-radius: 3px; margin-right: 5px; font-size: 12px; font-weight: bold;">
                                    <i class="fa-solid fa-user-pen"></i>
                                </a>
                                <a href="index.php?controller=patients&action=delete&id=<?php echo $row['patient_id']; ?>"
                                    style="padding: 5px 10px; background-color: #e74c3c; color: white; text-decoration: none; border-radius: 3px; font-size: 12px; font-weight: bold;"
                                    onclick="return confirm('Are you sure you want to delete this patient?')">
                                    <i class="fa-solid fa-user-xmark"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
    $('#patientsTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[0, 'desc']], 
        language: {
            search: "Search patients:",
            lengthMenu: "Show _MENU_",
            info: "Showing _START_ to _END_ of _TOTAL_ patients",
            infoEmpty: "No patients found",
            infoFiltered: "(filtered from _MAX_ total patients)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            },
            zeroRecords: "No matching patients found"
        },
        columnDefs: [
            {
                targets: -1,
                orderable: false,
                searchable: false
            }
        ]
    });
});
</script>


<?php include("./includes/footer.php"); ?>