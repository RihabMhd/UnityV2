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
                <label for="first_name" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">First Name *</label>
                <input type="text" id="first_name" name="first_name" 
                       value="<?php echo htmlspecialchars($data['first_name']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="last_name" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">Last Name *</label>
                <input type="text" id="last_name" name="last_name" 
                       value="<?php echo htmlspecialchars($data['last_name']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="gender" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">Gender *</label>
                <select id="gender" name="gender" required style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
                    <option value="">Select Gender</option>
                    <option value="Male" <?php echo ($data['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($data['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo ($data['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="date_of_birth" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">Date of Birth *</label>
                <input type="date" id="date_of_birth" name="date_of_birth" 
                       value="<?php echo htmlspecialchars($data['date_of_birth']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC; color-scheme: dark;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="phone_number" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">Phone Number *</label>
                <input type="tel" id="phone_number" name="phone_number" 
                       value="<?php echo htmlspecialchars($data['phone_number']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">Email *</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($data['email']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label for="address" style="display: block; margin-bottom: 5px; color: #DAF7DC; font-weight: bold;">Address</label>
                <input type="text" id="address" name="address" 
                       value="<?php echo htmlspecialchars($data['address']); ?>" style="width: 100%; padding: 10px; border: 1px solid #86BBD8; border-radius: 4px; box-sizing: border-box; background-color: #2F4858; color: #DAF7DC;">
            </div>
            
            <div>
                <button type="submit" style="padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-right: 10px; background-color: #9EE493; color: #2F4858; font-weight: bold;">Update Patient</button>
                <a href="index.php" style="padding: 12px 30px; border: none; border-radius: 4px; font-size: 16px; background-color: #86BBD8; color: #2F4858; text-decoration: none; display: inline-block; font-weight: bold;">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <div style="color: #ff6b6b; margin-bottom: 20px; padding: 10px; background-color: #3d2020; border: 1px solid #5a2a2a; border-radius: 4px;">Patient not found.</div>
        <a href="index.php" style="padding: 12px 30px; border: none; border-radius: 4px; font-size: 16px; background-color: #6c757d; color: white; text-decoration: none; display: inline-block;">Back to List</a>
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