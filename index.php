<?php
$json = file_get_contents("contenido.json");
$trenes = json_decode($json, true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Exposición Ferroviaria</title>
  
  <link rel="stylesheet" href="styles.css?v=11">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
</head>
<body class="page-index">
  <!-- Partículas de humo de fondo -->
  <div class="bg-smoke">
    <span></span><span></span><span></span><span></span><span></span>
  </div>

  <!-- Rieles decorativos -->
  <div class="rails-bg">
    <div class="rail"></div>
    <div class="rail"></div>
    <div class="ties"></div>
  </div>

    <!--Banner institucional-->
    <div class="banner-institucional"> 
      <div class="banner-patrocinador">
        <img src="images/enRuta.png" alt="Empresa" class="banner-logo">
        
      </div>
      <div class="banner-divider-vertical">
      </div>
      <div class="banner-patrocinador">
        <img src="images/biblioteca.png" alt="Biblioteca" class="banner-logo">
        
      </div>  
    </div>

    <!-- Control de tamaño de letra -->
    <div class="font-controls" aria-label="Tamaño de letra">
      <button class="btn-font-decrease" onclick="decreaseFontSize()" title="Reducir letra">A−</button>
      <span class="fc-divider"></span>
      <span class="font-size-indicator">A</span>
      <span class="fc-divider"></span>
      <button class="btn-font-increase" onclick="increaseFontSize()" title="Agrandar letra">A+</button>
    </div>

  <!-- Header -->
  <header class="main-header">
    <div class="header-inner">
      <div class="header-emblem"><img src="icons/logo.ico" alt="Locomotora"</div>
      <div class="header-text">
        <p class="header-sub">Exposición</p>
        <h1 class="header-title">Ferrocarriles<br><em>Históricos</em></h1>
        <p class="header-tagline">Un viaje a través del tiempo sobre rieles de acero</p>
      </div>
    </div>
    <div class="header-divider">
      <span>— ✦ —</span>
    </div>
  </header>

  <!-- Tablero de horarios estilo vintage -->
  <section class="departure-board">
    <div class="board-header">
      <span class="board-label">SALIDAS</span>
      <span class="board-label">DESTINOS</span>
    </div>

    <div class="board-rows" id="boardRows">
      <?php foreach ($trenes as $id => $tren): ?>
      <a href="tren.php?id=<?= $id ?>" class="board-row" data-delay="<?= $id ?>">
        <div class="row-number"><?= str_pad($id, 3, '0', STR_PAD_LEFT) ?></div>
        <div class="row-flip">
          <div class="row-name"><?= htmlspecialchars($tren["nombre"]) ?></div>
        </div>
        <div class="row-era"><?= htmlspecialchars($tren["era"] ?? "Siglo XIX") ?></div>
        <div class="row-status">
          <span class="status-dot"></span>
          EN SERVICIO
        </div>
        <div class="row-arrow">→</div>
      </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Línea de tiempo decorativa -->
  <section class="timeline-strip">
    <div class="timeline-track">
      <div class="timeline-train" id="timelineTrain"><img src="icons/icon-tren.svg" alt="tren" class="icon-tren-timeline"></div>
      <?php foreach ($trenes as $id => $tren): ?>
        <div class="timeline-stop">
          <div class="stop-dot"></div>
          <div class="stop-year"><?= htmlspecialchars($tren["año"] ?? "1890") ?></div>
          <div class="stop-name"><?= htmlspecialchars($tren["nombre"]) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Footer -->
  <footer class="main-footer">
    <p class="footer-text-principal">Exposición Ferroviaria &mdash; Patrimonio sobre Rieles</p>
    <div class="footer-tracks">
      <div class="track-line"></div>
      <div class="track-dots"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
      <div class="track-line"></div>
    </div>

  </footer>

  <script src="script.js?v=11"></script>
</body>
</html>