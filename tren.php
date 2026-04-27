<?php
$json = file_get_contents("contenido.json");
$trenes = json_decode($json, true);

$id = $_GET['id'] ?? null;
$tren = $trenes[$id] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $tren ? htmlspecialchars($tren["nombre"]) : "Tren" ?> — Exposición Ferroviaria</title>
  <link rel="stylesheet" href="styles.css?v=10">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.css"/>
  <script src="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.js"></script>
</head>
<body class="page-tren">

<?php if ($tren): ?>

  <!-- ===================== PANTALLA DE EMBARQUE ===================== -->
  <div class="boarding-screen" id="boardingScreen">
    <div class="boarding-inner">
      <div class="boarding-ticket">
        <div class="ticket-top">
          <span class="ticket-label">BOLETO DE EMBARQUE</span>
          <span class="ticket-num">#<?= str_pad($id, 4, '0', STR_PAD_LEFT) ?></span>
        </div>
        <div class="ticket-divider"><span></span></div>
        <div class="ticket-destination"><?= htmlspecialchars($tren["nombre"]) ?></div>
        <div class="ticket-era"><?= htmlspecialchars($tren["era"] ?? "Siglo XIX") ?></div>
        <div class="ticket-divider"><span></span></div>
        <div class="ticket-bottom">
          <div>
            <span class="ticket-label">PARTIDA</span>
            <span class="ticket-val">Estación Central</span>
          </div>
          <div class="ticket-train-icon">🚂</div>
          <div>
            <span class="ticket-label">LLEGADA</span>
            <span class="ticket-val">Estación Final</span>
          </div>
        </div>
      </div>
      <button class="btn-embarcar" id="btnEmbarcar" onclick="iniciarViaje()">
        <span class="btn-text">SUBIR AL TREN</span>
        <span class="btn-whistle">🔔</span>
      </button>
      <p class="boarding-hint">Haz clic para comenzar el recorrido</p>
    </div>
  </div>

  <!-- ===================== PANTALLA DE VIAJE ===================== -->
  <div class="journey-screen hidden" id="journeyScreen">

    <!-- Ventana del tren (efecto paisaje corriendo) -->
    <div class="train-window" id="trainWindow">
      <div class="window-frame">
        <div class="window-glass">
          <!-- Video real de fondo -->
          <iframe
            src="https://www.youtube.com/embed/cpL0Gnl76PE?autoplay=1&mute=1&loop=1&controls=0&playlist=cpL0Gnl76PE&showinfo=0&rel=0&modestbranding=1&iv_load_policy=3&disablekb=1"
            frameborder="0"
             allow="autoplay; encrypted-media"
        style="position:absolute; top:50%; left:50%; width:200%; height:100%; transform:translate(-50%,-50%); pointer-events:none; z-index:1;">
          </iframe>
          <!-- Paisaje JS encima (invisible normalmente, visible al acelerar) -->
          <div class="landscape" id="landscape" style="z-index:2; opacity:0;"></div>
          <div class="window-reflections" style="z-index:3;"></div>
          <div class="rain-overlay" id="rainOverlay" style="z-index:4;"></div>
        </div>
        <div class="window-ledge"></div>
      </div>
      <div class="window-curtain left"></div>
      <div class="window-curtain right"></div>
    </div>

    <!-- Interior del tren (contenido principal) -->
    <div class="train-interior">

      <!-- Barra de progreso del viaje -->
      <div class="journey-progress">
        <div class="progress-track">
          <div class="progress-fill" id="progressFill"></div>
          <div class="progress-train" id="progressTrain">🚂</div>
          <?php foreach ($tren["paradas"] as $i => $parada): ?>
            <div class="progress-stop" style="left: <?= ($i / (count($tren["paradas"]) - 1)) * 100 ?>%">
              <div class="pstop-dot" data-index="<?= $i ?>"></div>
              <div class="pstop-name"><?= htmlspecialchars($parada["nombre"]) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Contenido de la parada actual -->
      <div class="station-content" id="stationContent">

        <!-- Anuncio de llegada a estación -->
        <div class="arrival-announcement hidden" id="arrivalAnnouncement">
          <div class="announcement-inner">
            <span class="announcement-icon">📢</span>
            <span class="announcement-text" id="announcementText"></span>
          </div>
        </div>

        <!-- Contenedor de paradas (se llena dinámicamente) -->
        <div id="paradaContainer"></div>

      </div>

      <!-- Controles de navegación -->
      <div class="journey-controls">
        <button class="ctrl-btn" id="btnAnterior" onclick="paradaAnterior()">
          ◀ Estación anterior
        </button>
        <div class="ctrl-stop-name" id="ctrlStopName"></div>
        <button class="ctrl-btn primary" id="btnSiguiente" onclick="paradaSiguiente()">
          Próxima estación ▶
        </button>
      </div>

    </div>

    <!-- Botón volver -->
    <a href="index.php" class="btn-volver">← Volver a la exposición</a>

  </div>

  <!-- Datos PHP para JavaScript -->
  <script>
    const TREN_DATA = <?= json_encode($tren, JSON_UNESCAPED_UNICODE) ?>;
    const TREN_ID   = <?= json_encode($id) ?>;
  </script>

<?php else: ?>
  <div class="container" style="text-align:center; padding:100px 20px;">
    <h1 style="font-family:'Special Elite',serif; color:#c9a66b;">Tren no encontrado</h1>
    <a href="index.php"><button class="btn-embarcar">← Volver</button></a>
  </div>
<?php endif; ?>

  <script src="script.js?v=10"></script>
</body>
</html>