// tictactoe.js
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
        document.getElementById('result').innerText = `${currentPlayer} wins!`;
        recordScore(currentPlayer === 'X' ? 1 : 0); // Record score (1 for X win, 0 for O win)
        return;
    }

    if (board.every(cell => cell !== '')) {
        gameActive = false;
        document.getElementById('result').innerText = "It's a draw!";
        recordScore(0.5); // Record draw as 0.5 points
        return;
    }

    // Switch player
    currentPlayer = currentPlayer === 'X' ? 'O' : 'X';
    if (currentPlayer === 'O') {
        setTimeout(aiMove, 500); // AI makes its move after a short delay
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
            document.getElementById('result').innerText = `${currentPlayer} wins!`;
            recordScore(0); // AI (O) wins, player gets 0 points
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
            document.getElementById('result').innerText = `${currentPlayer} wins!`;
            recordScore(0); // AI (O) wins, player gets 0 points
            gameActive = false;
        } else if (board.every(cell => cell !== '')) {
            document.getElementById('result').innerText = "It's a draw!";
            recordScore(0.5); // Draw, player gets 0.5 points
            gameActive = false;
        } else {
            currentPlayer = 'X'; // Switch back to the human player
        }
    }
}

// Function to check for a winner
function checkWinner() {
    const winningCombinations = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
        [0, 3, 6], [1, 4, 7], [2, 5, 8], // Columns
        [0, 4, 8], [2, 4, 6] // Diagonals
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
    const boardElement = document.getElementById('tictactoeBoard');
    boardElement.innerHTML = ''; // Clear the board
    board.forEach((cell, index) => {
        const cellElement = document.createElement('div');
        cellElement.className = 'cell';
        cellElement.textContent = cell;
        cellElement.addEventListener('click', () => handleCellClick(index));
        boardElement.appendChild(cellElement);
    });
}

// Function to start a new game
function startNewGame() {
    board.fill('');
    currentPlayer = 'X';
    gameActive = true;
    document.getElementById('result').innerText = '';
    renderBoard();

    // Increment play count
    incrementPlayCount();
}
function incrementPlayCount() {
    fetch('handle_game.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            game_id: 2, // Tic Tac Toe game ID
            increment: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Play count incremented successfully');
        } else {
            console.error('Failed to increment play count:', data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Initialize the game
renderBoard();