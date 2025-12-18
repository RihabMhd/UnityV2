<?php include("./includes/header.php"); ?>

<div class="container" style="max-width: 600px; margin: 30px auto; background: #336699; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
    
    <?php if (isset($error)): ?>
        <div style="color: #ff6b6b; margin-bottom: 20px; padding: 10px; background-color: #3d2020; border: 1px solid #5a2a2a; border-radius: 4px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <h2 style="color: #DAF7DC; margin-bottom: 20px;"><?php echo __('Edit Doctor'); ?></h2>
    
    <?php if ($data): ?>
        <form method="POST" action="">
            <div style="margin-bottom: 20px;">
                <label for="first_name" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                    <?php echo __('First Name'); ?> <?php echo __('*'); ?>
                </label>
                <input type="text" id="first_name" name="first_name" 
                       value="<?php echo htmlspecialchars($data['first_name']); ?>" 
                       required style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="last_name" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                    <?php echo __('Last Name'); ?> <?php echo __('*'); ?>
                </label>
                <input type="text" id="last_name" name="last_name" 
                       value="<?php echo htmlspecialchars($data['last_name']); ?>" 
                       required style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="specialization" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                    <?php echo __('Specialization'); ?> <?php echo __('*'); ?>
                </label>
                <input type="text" id="specialization" name="specialization" 
                       value="<?php echo htmlspecialchars($data['specialization']); ?>" 
                       required style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="phone_number" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                    <?php echo __('Phone'); ?> <?php echo __('*'); ?>
                </label>
                <input type="tel" id="phone_number" name="phone_number" 
                       value="<?php echo htmlspecialchars($data['phone_number']); ?>" 
                       required style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                    <?php echo __('Email'); ?> <?php echo __('*'); ?>
                </label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($data['email']); ?>" 
                       required style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="department_id" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                    <?php echo __('Department'); ?> <?php echo __('*'); ?>
                </label>
                <select id="department_id" name="department_id" required 
                        style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
                    <option value=""><?php echo __('Select Department'); ?></option>
                    <?php if ($departments && $departments->num_rows > 0): ?>
                        <?php while ($dept = $departments->fetch_assoc()): ?>
                            <option value="<?php echo $dept['department_id']; ?>"
                                    <?php echo ($data['department_id'] == $dept['department_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept['department_name']) . ' - ' . htmlspecialchars($dept['location']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
                     
            <div>
                <button type="submit" 
                        style="padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-right: 10px; background-color: #9EE493; color: #2F4858; font-weight: bold;">
                    <?php echo __('Update Doctor'); ?>
                </button>
                <a href="index.php?controller=doctors" 
                   style="padding: 12px 30px; border: none; border-radius: 4px; font-size: 16px; background-color: #86BBD8; color: #2F4858; text-decoration: none; display: inline-block; font-weight: bold;">
                    <?php echo __('Cancel'); ?>
                </a>
            </div>
        </form>
    <?php else: ?>
        <div style="color: #ff6b6b; margin-bottom: 20px; padding: 10px; background-color: #3d2020; border: 1px solid #5a2a2a; border-radius: 4px;">
            <?php echo __('Doctor not found'); ?>
        </div>
        <a href="index.php?controller=doctors" 
           style="padding: 12px 30px; border: none; border-radius: 4px; font-size: 16px; background-color: #6c757d; color: white; text-decoration: none; display: inline-block;">
            <?php echo __('Back to List'); ?>
        </a>
    <?php endif; ?>
</div>

<style>
    input:focus, 
    select:focus {
        outline: none;
        border-color: #9EE493 !important;
    }

    select option {
        background-color: #2F4858;
        color: #DAF7DC;
    }
</style>

<?php include("./includes/footer.php"); ?>