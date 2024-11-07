<?php
//leaderboard.php
session_start();
require 'db.php';

$conn = getDbConnection();
$stmt = $conn->query("
    SELECT users.username, scores.score, scores.created_at
    FROM scores
    JOIN users ON users.id = scores.user_id
    ORDER BY scores.score DESC
    LIMIT 10
");
$leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch game play statistics
$stmt = $conn->query("
    SELECT games.name, COUNT(*) as play_count
    FROM scores
    JOIN games ON games.id = scores.game_id
    GROUP BY games.id
    ORDER BY play_count DESC
");
$gameStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for the pie chart
$labels = [];
$data = [];
foreach ($gameStats as $stat) {
    $labels[] = $stat['name'];
    $data[] = $stat['play_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="static/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="leaderboard-container">
        <h2>Top Scores</h2>
        <table>
            <tr>
                <th>Rank</th>
                <th>Player</th>
                <th>Game</th>
                <th>Score</th>
                <th>Date</th>
            </tr>
            <?php foreach ($leaderboard as $index => $entry): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($entry['username']); ?></td>
                    <td><?php echo htmlspecialchars($entry['game_name']); ?></td>
                    <td><?php echo htmlspecialchars($entry['score']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($entry['created_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="chart-container">
            <h3>Game Popularity</h3>
            <canvas id="gamePopularityChart"></canvas>
        </div>
    </div>

    <script>
    var ctx = document.getElementById('gamePopularityChart').getContext('2d');
    var gamePopularityChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                data: <?php echo json_encode($data); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                ],
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Game Popularity'
            }
        }
    });
    </script>
</body>
</html>