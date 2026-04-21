<?php
$json = file_get_contents("contenido.json"); 
$trenes = json_decode($json, true);

$id = $_GET['id'] ?? null;
$tren = $trenes[$id] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">
<title>Ferrocarril</title>
<link rel="stylesheet" href="./styles.css?v=3">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.css"/>
<script src="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.js"></script>
</head>
<body>

<!-- Tren animado -->
<div class="train">
  <img src="/Prueba/images/train.png" alt="tren">
  <div class="smoke">
    <span></span><span></span><span></span>
  </div>
</div>

<!--Tuerca -->
<div class="gear">
    <img src="/Prueba/images/settings.png" alt="tuerca">
</div>

<div class="container">

<?php if ($tren): ?>

    <header class="hero fade-in delay-1">
        <h1 class="title"><?= $tren["nombre"] ?></h1>
        <p class="subtitle">Un viaje cinematográfico por el ferrocarril</p>
    </header>

    <section class="section historia fade-in delay-2">
        <h2 class="station"> Estación : Historia</h2>
        <p><?= $tren["historia"] ?></p>
    </section>

    <section class="section fade-in delay-4">
        <h2>Datos Técnicos</h2>
        <div class="cards">
            <?php foreach ($tren["datos"] as $dato): ?>
                <div class="card"><?= $dato ?></div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section fade-in delay-5">
        <h2>Galería</h2>
        <div class="gallery">
           <?php foreach ($tren["imagenes"] as $index => $img): ?>
                <a href="detalle.php?tren=<?= $id ?>&img=<?= $index ?>">
                <img src="<?= $img["src"] ?>" alt="<?= $img["titulo"] ?>">
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section curiosidades fade-in delay-6">
        <h2>¿Sabías que...?</h2>
        <p id="datoCurioso"></p>
        <button onclick="mostrarCuriosidad()">Descubrir</button>
        
    </section>

<?php else: ?>
    <h1>Tren no encontrado</h1>
<?php endif; ?>
</div>

<div id="panorama"></div>

<script>
const curiosidades = <?= json_encode($tren["curiosidades"] ?? []) ?>;
</script>

<script src="./script.js"></script>

</body>
</html>