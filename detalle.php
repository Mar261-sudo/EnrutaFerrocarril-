<?php
$json = file_get_contents("contenido.json");
$trenes = json_decode($json, true);

$trenId = $_GET['tren'] ?? null;
$imgIndex = $_GET['img'] ?? 0;

$tren = $trenes[$trenId] ?? null;
$imagen = $tren["imagenes"][$imgIndex] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Detalle</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">

<?php if ($tren && $imagen): ?>

    <h1 class="title"><?= $imagen["titulo"] ?></h1>

    <img src="<?= $imagen["src"] ?>" 
         style="width:100%; border-radius:15px; margin-top:20px;">

    <p style="margin-top:20px; opacity:0.8;">
        <?= $imagen["descripcion"] ?>
    </p>

    <br>

    <a href="index.php?id=<?= $trenId ?>">
        <button>⬅ Volver al recorrido</button>
    </a>

<?php else: ?>
    <h1>Imagen no encontrada</h1>
<?php endif; ?>

</div>

</body>
</html>