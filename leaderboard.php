<?php
session_start();
require 'db.php';

try {
    $conn = getDbConnection();
    
    // Query for game play statistics
    $stmt = $conn->query("
        SELECT 
            g.id AS game_id,
            g.name AS game_name, 
            COALESCE(SUM(s.play_count), 0) AS total_play_count,
            COALESCE(MAX(s.score), 0) AS highest_score,
            (SELECT u.username 
             FROM scores max_s 
             JOIN users u ON max_s.user_id = u.id 
             WHERE max_s.game_id = g.id 
             ORDER BY max_s.score DESC 
             LIMIT 1) AS top_player
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

    // Query for detailed leaderboard
    $stmt = $conn->query("
        SELECT 
            g.name AS game_name,
            u.username,
            MAX(s.score) AS best_score,
            SUM(s.play_count) AS user_play_count
        FROM 
            scores s
        JOIN 
            users u ON s.user_id = u.id
        JOIN 
            games g ON s.game_id = g.id
        GROUP BY 
            g.name, u.username
        ORDER BY 
            g.name, best_score DESC
        LIMIT 20
    ");
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Game Statistics and Leaderboard</title>
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

            <h3>Game Overview</h3>
            <table>
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Total Plays</th>
                        <th>Highest Score</th>
                        <th>Top Player</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gameStats as $stat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stat['game_name']); ?></td>
                            <td><?php echo htmlspecialchars($stat['total_play_count']); ?></td>
                            <td><?php echo htmlspecialchars($stat['highest_score']); ?></td>
                            <td><?php echo htmlspecialchars($stat['top_player'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3>Detailed Leaderboard</h3>
            <table>
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Player</th>
                        <th>Best Score</th>
                        <th>Times Played</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaderboard as $entry): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($entry['game_name']); ?></td>
                            <td><?php echo htmlspecialchars($entry['username']); ?></td>
                            <td><?php echo htmlspecialchars($entry['best_score']); ?></td>
                            <td><?php echo htmlspecialchars($entry['user_play_count']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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