const canvas = document.getElementById("snakeCanvas");
const ctx = canvas.getContext("2d");
const box = 20; // Size of the snake and food
let snake, direction, food, score, intervalTime;

// Initialize the game
function initGame() {
    snake = [{ x: 9 * box, y: 9 * box }]; // Initial position of the snake
    direction = ""; // Initial direction
    food = spawnFood(); // Initial food position
    score = 0; // Initial score
    intervalTime = 100; // Initial interval time
    if (typeof game !== "undefined") clearInterval(game); // Clear previous game loop
    game = setInterval(draw, intervalTime); // Game loop
}

// Draw everything on the canvas
function draw() {
    ctx.fillStyle = "#f4f4f4"; // Background color
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Draw the snake
    for (let i = 0; i < snake.length; i++) {
        ctx.fillStyle = (i === 0) ? "green" : "lightgreen"; // Head vs. body color
        ctx.fillRect(snake[i].x, snake[i].y, box, box);
        ctx.strokeStyle = "darkgreen"; // Border color
        ctx.strokeRect(snake[i].x, snake[i].y, box, box);
    }

    // Draw the food
    ctx.fillStyle = "red";
    ctx.fillRect(food.x, food.y, box, box);

    // Move the snake
    const snakeX = snake[0].x;
    const snakeY = snake[0].y;

    // Update snake's position based on direction
    if (direction === "LEFT") snake[0].x -= box;
    if (direction === "UP") snake[0].y -= box;
    if (direction === "RIGHT") snake[0].x += box;
    if (direction === "DOWN") snake[0].y += box;

    // Wrap-around logic: Allow snake to appear on the opposite side of the canvas
    if (snake[0].x < 0) {
        snake[0].x = canvas.width - box; // Move to the right side
    } else if (snake[0].x >= canvas.width) {
        snake[0].x = 0; // Move to the left side
    }
    if (snake[0].y < 0) {
        snake[0].y = canvas.height - box; // Move to the bottom
    } else if (snake[0].y >= canvas.height) {
        snake[0].y = 0; // Move to the top
    }

    // Check if the snake eats the food
    if (snake[0].x === food.x && snake[0].y === food.y) {
        score++;
        updateSpeed(); // Update speed based on score
        food = spawnFood(); // Respawn food
    } else {
        snake.pop(); // Remove the last segment of the snake
    }

    // Check for collisions with self
    if (collision(snake[0], snake.slice(1))) {
        gameOver(); // Trigger game over
    }

    // Display score
    ctx.fillStyle = "black";
    ctx.font = "20px Arial";
    ctx.fillText("Score: " + score, 10, 20); // Display score at the top-left
}

// Spawn food in a random position
function spawnFood() {
    let foodX, foodY;
    do {
        foodX = Math.floor(Math.random() * (canvas.width / box)) * box;
        foodY = Math.floor(Math.random() * (canvas.height / box)) * box;
    } while (snake.some(segment => segment.x === foodX && segment.y === foodY)); // Ensure food is not on the snake
    return { x: foodX, y: foodY };
}

// Check for collision with the snake's body
function collision(head, body) {
    return body.some(segment => head.x === segment.x && head.y === segment.y); // Return true if the head collides with any body segment
}

// Control the snake's direction
document.addEventListener("keydown", directionControl);

function directionControl(event) {
    if (event.keyCode === 37 && direction !== "RIGHT") direction = "LEFT"; // Left arrow
    if (event.keyCode === 38 && direction !== "DOWN") direction = "UP"; // Up arrow
    if (event.keyCode === 39 && direction !== "LEFT") direction = "RIGHT"; // Right arrow
    if (event.keyCode === 40 && direction !== "UP") direction = "DOWN"; // Down arrow
}

// Update speed based on score
function updateSpeed() {
    if (score % 5 === 0 && score !== 0) { // Increase speed every 5 points
        clearInterval(game);
        intervalTime -= 10; // Decrease interval time
        game = setInterval(draw, intervalTime); // Restart game loop with new speed
    }
}

// Handle game over
function gameOver() {
    clearInterval(game); // Stop the game
    alert("Game Over! Your score: " + score);
    if (confirm("Do you want to play again?")) {
        initGame(); // Restart the game
    } else {
        document.location.reload(); // Reload the page
    }
}

// Set canvas size to be responsive
canvas.width = window.innerWidth * 0.8; // 80% of the window width
canvas.height = window.innerHeight * 0.8; // 80% of the window height

// Start the game
initGame();