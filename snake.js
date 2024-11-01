let canvas, ctx, snake, food, score, direction, gameInterval;

function setup() {
    canvas = document.getElementById("snakeCanvas");
    ctx = canvas.getContext("2d");
    snake = [{ x: 5, y: 5 }];
    food = { x: Math.floor(Math.random() * 20), y: Math.floor(Math.random() * 20) };
    score = 0;
    direction = { x: 0, y: 0 };
    document.addEventListener("keydown", changeDirection);
    gameInterval = setInterval(draw, 100);
}

function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawSnake();
    drawFood();
    moveSnake();
    checkCollision();
}

function drawSnake() {
    ctx.fillStyle = "green";
    for (let segment of snake) {
        ctx.fillRect(segment.x * 20, segment.y * 20, 18, 18);
    }
}

function drawFood() {
    ctx.fillStyle = "red";
    ctx.fillRect(food.x * 20, food.y * 20, 18, 18);
}

function moveSnake() {
    const head = { x: snake[0].x + direction.x, y: snake[0].y + direction.y };
    snake.unshift(head);
    if (head.x === food.x && head.y === food.y) {
        score++;
        food = { x: Math.floor(Math.random() * 20), y: Math.floor(Math.random() * 20) };
    } else {
        snake.pop();
    }
}

function changeDirection(event) {
    switch (event.key) {
        case "ArrowUp":
            direction = { x: 0, y: -1 };
            break;
        case "ArrowDown":
            direction = { x: 0, y: 1 };
            break;
        case "ArrowLeft":
            direction = { x: -1, y: 0 };
            break;
        case "ArrowRight":
            direction = { x: 1, y: 0 };
            break;
    }
}

function checkCollision() {
    const head = snake[0];
    if (head.x < 0 || head.x >= 20 || head.y < 0 || head.y >= 20 || snake.slice(1).some(seg => seg.x === head.x && seg.y === head.y)) {
        clearInterval(gameInterval);
        alert("Game Over! Your score: " + score);
        location.reload(); // Restart the game
    }
}

window.onload = setup;
