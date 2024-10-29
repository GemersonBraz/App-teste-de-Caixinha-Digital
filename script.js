let totalAmount;
let numSavings;
let squares = [];
let selectedSquares = [];

// Carrega os dados do localStorage quando o aplicativo é iniciado
function loadData() {
    const savedData = localStorage.getItem('savingsData');
    if (savedData) {
        const parsedData = JSON.parse(savedData);
        totalAmount = parsedData.totalAmount;
        numSavings = parsedData.numSavings;
        squares = parsedData.squares;
        selectedSquares = parsedData.selectedSquares || [];
        displayProgress();
    }
}

function startSaving() {
    totalAmount = parseFloat(document.getElementById('total-amount').value);
    numSavings = parseInt(document.getElementById('num-savings').value);

    if (!totalAmount || !numSavings) {
        alert('Por favor, preencha os campos corretamente.');
        return;
    }

    // Gera os quadrados
    generateSquares();
    // Mostra a tela de progresso
    document.getElementById('initial-screen').style.display = 'none';
    document.getElementById('progress-container').style.display = 'block';
    updateProgress();
    saveData();
}

function generateSquares() {
    const squaresContainer = document.getElementById('squares-container');
    squaresContainer.innerHTML = '';
    squares = [];
    const baseAmount = totalAmount / numSavings;
    let totalAllocated = 0;

    for (let i = 0; i < numSavings; i++) {
        const randomPercentage = Math.random() * 0.5 + 0.7; 
        const amount = Math.round(baseAmount * randomPercentage);
        squares[i] = amount;
        totalAllocated += amount;

        const square = document.createElement('div');
        square.className = 'square';
        square.onclick = () => selectSquare(square, i);
        square.innerText = amount;

        if (selectedSquares.includes(i)) {
            square.classList.add('selected');
        }

        squaresContainer.appendChild(square);
    }

    adjustSquares(totalAllocated);
}

function adjustSquares(totalAllocated) {
    const difference = totalAmount - totalAllocated;
    const adjustment = Math.round(difference / squares.length);
    for (let i = 0; i < squares.length; i++) {
        squares[i] += adjustment;
    }

    const newTotalAllocated = squares.reduce((acc, val) => acc + val, 0);
    const finalDifference = totalAmount - newTotalAllocated;

    if (finalDifference !== 0) {
        squares[0] += finalDifference;
    }

    updateProgress();
    saveData();
}

function selectSquare(square, index) {
    square.classList.toggle('selected');
    if (square.classList.contains('selected')) {
        selectedSquares.push(index);
    } else {
        selectedSquares = selectedSquares.filter(i => i !== index);
    }
    updateProgress();
    saveData();
    
    if (getTotalSaved() >= totalAmount) {
        alert("Parabéns! Você conseguiu guardar toda a quantia!");
    }
}

function updateProgress() {
    const selectedElements = document.querySelectorAll('.square.selected');
    let totalSaved = getTotalSaved();

    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    progressBar.style.width = (totalSaved / totalAmount) * 100 + '%';
    progressText.innerText = `${totalSaved} / ${totalAmount}`;
}

function getTotalSaved() {
    const selectedElements = document.querySelectorAll('.square.selected');
    let totalSaved = 0;

    selectedElements.forEach(square => {
        const index = Array.from(square.parentNode.children).indexOf(square);
        totalSaved += squares[index];
    });

    return totalSaved;
}

function saveData() {
    const dataToSave = {
        totalAmount: totalAmount,
        numSavings: numSavings,
        squares: squares,
        selectedSquares: selectedSquares
    };
    localStorage.setItem('savingsData', JSON.stringify(dataToSave));
}

function displayProgress() {
    document.getElementById('initial-screen').style.display = 'none';
    document.getElementById('progress-container').style.display = 'block';
    generateSquares();
    updateProgress();
}

function resetData() {
    const confirmation = confirm("Você tem certeza que deseja resetar todas as informações?");
    if (confirmation) {
        totalAmount = 0;
        numSavings = 0;
        squares = [];
        selectedSquares = [];
        localStorage.removeItem('savingsData');
        document.getElementById('initial-screen').style.display = 'block';
        document.getElementById('progress-container').style.display = 'none';
        alert("Dados resetados com sucesso!");
    }
}

window.onload = loadData;
