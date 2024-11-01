let canvas = document.getElementById("snakeGameCanvas");
let ctx = canvas.getContext("2d");

let box = 20; // Size of each box
let snake = [{ x: 5 * box, y: 5 * box }]; // Initial position of the snake
let direction = "RIGHT"; // Initial direction
let food = { x: Math.floor(Math.random() * 15 + 1) * box, y: Math.floor(Math.random() * 15 + 1) * box }; // Initial food position
let score = 0;
let speed = 200; // Increase this number to slow down the snake

// Event listener for key presses
document.addEventListener("keydown", changeDirection);

function changeDirection(event) {
    if (event.keyCode === 37 && direction !== "RIGHT") { // Left arrow
        direction = "LEFT";
    } else if (event.keyCode === 38 && direction !== "DOWN") { // Up arrow
        direction = "UP";
    } else if (event.keyCode === 39 && direction !== "LEFT") { // Right arrow
        direction = "RIGHT";
    } else if (event.keyCode === 40 && direction !== "UP") { // Down arrow
        direction = "DOWN";
    }
}

function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Draw the food
    ctx.fillStyle = "red";
    ctx.fillRect(food.x, food.y, box, box);

    // Draw the snake
    for (let i = 0; i < snake.length; i++) {
        ctx.fillStyle = (i === 0) ? "green" : "lightgreen";
        ctx.fillRect(snake[i].x, snake[i].y, box, box);

        // Check for collision with itself
        if (i !== 0 && snake[i].x === snake[0].x && snake[i].y === snake[0].y) {
            clearInterval(game); // Stop the game
            alert("Game Over! Score: " + score);
            document.location.reload(); // Reload the page
        }
    }

    // Update snake position
    let snakeX = snake[0].x;
    let snakeY = snake[0].y;

    if (direction === "LEFT") snakeX -= box;
    if (direction === "UP") snakeY -= box;
    if (direction === "RIGHT") snakeX += box;
    if (direction === "DOWN") snakeY += box;

    // Check if the snake eats the food
    if (snakeX === food.x && snakeY === food.y) {
        score++;
        food = {
            x: Math.floor(Math.random() * 15 + 1) * box,
            y: Math.floor(Math.random() * 15 + 1) * box,
        };
    } else {
        snake.pop(); // Remove the last part of the snake
    }

    // Add new head
    let newHead = { x: snakeX, y: snakeY };
    snake.unshift(newHead); // Add new head to the snake
}

// Start the game loop
let game = setInterval(draw, speed); // Set the speed of the game
