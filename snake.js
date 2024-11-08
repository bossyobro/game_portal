(function() {
    // Game configuration
    const CONFIG = {
        CANVAS_ID: "snakeCanvas",
        BOX_SIZE: 20,
        GAME_SPEED: 100,
        GAME_ID: 1
    };

    // Game state variables
    let canvas, ctx, snake, direction, food, score, session_id, gameLoop;

    // Utility function to log errors
    function logError(message) {
        console.error(`Snake Game Error: ${message}`);
    }

    // Initialize game canvas and context
    function initCanvas() {
        canvas = document.getElementById(CONFIG.CANVAS_ID);
        if (!canvas) {
            logError("Canvas not found");
            return false;
        }
        ctx = canvas.getContext("2d");
        return true;
    }

    // Spawn food at a random location
    function spawnFood() {
        let foodX, foodY;
        do {
            foodX = Math.floor(Math.random() * (canvas.width / CONFIG.BOX_SIZE)) * CONFIG.BOX_SIZE;
            foodY = Math.floor(Math.random() * (canvas.height / CONFIG.BOX_SIZE)) * CONFIG.BOX_SIZE;
        } while (snake.some(segment => segment.x === foodX && segment.y === foodY));
        return { x: foodX, y: foodY };
    }

    // Update score display
    function updateScoreDisplay() {
        const scoreElement = document.getElementById("score");
        if (scoreElement) {
            scoreElement.textContent = `Score: ${score}`;
        }
    }

    // Send score to server
    function sendScore() {
        if (!session_id) {
            logError('Session ID not set');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('add_score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `score=${score}&game_id=${CONFIG.GAME_ID}${csrfToken ? `&csrf_token=${csrfToken}` : ''}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Score submission failed');
            }
            return response.json();
        })
        .catch(error => logError(`Score submission error: ${error.message}`));
    }

    // Handle game over
    function handleGameOver() {
        // Stop the game loop
        clearInterval(gameLoop);

        // Draw game over screen
        ctx.fillStyle = "white";
        ctx.font = "30px Arial";
        ctx.textAlign = "center";
        ctx.fillText("Game Over", canvas.width / 2, canvas.height / 2 - 30);
        ctx.fillText(`Score: ${score}`, canvas.width / 2, canvas.height / 2 + 10);

        // Send final score
        sendScore();

        // Submit game session
        fetch('handle_game.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                session_id: session_id,
                game_id: CONFIG.GAME_ID,
                score: score
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Game submission failed');
            }
            return response.json();
        })
        .then(data => {
            if (data.achievements && data.achievements.length) {
                alert(`New achievements unlocked: ${data.achievements.join(', ')}`);
            }
            // Optionally reload or reset game after a delay
            setTimeout(resetGame, 2000);
        })
        .catch(error => logError(`Game submission error: ${error.message}`));
    }

    // Game draw and update logic
    function draw() {
        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Draw food
        ctx.fillStyle = "red";
        ctx.fillRect(food.x, food.y, CONFIG.BOX_SIZE, CONFIG.BOX_SIZE);

        // Move snake
        let head = { x: snake[0].x, y: snake[0].y };
        switch(direction) {
            case "LEFT":  head.x -= CONFIG.BOX_SIZE; break;
            case "UP":    head.y -= CONFIG.BOX_SIZE; break;
            case "RIGHT": head.x += CONFIG.BOX_SIZE; break;
            case "DOWN":  head.y += CONFIG.BOX_SIZE; break;
        }

        // Wrap around screen
        head.x = (head.x + canvas.width) % canvas.width;
        head.y = (head.y + canvas.height) % canvas.height;

        // Check for self-collision
        if (snake.some(segment => segment.x === head.x && segment.y === head.y)) {
            handleGameOver();
            return;
        }

        // Add new head
        snake.unshift(head);

        // Check for food collision
        if (head.x === food.x && head.y === food.y) {
            score++;
            food = spawnFood();
            updateScoreDisplay();
        } else {
            // Remove tail if no food eaten
            snake.pop();
        }

        // Draw snake
        ctx.fillStyle = "lime";
        snake.forEach(segment => {
            ctx.fillRect(segment.x, segment.y, CONFIG.BOX_SIZE, CONFIG.BOX_SIZE);
        });
    }

    // Change snake direction
    function changeDirection(event) {
        const key = event.keyCode;
        const directionMap = {
            37: "LEFT",
            38: "UP",
            39: "RIGHT",
            40: "DOWN"
        };

        const newDirection = directionMap[key];
        
        // Prevent 180-degree turns
        const invalidMoves = {
            "LEFT": "RIGHT",
            "RIGHT": "LEFT",
            "UP": "DOWN",
            "DOWN": "UP"
        };

        if (newDirection && direction !== invalidMoves[newDirection]) {
            direction = newDirection;
        }
    }

    // Reset game state
    function resetGame() {
        snake = [{ 
            x: CONFIG.BOX_SIZE * 5, 
            y: CONFIG.BOX_SIZE * 5 
        }];
        direction = "RIGHT";
        score = 0;
        food = spawnFood();
        updateScoreDisplay();

        // Restart game loop
        clearInterval(gameLoop);
        gameLoop = setInterval(draw, CONFIG.GAME_SPEED);
    }

    // Initialize game
    function initGame() {
        // Validate canvas and session
        if (!initCanvas() || !session_id) {
            logError("Game initialization failed");
            return;
        }

        // Reset game state
        resetGame();

        // Add event listeners
        document.addEventListener("keydown", changeDirection);
    }

    // Wait for DOM and session_id
    document.addEventListener('DOMContentLoaded', () => {
        // Get session_id from global scope (set in snake.php)
        session_id = window.session_id;
        
        if (session_id) {
            initGame();
        } else {
            logError('Session ID not found');
        }
    });

    // Expose global functions if needed
    window.startGame = initGame;
})();