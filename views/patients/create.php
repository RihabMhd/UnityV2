<?php include("./includes/header.php"); ?>

<div class="container" style="max-width: 600px; margin: 30px auto; background: #336699; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">

    <?php if (!empty($errors)): ?>
        <div style="
        background-color: #ff4d4d; 
        color: #fff; 
        padding: 15px; 
        border-radius: 6px; 
        margin-bottom: 20px; 
        border: 1px solid #e60000; 
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        font-family: Arial, sans-serif;
        animation: fadeIn 0.3s ease-in-out;
    ">
            <strong><?php echo __('Please fix the following errors'); ?>:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <?php foreach ($errors as $e): ?>
                    <li style="margin-bottom: 5px;">⚠️ <?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div style="margin-bottom: 20px;">
            <label for="first_name" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                <?php echo __('First Name'); ?> <?php echo __('*'); ?>
            </label>
            <input type="text" id="first_name" name="first_name" required 
                   style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="last_name" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                <?php echo __('Last Name'); ?> <?php echo __('*'); ?>
            </label>
            <input type="text" id="last_name" name="last_name" required 
                   style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="gender" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                <?php echo __('Gender'); ?> <?php echo __('*'); ?>
            </label>
            <select id="gender" name="gender" required 
                    style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
                <option value=""><?php echo __('Select Gender'); ?></option>
                <option value="Male"><?php echo __('Male'); ?></option>
                <option value="Female"><?php echo __('Female'); ?></option>
                <option value="Other"><?php echo __('Other'); ?></option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="date_of_birth" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                <?php echo __('Date of Birth'); ?> <?php echo __('*'); ?>
            </label>
            <input type="date" id="date_of_birth" name="date_of_birth" required 
                   style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC; color-scheme: dark;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="phone_number" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                <?php echo __('Phone Number'); ?> <?php echo __('*'); ?>
            </label>
            <input type="tel" id="phone_number" name="phone_number" required 
                   style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="email" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                <?php echo __('Email'); ?> <?php echo __('*'); ?>
            </label>
            <input type="email" id="email" name="email" required 
                   style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="address" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">
                <?php echo __('Address'); ?>
            </label>
            <input type="text" id="address" name="address" 
                   style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
        </div>

        <div>
            <button type="submit" 
                    style="padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-right: 10px; background-color: #9EE493; color: #2F4858; font-weight: bold;">
                <?php echo __('Create Patient'); ?>
            </button>
            <a href="index.php" 
               style="padding: 12px 30px; border: none; border-radius: 4px; font-size: 16px; background-color: #86BBD8; color: #2F4858; text-decoration: none; display: inline-block; font-weight: bold;">
                <?php echo __('Cancel'); ?>
            </a>
        </div>
    </form>
</div>

<style>
    input:focus,
    select:focus {
        outline: none;
        border-color: #9EE493 !important;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }
</style>

<?php include("./includes/footer.php"); ?>