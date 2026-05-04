<?php
$json = file_get_contents("contenido.json");
$trenes = json_decode($json, true);

$id = $_GET['id'] ?? null;
$tren = $trenes[$id] ?? null;

$totalParadas = $tren ? count($tren["paradas"]) : 0;
$divisor = max($totalParadas - 1, 1);

$idsOrdenados = array_keys($trenes);
$posActual = array_search((string)$id, $idsOrdenados);
$idSiguienteTren = null;
if ($posActual !== false && isset($idsOrdenados[$posActual + 1])) {
  $idSiguienteTren = $idsOrdenados[$posActual + 1];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $tren ? htmlspecialchars($tren["nombre"]) : "Tren" ?> — Exposición Ferroviaria</title>
  <link rel="icon" type="image/png" href="images/logo.png">
  <link rel="stylesheet" href="styles.css?v=13">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.css"/>
  <script src="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.js"></script>
</head>
<body class="page-tren">

  <div class="font-controls" aria-label="Tamaño de letra">
    <button class="btn-font-decrease" onclick="decreaseFontSize()" title="Reducir letra">A−</button>
    <span class="fc-divider"></span>
    <span class="font-size-indicator">A</span>
    <span class="fc-divider"></span>
    <button class="btn-font-increase" onclick="increaseFontSize()" title="Agrandar letra">A+</button>
  </div>

<?php if ($tren): ?>

  <div class="boarding-screen" id="boardingScreen">
    <div class="boarding-inner">

      <div class="vintage-ticket">

        <div class="ticket-top">
          <div class="ticket-header-left">
            <span class="ticket-company">FERROCARRIL DEL PACÍFICO</span>
            <span class="ticket-label">BOLETO DE EMBARQUE</span>
          </div>
          <div class="ticket-num-stamp">
            <span class="ticket-num-label">Nº</span>
            <span class="ticket-num"><?= str_pad($id, 4, '0', STR_PAD_LEFT) ?></span>
          </div>
        </div>

        <div class="ticket-stripe"></div>

        <div class="ticket-destination-block">
          <span class="ticket-dest-label">DESTINO</span>
          <div class="ticket-destination"><?= htmlspecialchars($tren["nombre"]) ?></div>
          <div class="ticket-era"><?= htmlspecialchars($tren["era"] ?? "Siglo XIX") ?></div>
        </div>

        <div class="ticket-tear-line">
        <div class="ticket-tear-dashes"></div>
        </div>
         
        <div class="ticket-bottom">
          <div class="ticket-field">
            <span class="ticket-field-label">PARTIDA</span>
            <span class="ticket-field-value">Estación Central</span>
          </div>

          <div class="ticket-train-icon">
            <img src="images/icon-tren.png" alt="tren" class="icon-btn">
          </div>
          <div class="ticket-field ticket-field-right">
            <span class="ticket-field-label">LLEGADA</span>
            <span class="ticket-field-value">Estación Final</span>
          </div>
        </div>

        <div class="ticket-stub">
          <span class="ticket-stub-text">
            <?= $totalParadas ?> PARADA<?= $totalParadas !== 1 ? 'S' : '' ?> · CLASE ÚNICA
          </span>
          <span class="ticket-stub-icon">✦</span>
        </div>

      </div>

      <button class="btn-embarcar" id="btnEmbarcar" onclick="iniciarViaje()">
        <span class="btn-text">SUBIR AL FERROCARRIL</span>
         <span class="btn-whistle">
            <img src="images/icon-campana.png" alt="campana" class="icon-btn">
         </span>
      </button>
      <p class="boarding-hint">Haz clic para comenzar el recorrido</p>
    </div>
  </div>

  <div class="journey-screen hidden" id="journeyScreen">

    <div class="train-window" id="trainWindow">
      <div class="window-frame">
        <div class="window-glass">
          <video
            id="videoVentanaTren"
            src="video/video.mp4"
            autoplay
            muted
            loop
            playsinline
            preload="auto"
            style="position:absolute; top:50%; left:50%; width:200%; height:100%; transform:translate(-50%,-50%); pointer-events:none; z-index:1;">
          </video>

          <div class="landscape" id="landscape" style="z-index:2; opacity:0;"></div>
          <div class="window-reflections" style="z-index:3;"></div>
          <div class="rain-overlay" id="rainOverlay" style="z-index:4;"></div>
        </div>
        <div class="window-ledge"></div>
      </div>
      <div class="window-curtain left"></div>
      <div class="window-curtain right"></div>
    </div>

    <div class="train-interior">

      <div class="journey-progress">
        <div class="progress-track">
          <div class="progress-fill" id="progressFill"></div>
          <div class="progress-train" id="progressTrain">
            <img src="images/icon-tren.png" alt="tren" class="icon-progress-train">
          </div>
          <?php foreach ($tren["paradas"] as $i => $parada): ?>
            <div class="progress-stop" style="left: <?= $totalParadas === 1 ? 50 : ($i / $divisor) * 100 ?>%">
              <div class="pstop-dot" data-index="<?= $i ?>"></div>
              <div class="pstop-name"><?= htmlspecialchars($parada["nombre"]) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="station-content" id="stationContent">
        <div class="arrival-announcement hidden" id="arrivalAnnouncement">
          <div class="announcement-inner">
            <span class="announcement-icon">
              <img src="images/icon-bocina.png" alt="anuncio" class="icon-sm">
            </span>
            <span class="announcement-text" id="announcementText"></span>
          </div>
        </div>
        <div id="paradaContainer"></div>
      </div>

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

    <a href="index.php" class="btn-volver">← Volver a la exposición</a>

  </div>

  <script>
    const TREN_DATA = <?= json_encode($tren, JSON_UNESCAPED_UNICODE) ?>;
    const TREN_ID   = <?= json_encode($id) ?>;
    const TREN_ID_SIGUIENTE = <?= json_encode($idSiguienteTren) ?>;
  </script>

<?php else: ?>
  <div class="container" style="text-align:center; padding:100px 20px;">
    <h1 style="font-family:'Special Elite',serif; color:#c9a66b;">Ferrocarril no encontrado</h1>
    <a href="index.php"><button class="btn-embarcar">← Volver</button></a>
  </div>
<?php endif; ?>

  <script src="script.js?v=13"></script>
</body>
</html>