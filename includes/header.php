<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Dashboard Generator'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1 class="logo">Dashboard Generator</h1>
            <div class="nav-links">
                <a href="index.php" class="nav-link">Inicio</a>
                <a href="#" class="nav-link">Plantillas</a>
                <a href="#" class="nav-link">Historial</a>
            </div>
        </div>
    </nav>
    <main class="main-content">
