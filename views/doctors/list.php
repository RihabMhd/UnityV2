<?php include("./includes/header.php"); ?>

<div class="container" style="margin-top: 20px;">
    <div style="background: #336699; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #DAF7DC;"><?php echo __('Doctors Management'); ?></h2>
            <a href="index.php?controller=doctors&action=create"
                style="padding: 10px 20px; background-color: #8ee481ff; color: #ffffffff; text-decoration: none; border-radius: 4px; font-weight: bold;">
                <i class="fa-regular fa-square-plus fa-beat"></i>
            </a>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div style="color: #ff6b6b; margin-bottom: 20px; padding: 10px; background-color: #3d2020; border: 1px solid #5a2a2a; border-radius: 4px;">
                <?php echo __('An error occurred. Please try again'); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div style="color: #9EE493; margin-bottom: 20px; padding: 10px; background-color: #2F4858; border: 1px solid #9EE493; border-radius: 4px;">
                <?php echo __('Operation completed successfully'); ?>
            </div>
        <?php endif; ?>

        <table id="doctorsTable" class="display" style="width: 100%;">
            <thead>
                <tr>
                    <th><?php echo __('ID'); ?></th>
                    <th><?php echo __('First Name'); ?></th>
                    <th><?php echo __('Last Name'); ?></th>
                    <th><?php echo __('Speciality'); ?></th>
                    <th><?php echo __('Phone'); ?></th>
                    <th><?php echo __('Email'); ?></th>
                    <th><?php echo __('Department'); ?></th>
                    <th><?php echo __('Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($doctors && $doctors->num_rows > 0): ?>
                    <?php while ($row = $doctors->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['doctor_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                            <td><?php echo __(htmlspecialchars($row['specialization'])); ?></td>
                            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo __(htmlspecialchars($row['department_name'] ?? 'No Department')); ?></td>
                            <td>
                                <a href="index.php?controller=doctors&action=edit&id=<?php echo $row['doctor_id']; ?>"
                                    style="padding: 5px 10px; background-color: #86BBD8; color: #2F4858; text-decoration: none; border-radius: 3px; margin-right: 5px; font-size: 12px; font-weight: bold;">
                                    <i class="fa-solid fa-user-pen"></i>
                                </a>
                                <a href="index.php?controller=doctors&action=delete&id=<?php echo $row['doctor_id']; ?>"
                                    style="padding: 5px 10px; background-color: #e74c3c; color: white; text-decoration: none; border-radius: 3px; font-size: 12px; font-weight: bold;"
                                    onclick="return confirm('<?php echo __('Are you sure you want to delete this doctor?'); ?>')">
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
        $('#doctorsTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?php echo __('All'); ?>"]
            ],
            order: [
                [0, 'desc']
            ],
            language: {
                search: "<?php echo __('Search doctors'); ?>:",
                lengthMenu: "<?php echo __('Show'); ?> _MENU_",
                info: "<?php echo __('Showing'); ?> _START_ <?php echo __('to'); ?> _END_ <?php echo __('of'); ?> _TOTAL_ <?php echo __('doctors'); ?>",
                infoEmpty: "<?php echo __('No doctors found'); ?>",
                infoFiltered: "(<?php echo __('filtered from'); ?> _MAX_ <?php echo __('total doctors'); ?>)",
                paginate: {
                    first: "<?php echo __('First'); ?>",
                    last: "<?php echo __('Last'); ?>",
                    next: "<?php echo __('Next'); ?>",
                    previous: "<?php echo __('Previous'); ?>"
                },
                zeroRecords: "<?php echo __('No matching doctors found'); ?>"
            },
            columnDefs: [{
                targets: -1,
                orderable: false,
                searchable: false
            }]
        });
    });
</script>

<?php include("./includes/footer.php"); ?>