const DASHBOARD_API_URL = window.BASE_URL + "api/dashboard/admin.php";

$(document).ready(function () {
    loadStats();
});

function loadStats() {
    // Using AJAX.post for reusable structure
    AJAX.post(DASHBOARD_API_URL, { action: "get_admin_stats" }, function (response) {
        if (response.status) {
            const { stats, recent, trend, distribution } = response.data;

            // Update Numeric Stats with Animation
            animateCounter("total-students", stats.total_students || 0);
            animateCounter("total-teachers", stats.total_teachers || 0);
            animateCounter("total-courses", stats.total_courses || 0);
            animateCounter("pending-users", stats.pending_users || 0);
            animateCounter("total-revenue", stats.total_revenue || 0, "$");

            // Render Recent Activity
            if (recent) {
                RecentActivity(recent);
            }

            // Render Charts
            renderCharts(trend, distribution);
        }
    }, function (err) {
        console.error("Dashboard error:", err);
    });
}

    function animateCounter(id, target, prefix = "") {
        let current = 0;
        const duration = 1000;
        const targetValue = parseInt(target) || 0;
        const element = document.getElementById(id);

        if (!element) return;

        if (targetValue === 0) {
            element.innerText = prefix + 0;
            return;
        }

        const step = Math.ceil(targetValue / (duration / 50));
        const timer = setInterval(() => {
            current += step;
            if (current >= targetValue) {
                element.innerText = prefix + targetValue.toLocaleString();
                clearInterval(timer);
            } else {
                element.innerText = prefix + current.toLocaleString();
            }
        }, 50);
    }

    function renderCharts(trendData, distributionData) {
        if (trendData && trendData.length > 0) {
            trendData.reverse();
            const categories = trendData.map(item => item.month_name);
            const data = trendData.map(item => item.total);

            var optionsTrend = {
                series: [{ name: 'Enrollments', data: data }],
                chart: { type: 'area', height: 350, toolbar: { show: false } },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                colors: ['#2563eb'],
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.1, stops: [0, 90, 100] }
                },
                xaxis: { categories: categories },
                tooltip: { theme: 'light' }
            };
            new ApexCharts(document.querySelector("#enrollmentTrendChart"), optionsTrend).render();
        }

        if (distributionData && distributionData.length > 0) {
            const labels = distributionData.map(item => item.level.toUpperCase());
            const series = distributionData.map(item => parseInt(item.count));

            var optionsDist = {
                series: series,
                chart: { type: 'donut', height: 350 },
                labels: labels,
                colors: ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6'],
                plotOptions: {
                    pie: { donut: { size: '70%' } }
                },
                dataLabels: { enabled: false },
                legend: { position: 'bottom' }
            };
            new ApexCharts(document.querySelector("#courseDistributionChart"), optionsDist).render();
        }
    }

    function RecentActivity(recent) {
        let listHtml = "";
        const uploadUrl = window.BASE_URL + "uploads/User_profile/";
        
        recent.forEach((item) => {
            listHtml += `
                <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-50 hover:bg-gray-50 hover:border-gray-200 transition-all shadow-sm">
                    <img src="${uploadUrl + (item.image || "default.png")}" class="w-12 h-12 rounded-full object-cover">
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-800">
                            ${item.student_name} 
                            <span class="font-normal text-gray-500">enrolled in</span> 
                            <span class="text-blue-600 font-semibold">"${item.course_title}"</span>
                        </p>
                        <p class="text-[11px] text-gray-400 font-medium">
                            ${new Date(item.enrolled_at).toLocaleString("en-GB", { day: "numeric", month: "short", year: "numeric", hour: "2-digit", minute: "2-digit" })}
                        </p>
                    </div>
                </div>
            `;
        });
        $("#recent-enrollments-list").html(
            listHtml || '<p class="text-sm text-gray-400 italic">No recent activity.</p>',
        );
    }
