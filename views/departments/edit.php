<?php include("./includes/header.php"); ?>

<div class="container" style="max-width: 600px; margin: 30px auto; background: #336699; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
    
    <?php if (isset($error)): ?>
        <div style="color: #ff6b6b; margin-bottom: 20px; padding: 10px; background-color: #3d2020; border: 1px solid #5a2a2a; border-radius: 4px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($data): ?>
        <form method="POST" action="">
            <div style="margin-bottom: 20px;">
                <label for="department_name" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                    <?php echo __('Department Name'); ?> <?php echo __('*'); ?>
                </label>
                <input type="text" id="department_name" name="department_name" 
                       value="<?php echo htmlspecialchars($data['department_name']); ?>" required 
                       style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="location" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                    <?php echo __('Location'); ?> <?php echo __('*'); ?>
                </label>
                <input type="text" id="location" name="location" 
                       value="<?php echo htmlspecialchars($data['location']); ?>" required 
                       style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
            </div>
                     
            <div>
                <button type="submit" 
                        style="padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-right: 10px; background-color: #9EE493; color: #2F4858; font-weight: bold;">
                    <?php echo __('Update Department'); ?>
                </button>
                <a href="index.php" 
                   style="padding: 12px 30px; border: none; border-radius: 4px; font-size: 16px; background-color: #86BBD8; color: #2F4858; text-decoration: none; display: inline-block; font-weight: bold;">
                    <?php echo __('Cancel'); ?>
                </a>
            </div>
        </form>
    <?php else: ?>
        <div style="color: #ff6b6b; margin-bottom: 20px; padding: 10px; background-color: #3d2020; border: 1px solid #5a2a2a; border-radius: 4px;">
            <?php echo __('Department not found'); ?>
        </div>
        <a href="index.php" 
           style="padding: 12px 30px; border: none; border-radius: 4px; font-size: 16px; background-color: #6c757d; color: white; text-decoration: none; display: inline-block;">
            <?php echo __('Back to List'); ?>
        </a>
    <?php endif; ?>
</div>

<style>
    input:focus, select:focus {
        outline: none;
        border-color: #9EE493 !important;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }
</style>

<?php include("./includes/footer.php"); ?>