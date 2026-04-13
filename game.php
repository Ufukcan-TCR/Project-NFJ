<?php
include 'functions.php';

if (!isset($_GET['id'])) {
    echo "Geen game gekozen.";
    exit;
}

$id = $_GET['id'];
$game = getRecord($id);

if (!$game) {
    echo "Game niet gevonden.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($game['naam']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="begin">
            <img src="pictures/bear.png" alt="Legends of gaming logo" width="150">
            <h1>Legends Of <br> Gaming</h1>
        </div>
        <div class="middle-group">
            <div class="item"><a class='pagina' href="index.php">Home</a></div>
            <div class="selected">Shop</div>
            <div class="item"><a class='pagina' href="winkelmandje.php">Winkelmandje</a></div>
        </div>

    <div class="end"> Log In</div>
    </div>

<section>
    <h1 class="info"><?php echo htmlspecialchars($game['naam']); ?></h1>

    <img class="img-info" src="pictures/<?php echo htmlspecialchars($game['img']); ?>" width="300">

    <p class="info"><strong>Prijs:</strong> €<?php echo htmlspecialchars($game['prijs']); ?></p>
    <p class="info"><strong>Genre:</strong> <?php echo htmlspecialchars($game['genre']); ?></p>
    <p class="info"><strong>Players:</strong> <?php echo htmlspecialchars($game['player']); ?></p>
    <p class="info"><strong>Platform:</strong> <?php echo htmlspecialchars($game['consol']); ?></p>

    <p class="info"><strong>Beschrijving:</strong><br>
    <?php echo htmlspecialchars($game['description']); ?>
    </p>
</section>

    <a id="back" href="shop.php"><img id="back-img" src="pictures/X.png" alt=""></a>
<br>
<footer>©2025 Ufukcan Kaynar</footer>
</body>
</html>