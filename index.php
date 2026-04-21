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
  <link rel="stylesheet" href="styles.css?v=10">
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

  <!-- Header -->
  <header class="main-header">
    <div class="header-inner">
      <div class="header-emblem">🚂</div>
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
      <span class="board-clock" id="reloj">00:00:00</span>
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
      <div class="timeline-train" id="timelineTrain">🚂</div>
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
    <p>Exposición Ferroviaria &mdash; Patrimonio sobre Rieles</p>
    <div class="footer-tracks">
      <div class="track-line"></div>
      <div class="track-dots"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
      <div class="track-line"></div>
    </div>
  </footer>

  <script src="script.js?v=10"></script>
</body>
</html>