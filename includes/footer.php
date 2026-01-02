</div> 
    </div> 
    
    <footer class="footer">
        <div class="container-fluid">
            <p class="mb-2">&copy; <?php echo date('Y'); ?> Hospital Management System. All rights reserved.</p>
            <p class="mb-0">
                <small>
                    <a href="#">Privacy Policy</a> | 
                    <a href="#">Terms of Service</a> | 
                    <a href="#">Contact Support</a>
                </small>
            </p>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
   
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>