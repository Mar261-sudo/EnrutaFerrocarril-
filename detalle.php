<?php
$json = file_get_contents("contenido.json");
$trenes = json_decode($json, true);

$trenId   = $_GET['tren'] ?? null;
$paradaId = $_GET['parada'] ?? 0;

$tren   = $trenes[$trenId] ?? null;
$parada = $tren["paradas"][$paradaId] ?? null;

// ── Detectar si esta parada tiene panorama 360 o imagen plana ──
$panoramaUrl  = $parada["panorama"] ?? "";
$esPanorama   = !empty($panoramaUrl);
$imagenPlana  = $parada["imagen_thumb"] ?? $panoramaUrl; // usa imagen_thumb si existe, si no cae a panorama
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $parada ? htmlspecialchars($parada["nombre"]) : "Detalle" ?> — Ferroviaria</title>
  <link rel="stylesheet" href="styles.css?v=11">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

  <?php if ($esPanorama): ?>
  <!-- Pannellum solo se carga si hay panorama 360 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.css"/>
  <script src="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.js"></script>
  <?php endif; ?>

  <style>
    /* Imagen plana: ocupa el mismo espacio que el visor 360 */
    .detalle-imagen-plana {
      width: 100%;
      height: 100%;
      object-fit: contain;       /* muestra el collage completo sin recortar */
      background: #111;          /* fondo oscuro igual que pannellum */
      display: block;
      border-radius: inherit;
    }
  </style>
</head>
<body class="page-detalle">

  <!-- Control de tamaño de letra -->
  <div class="font-controls" aria-label="Tamaño de letra">
    <button class="btn-font-decrease" onclick="decreaseFontSize()" title="Reducir letra">A−</button>
    <span class="fc-divider"></span>
    <span class="font-size-indicator">A</span>
    <span class="fc-divider"></span>
    <button class="btn-font-increase" onclick="increaseFontSize()" title="Agrandar letra">A+</button>
  </div>

<?php if ($tren && $parada): ?>

  <div class="detalle-layout">

    <!-- Zona visual: 360° O imagen plana según el JSON -->
    <div class="detalle-360-wrapper">

      <?php if ($esPanorama): ?>
        <!-- ── MODO 360° ── -->
        <div class="detalle-360-label">
          <span class="label-icon">⟳</span> Vista 360° — Gira para explorar
        </div>
        <div id="panoramaDetalle" class="panorama-full"></div>

      <?php else: ?>
        <!-- ── MODO IMAGEN PLANA (collage, foto, etc.) ── -->
        <div class="detalle-360-label">
          <span class="label-icon">🖼</span> Imagen de referencia
        </div>
        <img
          src="<?= htmlspecialchars($imagenPlana) ?>"
          alt="<?= htmlspecialchars($parada["nombre"]) ?>"
          class="detalle-imagen-plana"
        >
      <?php endif; ?>

    </div>

    <!-- Panel informativo lateral -->
    <div class="detalle-panel">

      <div class="detalle-breadcrumb">
        <a href="index.php">Exposición</a>
        <span>›</span>
        <a href="tren.php?id=<?= $trenId ?>"><?= htmlspecialchars($tren["nombre"]) ?></a>
        <span>›</span>
        <span><?= htmlspecialchars($parada["nombre"]) ?></span>
      </div>

      <h1 class="detalle-title"><?= htmlspecialchars($parada["nombre"]) ?></h1>

      <?php if (!empty($parada["año"])): ?>
        <div class="detalle-year"><?= htmlspecialchars($parada["año"]) ?></div>
      <?php endif; ?>

      <p class="detalle-desc"><?= nl2br(htmlspecialchars($parada["descripcion"])) ?></p>

      <?php if (!empty($parada["datos"])): ?>
      <div class="detalle-facts">
        <h3 class="facts-title"><img src="icons/icon-datos.svg" alt="datos" class="icon-sm"> Datos de esta parada</h3>
        <ul class="facts-list">
          <?php foreach ($parada["datos"] as $dato): ?>
            <li><?= htmlspecialchars($dato) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

      <?php if (!empty($parada["curiosidad"])): ?>
      <div class="detalle-curiosity">
        <span class="curiosity-icon"><img src="icons/icon-linterna.svg" alt="curiosidad" class="icon-sm"></span>
        <p><?= htmlspecialchars($parada["curiosidad"]) ?></p>
      </div>
      <?php endif; ?>

      <div class="detalle-nav">
        <?php if ($paradaId > 0): ?>
          <a href="detalle.php?tren=<?= $trenId ?>&parada=<?= $paradaId - 1 ?>" class="btn-detalle-nav">
            ◀ Parada anterior
          </a>
        <?php endif; ?>
          <a href="tren.php?id=<?= $trenId ?>" class="btn-volver-tren">
            <img src="icons/icon-tren.svg" alt="tren">
            Volver al tren
          </a>

        <?php if ($paradaId < count($tren["paradas"]) - 1): ?>
          <a href="detalle.php?tren=<?= $trenId ?>&parada=<?= $paradaId + 1 ?>" class="btn-detalle-nav primary">
            Siguiente parada ▶
          </a>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <?php if ($esPanorama): ?>
  <script>
    const PANORAMA_URL   = <?= json_encode($panoramaUrl) ?>;
    const PANORAMA_PITCH = <?= json_encode($parada["panorama_pitch"] ?? 0) ?>;

    document.addEventListener("DOMContentLoaded", () => {
      pannellum.viewer('panoramaDetalle', {
        type:        "equirectangular",
        panorama:    PANORAMA_URL,
        autoLoad:    true,
        autoRotate:  -2,
        pitch:       PANORAMA_PITCH,
        hfov:        100,
        showControls: true
      });
    });
  </script>
  <?php endif; ?>

<?php else: ?>
  <div style="text-align:center; padding:100px 20px;">
    <h1 style="font-family:'Special Elite',serif; color:#c9a66b;">Parada no encontrada</h1>
    <a href="index.php"><button class="btn-embarcar">← Volver</button></a>
  </div>
<?php endif; ?>

  <script src="script.js?v=11"></script>
</body>
</html>