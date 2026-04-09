<?php
include 'login.php';

$clicked = isset($_POST["login"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crud Games</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="main">
    <div class="container">

        <div class="begin">
            <img src="pictures/bear.png" alt="Legends of gaming logo" width="150">
            <h1>Legends Of <br> Gaming</h1>
        </div>

        <div class="middle-group">
            <div class="selected">Home</div>
            <div class="item"><a class='pagina' href="shop.php">Shop</a></div>
            <div class="item"><a class='pagina' href="winkelmandje.php">Winkelmandje</a></div>
        </div>

        <!-- LOGIN BUTTON COMES FROM FUNCTION -->
        <?php Login($clicked); ?>

    </div>

    <footer>©2025 Ufukcan Kaynar</footer>
</body>
</html>