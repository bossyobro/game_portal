const board = ['', '', '', '', '', '', '', '', '']; // Game board
let currentPlayer = 'X'; // Current player
let gameActive = true; // Game state

// Function to handle cell clicks
function handleCellClick(index) {
    if (board[index] !== '' || !gameActive) return; // Ignore if cell is filled or game is over
    board[index] = currentPlayer; // Set the cell to the current player
    renderBoard(); // Update the board display

    if (checkWinner()) {
        gameActive = false; // Stop the game if there's a winner
        alert(`${currentPlayer} wins!`); // Notify the winner
        return;
    }

    // Switch player
    currentPlayer = currentPlayer === 'X' ? 'O' : 'X';
    if (currentPlayer === 'O') {
        aiMove(); // AI makes its move
    }
}

// AI move function
function aiMove() {
    let availableMoves = board.map((cell, index) => (cell === '' ? index : null)).filter(index => index !== null);
    
    // Simple AI logic: Prioritize winning or blocking
    for (let i = 0; i < availableMoves.length; i++) {
        let move = availableMoves[i];
        board[move] = currentPlayer;
        if (checkWinner()) {
            renderBoard();
            alert(`${currentPlayer} wins!`);
            gameActive = false;
            return;
        }
        board[move] = ''; // Reset the cell
    }

    // If no immediate win, make a random move
    if (availableMoves.length > 0) {
        let move = availableMoves[Math.floor(Math.random() * availableMoves.length)];
        board[move] = currentPlayer;
        renderBoard();
        if (checkWinner()) {
            alert(`${currentPlayer} wins!`);
            gameActive = false;
        }
        currentPlayer = 'X'; // Switch back to the human player
    }
}

// Function to check for a winner
function checkWinner() {
    const winningCombinations = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8],
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8],
        [0, 4, 8],
        [2, 4, 6]
    ];

    for (const combination of winningCombinations) {
        const [a, b, c] = combination;
        if (board[a] && board[a] === board[b] && board[a] === board[c]) {
            return true; // Winning combination found
        }
    }
    return false; // No winner yet
}

// Function to render the board
function renderBoard() {
    const cells = document.querySelectorAll('.cell'); // Assuming you have elements with the class 'cell'
    cells.forEach((cell, index) => {
        cell.textContent = board[index]; // Update each cell with the board state
    });
}

// Set up event listeners for each cell
const cells = document.querySelectorAll('.cell');
cells.forEach((cell, index) => {
    cell.addEventListener('click', () => handleCellClick(index)); // Add click event to each cell
});
