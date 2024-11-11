<?php
session_start();
require 'db.php';
require 'auth.php';

// Check authentication
checkAuth();

try {
    $conn = getDbConnection();
    
    // Query for game play statistics based solely on play count
    $stmt = $conn->query("
    SELECT 
        g.id AS game_id,
        g.name AS game_name, 
        COALESCE(SUM(s.play_count), 0) AS total_play_count
    FROM 
        games g
    LEFT JOIN 
        scores s ON g.id = s.game_id
    GROUP BY 
        g.id, g.name
    ORDER BY 
        total_play_count DESC
    ");
    $gameStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare data for the pie chart
    $labels = [];
    $data = [];
    $colors = [
        'rgba(255, 99, 132, 0.8)',   // Red
        'rgba(54, 162, 235, 0.8)',   // Blue
        'rgba(255, 206, 86, 0.8)',   // Yellow
        'rgba(75, 192, 192, 0.8)',   // Green
        'rgba(153, 102, 255, 0.8)',  // Purple
        'rgba(255, 159, 64, 0.8)'    // Orange
    ];

    foreach ($gameStats as $stat) {
        $labels[] = $stat['game_name'];
        $data[] = $stat['total_play_count'];
    }

} catch (PDOException $e) {
    // Log the error and show a user-friendly message
    error_log("Leaderboard error: " . $e->getMessage());
    $error = "Unable to retrieve game statistics. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Play Statistics</title>
    <link rel="stylesheet" href="static/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="leaderboard-container">
        <h2>Game Play Statistics</h2>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php else: ?>
            <div class="chart-container">
                <canvas id="gamePlayChart"></canvas>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($gameStats)): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('gamePlayChart').getContext('2d');
        var gamePlayChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($data); ?>,
                    backgroundColor: <?php echo json_encode($colors); ?>,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Game Play Frequency'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.formattedValue + ' plays';
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
    </script>
    <?php endif; ?>
</body>
</html>