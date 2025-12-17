<?php include("./includes/header.php"); ?>

<div class="container" style="margin-top: 20px;">
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -10px; top: -10px; font-size: 100px; opacity: 0.1;">
                <i class="fa-solid fa-hospital-user"></i>
            </div>
            <div style="position: relative; z-index: 1;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total Patients</div>
                <div style="font-size: 36px; font-weight: bold; margin-bottom: 10px;"><?php echo number_format($stats['total_patients']); ?></div>
                <div style="font-size: 12px; opacity: 0.8;">
                    <i class="fa-solid fa-arrow-up"></i> <?php echo $stats['new_patients_month']; ?> new this month
                </div>
            </div>
        </div>

        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -10px; top: -10px; font-size: 100px; opacity: 0.1;">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div style="position: relative; z-index: 1;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total Doctors</div>
                <div style="font-size: 36px; font-weight: bold; margin-bottom: 10px;"><?php echo number_format($stats['total_doctors']); ?></div>
                <div style="font-size: 12px; opacity: 0.8;">
                    <i class="fa-solid fa-stethoscope"></i> Active medical staff
                </div>
            </div>
        </div>

        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -10px; top: -10px; font-size: 100px; opacity: 0.1;">
                <i class="fa-solid fa-hospital"></i>
            </div>
            <div style="position: relative; z-index: 1;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total Departments</div>
                <div style="font-size: 36px; font-weight: bold; margin-bottom: 10px;"><?php echo number_format($stats['total_departments']); ?></div>
                <div style="font-size: 12px; opacity: 0.8;">
                    <i class="fa-solid fa-building"></i> Active departments
                </div>
            </div>
        </div>

    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        
        <div style="background: #336699; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
            <h3 style="color: #DAF7DC; margin-bottom: 20px;">
                <i class="fa-solid fa-chart-pie"></i> Doctors by Department
            </h3>
            <canvas id="doctorsByDeptChart" style="max-height: 300px;"></canvas>
        </div>

        <div style="background: #336699; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
            <h3 style="color: #DAF7DC; margin-bottom: 20px;">
                <i class="fa-solid fa-clock"></i> Recent Patients
            </h3>
            <div style="max-height: 300px; overflow-y: auto;">
                <?php if ($recentPatients && $recentPatients->num_rows > 0): ?>
                    <?php while ($patient = $recentPatients->fetch_assoc()): ?>
                        <div style="background: #2F4858; padding: 12px; border-radius: 6px; margin-bottom: 10px; border-left: 4px solid #9EE493;">
                            <div style="color: #DAF7DC; font-weight: bold; margin-bottom: 5px;">
                                <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="color: #86BBD8; text-align: center; padding: 20px;">No recent patients</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 30px;">
        
        <div style="background: #336699; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
            <h3 style="color: #DAF7DC; margin-bottom: 20px;">
                <i class="fa-solid fa-chart-pie"></i> Patients by Gender
            </h3>
            <canvas id="patientsByGenderChart" style="max-height: 300px;"></canvas>
        </div>

        <div style="background: #336699; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
            <h3 style="color: #DAF7DC; margin-bottom: 20px;">
                <i class="fa-solid fa-chart-pie"></i> Patients by Doctor
            </h3>
            <canvas id="patientsByDoctorChart" style="max-height: 300px;"></canvas>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        const ctx1 = document.getElementById('doctorsByDeptChart').getContext('2d');
        
        <?php
        $labels1 = [];
        $data1 = [];
        $colors = ['#667eea', '#f5576c', '#4facfe', '#43e97b', '#f093fb', '#ffa726', '#66bb6a'];
        
        mysqli_data_seek($doctorsByDepartment, 0); 
        while ($row = $doctorsByDepartment->fetch_assoc()) {
            $labels1[] = $row['department_name'];
            $data1[] = $row['doctor_count'];
        }
        ?>
        
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($labels1); ?>,
                datasets: [{
                    data: <?php echo json_encode($data1); ?>,
                    backgroundColor: <?php echo json_encode(array_slice($colors, 0, count($labels1))); ?>,
                    borderWidth: 2,
                    borderColor: '#336699'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#DAF7DC',
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        const ctx2 = document.getElementById('patientsByGenderChart').getContext('2d');
        
        <?php
        $genderData = $patientsByGender->fetch_assoc();
        $labels2 = ['Male', 'Female'];
        $data2 = [$genderData['male_count'], $genderData['female_count']];
        ?>
        
        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($labels2); ?>,
                datasets: [{
                    data: <?php echo json_encode($data2); ?>,
                    backgroundColor: ['#4facfe', '#f5576c'],
                    borderWidth: 2,
                    borderColor: '#336699'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#DAF7DC',
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });


        const ctx3 = document.getElementById('patientsByDoctorChart').getContext('2d');
        
        <?php
        $labels3 = [];
        $data3 = [];
        $colors = ['#667eea', '#f5576c', '#4facfe', '#43e97b', '#f093fb', '#ffa726', '#66bb6a'];
        
        mysqli_data_seek($patientsByDoctor, 0); 
        while ($row = $patientsByDoctor->fetch_assoc()) {
            $labels3[] = $row['doctor_name'];
            $data3[] = $row['patient_count'];
        }
        ?>
        
        new Chart(ctx3, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($labels3); ?>,
                datasets: [{
                    data: <?php echo json_encode($data3); ?>,
                    backgroundColor: <?php echo json_encode(array_slice($colors, 0, count($labels3))); ?>,
                    borderWidth: 2,
                    borderColor: '#336699'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#DAF7DC',
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<?php include("./includes/footer.php"); ?>