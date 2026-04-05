<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ouderavond</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="main">
    <div class="container">
        <div class="begin">
            <img src="pictures/bear.png" alt="Legends of gaming logo" width="150">
            <h1>Legends Of <br> Gaming</h1>
        </div>
        <div class="middle-group">
            <div class="item"><a href="index.php">Home</a></div>
            <div class="selected">Shop</div>
            <div class="item"><a href="winkelmandje.php">Winkelmandje</a></div>
        </div>

    <div class="end"> Log In</div>
    </div>
    <section>
        <?php
        include 'functions.php';
        crudMain();
        ?>
    </section>



    <footer>©2025 Ufukcan Kaynar</footer>
</body>
</html>