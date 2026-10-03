<?php
session_start();

$code_correct = "Andrirakoto";
$erreur = "";

// Traitement du formulaire lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['password'])) {
        $mot_de_passe = trim($_POST['password']);
        if ($mot_de_passe === $code_correct) {
            $_SESSION['authenticated'] = true;
        } else {
            $erreur = " code incorrect! genre tu connais pas ton nom de famille ? ";
        }
    }
}

// Vérification de l'état de connexion
$est_connecte = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Happy Boyfriend Day - Arcade & Love Zone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php if ($est_connecte): ?>
        <!-- BARRE DE NAVIGATION DU HAUT -->
        <nav class="top-navbar">
            <div class="nav-links">
                <button class="nav-item active" onclick="showSection('games', this)"> jeux </button>
                <button class="nav-item" onclick="showSection('letter', this)"> lettre (oui encore)</button>
                <button class="nav-item" onclick="showSection('playlist', this)"> playlist</button>
            </div>

        </nav>
    <?php endif; ?>

    <div class="container <?php echo $est_connecte ? 'container-connected' : ''; ?>">
        <?php if (!$est_connecte): ?>
            <!-- PAGE DE LOGIN -->
            <p class="subtitle">Happy Boyfriend day Hira</p>

            <?php if (!empty($erreur)): ?>
                <div class="error"><?php echo htmlspecialchars($erreur); ?></div>
            <?php endif; ?>

            <form action="index.php" method="POST" class="login-form">
                <div class="input-group">
                    <label for="password">Entre le code secret :</label>
                    <input type="password" id="password" name="password" placeholder=" inona arany ohhhh" required autofocus>
                    <span class="hint">Indice : c'est juste ton nom de famille mdr </span>
                </div>
                <button type="submit" class="btn">Déverrouiller </button>
            </form>

        <?php else: ?>
            <!-- CONTENU DE CONNECTÉ (SECTIONS SWITCHABLES) -->
            
            <!-- SECTION 1 : JEUX -->
            <div id="section-games" class="content-section active">
                <div class="welcome-card">
                    <h1>Bonne fête mon namoureux ! </h1>

                    <p style="margin-bottom: 15px;">Bienvenue dans ta zone de jeu interactive ! </p>

                    <!-- Sélecteur de jeux -->
                    <div class="arcade-menu">
                        <button class="btn-game active" onclick="switchGame(1)">1. CATCHER</button>
                        <button class="btn-game" onclick="switchGame(2)">2. TAP-HEART</button>
                        <button class="btn-game" onclick="switchGame(3)">3. FLAPPY</button>
                    </div>

                    <!-- Conteneur des jeux -->
                    <div class="game-canvas-container" id="game-container"></div>

                    <div class="game-score" id="score-display">Score: 0</div>
                </div>
            </div>

            <!-- SECTION 2 : LETTRE -->
            <div id="section-letter" class="content-section">
                <div class="welcome-card">
                    <h1>Ma Lettre Pour Toi 💌</h1>
                    <div class="letter-box">
                        <p>Mon Hira d'amour ,</p>
                        <p>Merci d'être cette personne formidable que tu es. Tu apportes tellement de joie et de bonheur dans ma vie !</p>
                        <p>Aujourd'hui c'est ta journée, alors j'espère que ces petits jeux et cette attention te feront sourire autant que tu me fais sourire au quotidien.</p>
                        <p>Je t'aime très fort ! </p>
                        <p>from la plus belle et la plus douce des copines que tu as connu dans ta vie </p>
                    </div>
                </div>
            </div>

            <!-- SECTION 3 : PLAYLIST -->
            <div id="section-playlist" class="content-section">
                <div class="welcome-card">
                    <h1>Playlist </h1>
                    <p style="margin-bottom: 15px;"> c'est juste ma playlist :</p>
                    <div class="playlist-box">
                        <!-- Exemple avec Spotify (remplace l'URL src par ton player ou iframe Spotify) -->
                      <iframe data-testid="embed-iframe" style="border-radius:12px" src="https://open.spotify.com/embed/playlist/56jvhrUHEcpoheNiMH0elJ?utm_source=generator&theme=0&si=968339c2256c4c98" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe> </div>
                </div>
            </div>

            <script>
                // --- NAVIGATION DES SECTIONS ---
                function showSection(sectionName, btnElement) {
                    // Masquer toutes les sections
                    document.querySelectorAll('.content-section').forEach(sec => {
                        sec.classList.remove('active');
                    });
                    // Retirer l'état actif sur les liens du nav
                    document.querySelectorAll('.nav-item').forEach(btn => {
                        btn.classList.remove('active');
                    });

                    // Activer la section et le bouton cliqué
                    document.getElementById('section-' + sectionName).classList.add('active');
                    btnElement.classList.add('active');
                }

                // --- SCRIPT DES MINI-JEUX ---
                let currentGame = 1;
                let score = 0;
                let gameInterval = null;
                let animFrame = null;

                const container = document.getElementById('game-container');
                const scoreDisplay = document.getElementById('score-display');

                function updateScore(val) {
                    score = val;
                    scoreDisplay.innerText = "Score: " + score;
                }

                function clearCurrentGame() {
                    if (gameInterval) clearInterval(gameInterval);
                    if (animFrame) cancelAnimationFrame(animFrame);
                    container.innerHTML = '';
                }

                function switchGame(gameNum) {
                    currentGame = gameNum;
                    document.querySelectorAll('.btn-game').forEach((btn, idx) => {
                        btn.classList.toggle('active', idx + 1 === gameNum);
                    });
                    clearCurrentGame();
                    updateScore(0);

                    if (gameNum === 1) initCatcherGame();
                    if (gameNum === 2) initWhackGame();
                    if (gameNum === 3) initFlappyGame();
                }

                /* ================= JEU 1: HEART CATCHER ================= */
                function initCatcherGame() {
                    const canvas = document.createElement('canvas');
                    canvas.width = 360;
                    canvas.height = 300;
                    container.appendChild(canvas);
                    const ctx = canvas.getContext('2d');

                    let player = { x: 150, y: 270, w: 60, h: 15 };
                    let hearts = [];
                    let localScore = 0;

                    function createHeart() {
                        hearts.push({
                            x: Math.random() * (canvas.width - 20) + 10,
                            y: -10,
                            speed: 2 + Math.random() * 2
                        });
                    }

                    function moveBasket(e) {
                        const rect = canvas.getBoundingClientRect();
                        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                        player.x = clientX - rect.left - player.w / 2;
                    }

                    canvas.addEventListener('mousemove', moveBasket);
                    canvas.addEventListener('touchmove', moveBasket);

                    gameInterval = setInterval(createHeart, 800);

                    function loop() {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);

                        ctx.fillStyle = '#00f0ff';
                        ctx.shadowBlur = 10;
                        ctx.shadowColor = '#00f0ff';
                        ctx.fillRect(player.x, player.y, player.w, player.h);

                        ctx.fillStyle = '#ff007f';
                        ctx.shadowColor = '#ff007f';
                        for (let i = 0; i < hearts.length; i++) {
                            let h = hearts[i];
                            h.y += h.speed;

                            ctx.font = '18px sans-serif';
                            ctx.fillText('❤️', h.x, h.y);

                            if (h.y >= player.y - 10 && h.y <= player.y + player.h && h.x >= player.x && h.x <= player.x + player.w) {
                                localScore += 10;
                                updateScore(localScore);
                                hearts.splice(i, 1);
                                i--;
                            } else if (h.y > canvas.height) {
                                hearts.splice(i, 1);
                                i--;
                            }
                        }

                        animFrame = requestAnimationFrame(loop);
                    }
                    loop();
                }
/* ================= JEU 2: TAPE LE CŒUR ================= */
function initWhackGame() {
    const grid = document.createElement('div');
    grid.className = 'whack-grid';
    let localScore = 0;

    for (let i = 0; i < 6; i++) {
        const hole = document.createElement('div');
        hole.className = 'hole';
        hole.dataset.id = i;
        
        // Utilisation de pointerdown ou click avec trim() pour éviter les bugs d'émoji
        hole.onclick = () => {
            if (hole.innerText.includes('❤️') || hole.innerText.includes('❤')) {
                localScore += 10;
                updateScore(localScore);
                hole.innerText = '💥';
                setTimeout(() => { 
                    if (hole.innerText === '💥') hole.innerText = ''; 
                }, 200);
            }
        };
        grid.appendChild(hole);
    }
    container.appendChild(grid);

    const holes = grid.querySelectorAll('.hole');
    gameInterval = setInterval(() => {
        holes.forEach(h => { 
            if (h.innerText.includes('❤️') || h.innerText.includes('❤')) {
                h.innerText = ''; 
            }
        });
        const randomHole = holes[Math.floor(Math.random() * holes.length)];
        randomHole.innerText = '❤️';
    }, 700);
}
                
                /* ================= JEU 3: FLAPPY HEART ================= */
function initFlappyGame() {
    const canvas = document.createElement('canvas');
    canvas.width = 360;
    canvas.height = 300;
    container.appendChild(canvas);
    const ctx = canvas.getContext('2d');

    let heart = { x: 50, y: 150, vy: 0, gravity: 0.35, jump: -6.5 };
    let pipes = [];
    let localScore = 0;
    let isGameOver = false;

    function jump() {
        if (isGameOver) return;
        heart.vy = heart.jump;
    }

    // Gestionnaire de saut unique pour le clavier
    const handleKeyDown = (e) => {
        if (e.code === 'Space') {
            e.preventDefault();
            if (isGameOver) {
                restartGame();
            } else {
                jump();
            }
        }
    };
    window.addEventListener('keydown', handleKeyDown);

    function spawnPipe() {
        if (isGameOver) return;
        let gap = 100;
        let topHeight = Math.floor(Math.random() * (canvas.height - gap - 60)) + 30;
        pipes.push({
            x: canvas.width,
            top: topHeight,
            bottom: canvas.height - topHeight - gap,
            passed: false
        });
    }

    gameInterval = setInterval(spawnPipe, 1400);

    function loop() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (!isGameOver) {
            heart.vy += heart.gravity;
            heart.y += heart.vy;

            // Détection sol et plafond
            if (heart.y > canvas.height - 10 || heart.y < 10) {
                isGameOver = true;
            }
        }

        // Dessin du cœur (centré)
        ctx.font = '22px sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('💖', heart.x, heart.y);

        // Dessin des tuyaux
        ctx.fillStyle = '#00f0ff';
        ctx.shadowBlur = 8;
        ctx.shadowColor = '#00f0ff';

        for (let i = 0; i < pipes.length; i++) {
            let p = pipes[i];
            if (!isGameOver) p.x -= 2;

            ctx.fillRect(p.x, 0, 30, p.top);
            ctx.fillRect(p.x, canvas.height - p.bottom, 30, p.bottom);

            // Détection exacte de collision avec les tuyaux
            if (
                heart.x + 10 > p.x && heart.x - 10 < p.x + 30 &&
                (heart.y - 10 < p.top || heart.y + 10 > canvas.height - p.bottom)
            ) {
                isGameOver = true;
            }

            // Calcul du score
            if (!p.passed && p.x < heart.x) {
                p.passed = true;
                localScore += 1;
                updateScore(localScore);
            }
        }

        if (isGameOver) {
            ctx.fillStyle = '#ff007f';
            ctx.font = '14px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('GAME OVER', canvas.width / 2, canvas.height / 2 - 10);
            ctx.font = '12px sans-serif';
            ctx.fillText('Cliquez pour rejouer', canvas.width / 2, canvas.height / 2 + 20);
        }

        // On continue la boucle d'animation
        animFrame = requestAnimationFrame(loop);
    }

    function restartGame() {
        isGameOver = false;
        heart.y = 150;
        heart.vy = 0;
        pipes = [];
        localScore = 0;
        updateScore(0);
    }

    // Gestion clic / touch unique
    canvas.onclick = () => {
        if (isGameOver) {
            restartGame();
        } else {
            jump();
        }
    };

    // Nettoyage spécifique de l'écouteur clavier si on change de jeu
    const originalClear = window.clearCurrentGame;
    window.clearCurrentGame = function() {
        window.removeEventListener('keydown', handleKeyDown);
        if (typeof originalClear === 'function') originalClear();
    };

    // Lancement de la boucle
    loop();
}

                // Lancement initial du 1er jeu
                initCatcherGame();
            </script>
        <?php endif; ?>
    </div>
</body>
</html>