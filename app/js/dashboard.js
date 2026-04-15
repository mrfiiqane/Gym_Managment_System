$(document).ready(function() {
    // Only run if we are on the dashboard page and the stats container exists
    if ($("#admin-stats").length) {
        loadStats();
    }

    function loadStats() {
        $.ajax({
            url: '../api/dashboard.php',
            type: 'POST',
            data: { action: 'get_stats' },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    const data = response.data;
                    
                    // Animate numbers
                    animateValue("total_students", 0, data.total_students, 1000);
                    animateValue("total_teachers", 0, data.total_teachers, 1000);
                    animateValue("total_classes", 0, data.total_classes, 1000);
                    animateValue("pending_users", 0, data.pending_users, 1000);

                    // Render Charts
                    renderCharts(data);
                }
            }
        });
    }

    function renderCharts(data) {
        // 1. Gender Distribution (Pie Chart)
        const ctxGender = document.getElementById('genderChart');
        if (ctxGender) {
            // Destroy existing if any (optional, but good for SPA)
            // For now simple render
            const genderLabels = Object.keys(data.gender_dist);
            const genderValues = Object.values(data.gender_dist);

            new Chart(ctxGender, {
                type: 'doughnut',
                data: {
                    labels: genderLabels,
                    datasets: [{
                        data: genderValues,
                        backgroundColor: ['#6366f1', '#ec4899', '#fbbf24'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '70%'
                }
            });
        }

        // 2. Monthly Registrations (Bar Chart)
        const ctxReg = document.getElementById('registrationChart');
        if (ctxReg) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            // data.monthly_regs is 1-indexed (1=Jan), simpler to just map 
            const regData = Object.values(data.monthly_regs);

            new Chart(ctxReg, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'New Students',
                        data: regData,
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { display: false } },
                        x: { grid: { display: false } }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    }

    function animateValue(id, start, end, duration) {
        if (!document.getElementById(id)) return;
        
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            document.getElementById(id).innerHTML = value;
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
});
