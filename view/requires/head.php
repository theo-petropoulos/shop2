<!DOCTYPE html>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="/shop/assets/css/globals.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" 
            href="/shop/assets/css/<?=explode('/', $f3->hive()['PATH'])[1] ? explode('/', $f3->hive()['PATH'])[1] : 'index';?>.css?v=<?php echo time(); ?>">
        <link rel="icon" href="/shop/assets/images/icon.png" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DotGothic16&display=swap" rel="stylesheet"> 
        <script src="https://kit.fontawesome.com/9ddb75d515.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js"></script>
        <title><?=$title;?> | MINIMAL SHOP</title>
    </head>