<?php
ob_start();
?>

<body>
    <header id="header_nav">
        <div id="header_left">
            <a href="/shop/">
                <h1><span>Minimal</span><br><span>SHOP</span></h1>
            </a>
        </div>
        <div id="header_right">
            <ul id="header_nav_ul">
                <li id="search_bar_container">
                    <input type="text" name="search" id="search_bar" autocomplete="off" required>
                    <button type="submit"><i class="fas fa-search"></i></button>
                </li>
                <li>
                    <a href="marques/">NOS PRODUITS</a>
                </li>
                <li>
                    <a href="profil/">PROFIL</a>
                </li>
                <li>
                    <a href="panier/">PANIER</a>
                </li>
            </ul>
        </div>
    </header>