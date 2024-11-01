const canvas = document.getElementById('snakeCanvas');
const ctx = canvas.getContext('2d');

const box = 20; // Size of each box in the grid
let snake = [{ x: 9 * box, y: 9 * box }]; // Starting position of the snake
let direction = 'RIGHT'; // Initial direction
let food = generateFood(); // Generate initial food position
let score = 0;

// Event listener for keyboard controls
document.addEventListener('keydown', changeDirection);

// Function to change the direction of the snake
function changeDirection(event) {
    if (event.key === 'ArrowUp' && direction !== 'DOWN') {
        direction = 'UP';
    } else if (event.key === 'ArrowDown' && direction !== 'UP') {
        direction = 'DOWN';
    } else if (event.key === 'ArrowLeft' && direction !== 'RIGHT') {
        direction = 'LEFT';
    } else if (event.key === 'ArrowRight' && direction !== 'LEFT') {
        direction = 'RIGHT';
    }
}

// Function to generate food in a random position
function generateFood() {
    return {
        x: Math.floor(Math.random() * (canvas.width / box)) * box,
        y: Math.floor(Math.random() * (canvas.height / box)) * box,
    };
}

// Function to draw everything
function draw() {
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Draw food
    ctx.fillStyle = 'red';
    ctx.fillRect(food.x, food.y, box, box);

    // Draw snake
    for (let i = 0; i < snake.length; i++) {
        ctx.fillStyle = (i === 0) ? 'green' : 'lightgreen'; // Head is green, body is light green
        ctx.fillRect(snake[i].x, snake[i].y, box, box);
        ctx.strokeStyle = 'darkgreen';
        ctx.strokeRect(snake[i].x, snake[i].y, box, box);
    }

    // Move the snake
    const snakeX = snake[0].x;
    const snakeY = snake[0].y;

    // Check for collision with food
    if (snakeX === food.x && snakeY === food.y) {
        score++;
        food = generateFood(); // Generate new food
    } else {
        snake.pop(); // Remove the last part of the snake
    }

    // Move the snake in the current direction
    if (direction === 'LEFT') snake.unshift({ x: snakeX - box, y: snakeY });
    if (direction === 'UP') snake.unshift({ x: snakeX, y: snakeY - box });
    if (direction === 'RIGHT') snake.unshift({ x: snakeX + box, y: snakeY });
    if (direction === 'DOWN') snake.unshift({ x: snakeX, y: snakeY + box });

    // Check for collisions with walls or self
    if (snake[0].x < 0 || snake[0].x >= canvas.width || snake[0].y < 0 || snake[0].y >= canvas.height || collision(snake)) {
        alert('Game Over! Your score: ' + score);
        document.location.reload(); // Restart game
    }
}

// Function to check collision with the snake itself
function collision(snake) {
    for (let i = 1; i < snake.length; i++) {
        if (snake[i].x === snake[0].x && snake[i].y === snake[0].y) {
            return true; // Collision occurred
        }
    }
    return false; // No collision
}

// Start the game loop
setInterval(draw, 100); // 100ms for each frame
