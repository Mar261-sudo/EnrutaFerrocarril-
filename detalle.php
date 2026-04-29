<?php
$json = file_get_contents("contenido.json");
$trenes = json_decode($json, true);
 
$trenId   = $_GET['tren'] ?? null;
$paradaId = $_GET['parada'] ?? 0;
 
$tren   = $trenes[$trenId] ?? null;
$parada = $tren["paradas"][$paradaId] ?? null;
 
// Soporta tanto "panorama" como "imagen" (compatibilidad tren 8)
$panoramaUrl = $parada["panorama"] ?? "";
$imagenPlana = $parada["imagen_thumb"] ?? $parada["imagen"] ?? "";
$esPanorama  = !empty($panoramaUrl);
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.css"/>
  <script src="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.js"></script>
 
  
</head>
<body class="page-detalle">
 
  <div class="font-controls" aria-label="Tamaño de letra">
    <button class="btn-font-decrease" onclick="decreaseFontSize()" title="Reducir letra">A−</button>
    <span class="fc-divider"></span>
    <span class="font-size-indicator">A</span>
    <span class="fc-divider"></span>
    <button class="btn-font-increase" onclick="increaseFontSize()" title="Agrandar letra">A+</button>
  </div>
 
<?php if ($tren && $parada): ?>
 
  <div class="detalle-layout">
 
    <div class="detalle-360-wrapper" id="vistaWrapper">
 
      <?php if ($esPanorama): ?>
 
        <!-- Capa 1: imagen estática (preview de la panorámica) -->
        <img
          class="capa-imagen"
          id="capaImagen"
          src="<?= htmlspecialchars($panoramaUrl) ?>"
          alt="<?= htmlspecialchars($parada["nombre"]) ?>"
        >
 
        <!-- Capa 2: visor 360° (empieza invisible) -->
        <div class="capa-360" id="capa360">
          <div id="panoramaDetalle" style="width:100%;height:100%;"></div>
        </div>
 
        <!-- Barra de progreso del lazo -->
        <div class="carga-barra-wrap" id="cargaBarraWrap">
          <div class="carga-barra" id="cargaBarra"></div>
        </div>
        <div class="carga-label" id="cargaLabel">Preparando vista 360°…</div>
 
      <?php else: ?>
 
        <!-- Solo imagen plana, sin lazo -->
        <img
          class="capa-imagen"
          src="<?= htmlspecialchars($imagenPlana) ?>"
          alt="<?= htmlspecialchars($parada["nombre"]) ?>"
          style="object-fit:contain;"
        >
 
      <?php endif; ?>
 
      <!-- Overlay invitación -->
      <div class="expand-overlay" onclick="expandirVista()">
        <div class="expand-overlay-icon"><?= $esPanorama ? '⟳' : '' ?></div>
        <div class="expand-overlay-text">
          <?= $esPanorama ? 'Clic para explorar en 360°' : 'Clic para ampliar imagen' ?>
        </div>
      </div>
 
      <!-- Botón minimizar -->
      <button class="btn-cerrar-vista" onclick="cerrarVista(event)">✕ Minimizar</button>
    </div>
 
    <!-- Panel informativo -->
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
        <h3 class="facts-title">
          <img src="icons/icon-datos.svg" alt="datos" class="icon-sm"> Datos de esta parada
        </h3>
        <ul class="facts-list">
          <?php foreach ($parada["datos"] as $dato): ?>
            <li><?= htmlspecialchars($dato) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
 
      <?php if (!empty($parada["curiosidad"])): ?>
      <div class="detalle-curiosity">
        <span class="curiosity-icon">
          <img src="icons/icon-linterna.svg" alt="curiosidad" class="icon-sm">
        </span>
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
          <img src="icons/icon-tren.svg" alt="tren"> Volver al tren
        </a>
        <?php if ($paradaId < count($tren["paradas"]) - 1): ?>
          <a href="detalle.php?tren=<?= $trenId ?>&parada=<?= $paradaId + 1 ?>" class="btn-detalle-nav primary">
            Siguiente parada ▶
          </a>
        <?php endif; ?>
      </div>
 
    </div>
  </div>
 
  <script>
    const esPanorama    = <?= $esPanorama ? 'true' : 'false' ?>;
    const panoramaUrl   = <?= json_encode($panoramaUrl) ?>;
    const panoramaPitch = <?= json_encode($parada["panorama_pitch"] ?? 0) ?>;
    const LAZO_MS       = 12000; // ms que se ve la imagen plana antes del 360°
 
    let expandido    = false;
    let panoramaListo = false;
 
    function expandirVista() {
      if (expandido) return;
      expandido = true;
 
      const wrapper = document.getElementById('vistaWrapper');
      wrapper.classList.add('expandido');
      wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
 
      if (!esPanorama) return; // imagen plana: nada más que hacer
 
      // ── Lazo de tiempo: mostrar imagen 3s, luego activar 360° ──
      const barraWrap = document.getElementById('cargaBarraWrap');
      const barra     = document.getElementById('cargaBarra');
      const label     = document.getElementById('cargaLabel');
 
      // Mostrar barra y label
      barraWrap.classList.add('activa');
      label.classList.add('activa');
 
      // Animar barra al 100% en LAZO_MS ms
      requestAnimationFrame(() => {
        barra.style.transition = `width ${LAZO_MS}ms linear`;
        barra.style.width = '100%';
      });
 
      // Iniciar Pannellum en paralelo (carga en background)
      pannellum.viewer('panoramaDetalle', {
        type:         "equirectangular",
        panorama:     panoramaUrl,
        autoLoad:     true,
        autoRotate:   -2,
        pitch:        panoramaPitch,
        hfov:         100,
        showControls: true
      });
 
      // Después del lazo: ocultar imagen estática, mostrar 360°
      setTimeout(() => {
        barraWrap.classList.remove('activa');
        label.classList.remove('activa');
 
        const capa360 = document.getElementById('capa360');
        capa360.classList.add('visible');
 
        // Desvanecer imagen estática
        document.getElementById('capaImagen').style.opacity = '0';
      }, LAZO_MS);
    }
 
    function cerrarVista(e) {
      e.stopPropagation();
      expandido = false;
      document.getElementById('vistaWrapper').classList.remove('expandido');
    }
  </script>
 
<?php else: ?>
  <div style="text-align:center; padding:100px 20px;">
    <h1 style="font-family:'Special Elite',serif; color:#c9a66b;">Parada no encontrada</h1>
    <a href="index.php"><button class="btn-embarcar">← Volver</button></a>
  </div>
<?php endif; ?>
 
  <script src="script.js?v=11"></script>
</body>
</html>