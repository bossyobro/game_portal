const board = Array(9).fill(null);
let currentPlayer = "X";

function setup() {
    renderBoard();
}

function renderBoard() {
    const gameBoard = document.getElementById("gameBoard");
    gameBoard.innerHTML = "";
    board.forEach((cell, index) => {
        const cellDiv = document.createElement("div");
        cellDiv.className = "cell";
        cellDiv.innerText = cell;
        cellDiv.addEventListener("click", () => handleCellClick(index));
        gameBoard.appendChild(cellDiv);
    });
}

function handleCellClick(index) {
    if (!board[index]) {
        board[index] = currentPlayer;
        if (checkWin(currentPlayer)) {
            setTimeout(() => alert(currentPlayer + " wins!"), 100);
            resetGame();
        } else if (board.every(cell => cell)) {
            setTimeout(() => alert("It's a draw!"), 100);
            resetGame();
        } else {
            currentPlayer = currentPlayer === "X" ? "O" : "X"; // Switch players
            if (currentPlayer === "O") {
                aiMove();
            }
            renderBoard();
        }
    }
}

function aiMove() {
    const availableCells = board.map((cell, index) => (cell === null ? index : null)).filter(index => index !== null);
    const randomIndex = availableCells[Math.floor(Math.random() * availableCells.length)];
    board[randomIndex] = currentPlayer;
    if (checkWin(currentPlayer)) {
        setTimeout(() => alert(currentPlayer + " wins!"), 100);
        resetGame();
    } else if (board.every(cell => cell)) {
        setTimeout(() => alert("It's a draw!"), 100);
        resetGame();
    } else {
        currentPlayer = "X"; // Switch back to player
        renderBoard();
    }
}

function checkWin(player) {
    const winningCombinations = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8],
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8],
        [0, 4, 8],
        [2, 4, 6],
    ];
    return winningCombinations.some(combination => combination.every(index => board[index] === player));
}

function resetGame() {
    board.fill(null);
    currentPlayer = "X";
    renderBoard();
}

window.onload = setup;
