const canvas = document.getElementById("gameCanvas");
const ctx = canvas.getContext("2d");

const boxSize = 20;
const canvasSize = canvas.width;
let snake = [
    { x: boxSize * 5, y: boxSize * 5 }
];
let direction = "RIGHT";
let food = spawnFood();
let score = 0;

document.addEventListener("keydown", changeDirection);

function changeDirection(event) {
    const key = event.keyCode;
    if (key === 37 && direction !== "RIGHT") direction = "LEFT";
    else if (key === 38 && direction !== "DOWN") direction = "UP";
    else if (key === 39 && direction !== "LEFT") direction = "RIGHT";
    else if (key === 40 && direction !== "UP") direction = "DOWN";
}

function draw() {
    ctx.clearRect(0, 0, canvasSize, canvasSize);

    // Draw food
    ctx.fillStyle = "red";
    ctx.fillRect(food.x, food.y, boxSize, boxSize);

    // Move the snake
    let head = { x: snake[0].x, y: snake[0].y };
    if (direction === "LEFT") head.x -= boxSize;
    else if (direction === "UP") head.y -= boxSize;
    else if (direction === "RIGHT") head.x += boxSize;
    else if (direction === "DOWN") head.y += boxSize;

    // Wrap snake position when it crosses the wall
    if (head.x < 0) head.x = canvasSize - boxSize;
    else if (head.x >= canvasSize) head.x = 0;
    if (head.y < 0) head.y = canvasSize - boxSize;
    else if (head.y >= canvasSize) head.y = 0;

    // Check for collision with itself
    if (snake.some(segment => segment.x === head.x && segment.y === head.y)) {
        resetGame();
        return;
    }

    // Add new head to snake
    snake.unshift(head);

    // Check if snake has eaten the food
    if (head.x === food.x && head.y === food.y) {
        score++;
        food = spawnFood();
    } else {
        snake.pop(); // Remove last segment if no food eaten
    }

    // Draw the snake
    ctx.fillStyle = "lime";
    snake.forEach(segment => {
        ctx.fillRect(segment.x, segment.y, boxSize, boxSize);
    });

    // Display score
    ctx.fillStyle = "white";
    ctx.font = "20px Arial";
    ctx.fillText("Score: " + score, 10, 20);
}

// Generate random food position
function spawnFood() {
    let foodX, foodY;
    do {
        foodX = Math.floor(Math.random() * (canvasSize / boxSize)) * boxSize;
        foodY = Math.floor(Math.random() * (canvasSize / boxSize)) * boxSize;
    } while (snake.some(segment => segment.x === foodX && segment.y === foodY));
    return { x: foodX, y: foodY };
}

// Reset the game
function resetGame() {
    snake = [{ x: boxSize * 5, y: boxSize * 5 }];
    direction = "RIGHT";
    score = 0;
    food = spawnFood();
}

// Game loop
setInterval(draw, 100);
