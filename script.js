/* ================================================================
   FERROCARRIL — Script Principal
   ================================================================ */

document.addEventListener("DOMContentLoaded", () => {

  const body = document.body;

  /* ================================================================
     TAMAÑO DE LETRA — Agrandar / Minimizar
     ================================================================ */
  const FONT_STEPS   = [12, 14, 16, 19, 22];   // px sobre el elemento <html>
  const FONT_LABELS  = ["A−−", "A−", "A", "A+", "A++"];
  const STORAGE_KEY  = "ferro_font_size";
  const htmlEl       = document.documentElement;

  let currentSizeIndex = 2; // índice 2 = 16px (normal)

  // Restaurar tamaño guardado
  const savedIdx = parseInt(localStorage.getItem(STORAGE_KEY));
  if (!isNaN(savedIdx) && savedIdx >= 0 && savedIdx < FONT_STEPS.length) {
    currentSizeIndex = savedIdx;
  }

  function applyFontSize(index) {
    htmlEl.style.fontSize = FONT_STEPS[index] + "px";
    localStorage.setItem(STORAGE_KEY, index);

    document.querySelectorAll(".btn-font-increase").forEach(btn => {
      btn.disabled = index >= FONT_STEPS.length - 1;
    });
    document.querySelectorAll(".btn-font-decrease").forEach(btn => {
      btn.disabled = index <= 0;
    });
    document.querySelectorAll(".font-size-indicator").forEach(el => {
      el.textContent = FONT_LABELS[index];
    });
  }

  window.increaseFontSize = function () {
    if (currentSizeIndex < FONT_STEPS.length - 1) {
      currentSizeIndex++;
      applyFontSize(currentSizeIndex);
    }
  };

  window.decreaseFontSize = function () {
    if (currentSizeIndex > 0) {
      currentSizeIndex--;
      applyFontSize(currentSizeIndex);
    }
  };

  // Aplicar tamaño inicial
  applyFontSize(currentSizeIndex);

  /* ================================================================
     RELOJ EN VIVO (index.php)
     ================================================================ */
  const relojEl = document.getElementById("reloj");
  if (relojEl) {
    function actualizarReloj() {
      const now = new Date();
      const h = String(now.getHours()).padStart(2, '0');
      const m = String(now.getMinutes()).padStart(2, '0');
      const s = String(now.getSeconds()).padStart(2, '0');
      relojEl.textContent = `${h}:${m}:${s}`;
    }
    actualizarReloj();
    setInterval(actualizarReloj, 1000);
  }

  /* ================================================================
     TREN EN LÍNEA DE TIEMPO (index.php)
     ================================================================ */
  const timelineTrain = document.getElementById("timelineTrain");
  const timelineStops = document.querySelectorAll(".timeline-stop");

  if (timelineTrain && timelineStops.length > 0) {
    let currentStop = 0;

    function moverTrenTimeline() {
      const stop = timelineStops[currentStop];
      if (!stop) return;
      const track     = stop.closest(".timeline-track");
      const trackRect = track.getBoundingClientRect();
      const stopRect  = stop.getBoundingClientRect();
      const relativeLeft = (stopRect.left - trackRect.left) / trackRect.width * 100;

      timelineTrain.style.left = relativeLeft + "%";

      timelineStops.forEach((s, i) => {
        const dot = s.querySelector(".stop-dot");
        if (dot) dot.style.background = i <= currentStop ? "var(--ocre)" : "var(--ocre-oscuro)";
      });

      currentStop = (currentStop + 1) % timelineStops.length;
    }

    setTimeout(moverTrenTimeline, 800);
    setInterval(moverTrenTimeline, 3000);
  }

  /* ================================================================
     PANTALLA DE EMBARQUE Y VIAJE (tren.php)
     ================================================================ */
  if (typeof TREN_DATA === "undefined") return;

  const boardingScreen = document.getElementById("boardingScreen");
  const journeyScreen  = document.getElementById("journeyScreen");

  let paradaActual  = 0;
  const paradas     = TREN_DATA.paradas || [];
  let panoramaViewer = null;

  window.iniciarViaje = function () {
    if (!boardingScreen) return;

    const btn = document.getElementById("btnEmbarcar");
    if (btn) {
      btn.style.transform = "scale(0.95)";
      setTimeout(() => { btn.style.transform = ""; }, 150);
    }

    boardingScreen.classList.add("leaving");

    setTimeout(() => {
      boardingScreen.style.display = "none";
      journeyScreen.classList.remove("hidden");
      generarPaisaje();
      mostrarParada(0, false);
      activarLluvia();
    }, 800);
  };

  function generarPaisaje() {
    const landscape = document.getElementById("landscape");
    if (!landscape) return;

    const colores = {
      cielo:    "#1a1411",
      horizonte:"#2b1f1a",
      terreno:  "#3d2e28",
      arbol:    "#2a3d2a",
      edificio: "#1e2a38",
      montaña:  "#1a2030"
    };

    let html = "";

    html += `<div style="position:absolute;top:0;left:0;width:100%;height:50%;background:linear-gradient(to bottom,#0a0a12,${colores.horizonte});"></div>`;

    for (let i = 0; i < 80; i++) {
      const x  = Math.random() * 3000;
      const y  = Math.random() * 50;
      const s  = Math.random() * 2 + 0.5;
      const op = Math.random() * 0.7 + 0.3;
      html += `<div style="position:absolute;left:${x}px;top:${y}%;width:${s}px;height:${s}px;border-radius:50%;background:white;opacity:${op};"></div>`;
    }

    html += `<div style="position:absolute;left:200px;top:10%;width:30px;height:30px;border-radius:50%;background:radial-gradient(circle at 35% 35%,#f5deb3,#c9a66b);box-shadow:0 0 20px rgba(201,166,107,0.4);"></div>`;

    const montañas = [[0,"#101820"],[150,"#121a22"],[300,"#141c24"],[500,"#101820"],
                      [700,"#0e1618"],[900,"#121a22"],[1100,"#0c1416"],[1300,"#101820"],
                      [1500,"#121a22"],[1700,"#101820"],[1900,"#141c24"],[2100,"#101820"],
                      [2300,"#121a22"],[2500,"#0e1618"],[2700,"#141c24"],[2900,"#101820"]];
    montañas.forEach(([x, col]) => {
      const w = 300 + Math.random() * 400;
      const h = 60  + Math.random() * 80;
      html += `<div style="position:absolute;left:${x}px;bottom:35%;width:${w}px;height:${h}px;background:${col};clip-path:polygon(0% 100%,50% 0%,100% 100%);"></div>`;
    });

    html += `<div style="position:absolute;bottom:0;left:0;width:100%;height:40%;background:linear-gradient(to bottom,#1a1210,#0a0a08);"></div>`;

    const elementos = [];
    for (let i = 0; i < 60; i++) {
      const x    = i * 50 + Math.random() * 30;
      const tipo = Math.random();
      if (tipo < 0.4) {
        const h = 40 + Math.random() * 50;
        const w = 12 + Math.random() * 10;
        elementos.push(`
          <div style="position:absolute;left:${x}px;bottom:38%;width:${w}px;height:${h}px;background:radial-gradient(ellipse 50% 60% at 50% 40%,${colores.arbol},#1a2a1a);border-radius:50% 50% 20% 20%;"></div>
          <div style="position:absolute;left:${x + w/2 - 2}px;bottom:37%;width:4px;height:15px;background:#1a1008;"></div>
        `);
      } else if (tipo < 0.6) {
        const h = 50 + Math.random() * 30;
        elementos.push(`
          <div style="position:absolute;left:${x}px;bottom:38%;width:3px;height:${h}px;background:#1a1210;"></div>
          <div style="position:absolute;left:${x - 8}px;bottom:${38 + (h/5)}%;width:18px;height:2px;background:#1a1210;"></div>
        `);
      } else if (tipo < 0.75) {
        const bh = 30 + Math.random() * 60;
        const bw = 20 + Math.random() * 40;
        elementos.push(`<div style="position:absolute;left:${x}px;bottom:38%;width:${bw}px;height:${bh}px;background:${colores.edificio};"></div>`);
      }
    }
    html += elementos.join("");

    html += `<div style="position:absolute;bottom:20%;left:0;width:100%;height:4px;background:repeating-linear-gradient(to right,var(--ocre-oscuro) 0px,var(--ocre-oscuro) 30px,transparent 30px,transparent 50px);opacity:0.6;"></div>`;

    landscape.innerHTML = html;
  }

  function activarLluvia() {
    const rain = document.getElementById("rainOverlay");
    if (!rain) return;
    if (Math.random() > 0.5) return;
    for (let i = 0; i < 30; i++) {
      const drop = document.createElement("div");
      drop.className = "rain-drop";
      drop.style.left             = Math.random() * 100 + "%";
      drop.style.height           = (10 + Math.random() * 20) + "px";
      drop.style.opacity          = (0.1 + Math.random() * 0.3).toString();
      drop.style.animationDelay   = Math.random() * 0.8 + "s";
      drop.style.animationDuration= (0.4 + Math.random() * 0.6) + "s";
      rain.appendChild(drop);
    }
  }

  let transitionEl = document.createElement("div");
  transitionEl.className = "station-transition";
  transitionEl.innerHTML = `<div class="station-transition-text" id="transitionText">⬤ ⬤ ⬤</div>`;
  document.body.appendChild(transitionEl);

  function mostrarTransicion(texto, callback) {
    const textEl = document.getElementById("transitionText");
    if (textEl) textEl.textContent = texto;
    transitionEl.classList.add("active");
    setTimeout(() => {
      if (callback) callback();
      setTimeout(() => { transitionEl.classList.remove("active"); }, 600);
    }, 700);
  }

  function actualizarProgreso(index) {
    const fill  = document.getElementById("progressFill");
    const train = document.getElementById("progressTrain");
    const dots  = document.querySelectorAll(".pstop-dot");
    const pct   = paradas.length > 1 ? (index / (paradas.length - 1)) * 100 : 0;
    if (fill)  fill.style.width = pct + "%";
    if (train) train.style.left  = pct + "%";
    dots.forEach((dot, i) => {
      dot.classList.remove("current", "visited");
      if (i < index)  dot.classList.add("visited");
      if (i === index) dot.classList.add("current");
    });
  }

  function mostrarParada(index, conTransicion = true) {
    const parada = paradas[index];
    if (!parada) return;

    const mostrar = () => {
      const anuncio     = document.getElementById("arrivalAnnouncement");
      const anuncioText = document.getElementById("announcementText");
      if (anuncio && anuncioText) {
        anuncioText.textContent = `PRÓXIMA PARADA: ${parada.nombre.toUpperCase()}`;
        anuncio.classList.remove("hidden");
        setTimeout(() => anuncio.classList.add("hidden"), 3500);
      }
      actualizarProgreso(index);
      const ctrlName = document.getElementById("ctrlStopName");
      if (ctrlName) ctrlName.textContent = parada.nombre;
      const btnAnt = document.getElementById("btnAnterior");
      const btnSig = document.getElementById("btnSiguiente");
      if (btnAnt) btnAnt.disabled = index === 0;
      if (btnSig) {
        if (index === paradas.length - 1) {
          btnSig.textContent = "🏁 Fin del recorrido";
          btnSig.disabled = true;
        } else {
          btnSig.textContent = "Próxima estación ▶";
          btnSig.disabled = false;
        }
      }
      renderParada(parada, index);
    };

    if (conTransicion && index > 0) {
      mostrarTransicion(`🚂  Llegando a ${parada.nombre.toUpperCase()}  🚂`, mostrar);
    } else {
      mostrar();
    }
  }

  function renderParada(parada, index) {
    const container = document.getElementById("paradaContainer");
    if (!container) return;

    if (panoramaViewer) {
      try { panoramaViewer.destroy(); } catch(e) {}
      panoramaViewer = null;
    }

    const tipoLabels = {
      historia:  "Historia",
      tecnica:   "Técnica",
      industria: "Industria",
      interior:  "Interior",
      paisaje:   "Paisaje",
      destino:   "Destino"
    };

    const tipoLabel  = tipoLabels[parada.tipo] || "Parada";
    const datosHTML  = (parada.datos || []).map(d => `<div class="parada-dato">${d}</div>`).join("");
    const curiosidadHTML = parada.curiosidad ? `
      <div class="parada-curiosidad">
        <span class="curiosidad-icon">💡</span>
        <p>${parada.curiosidad}</p>
      </div>` : "";
    const panoId = "pano-" + index;

    container.innerHTML = `
      <div class="parada-slide">
        <div class="parada-header">
          <span class="parada-tipo-badge">${tipoLabel}</span>
          ${parada.año ? `<span class="parada-año">${parada.año}</span>` : ""}
        </div>
        <h2 class="parada-nombre">${parada.nombre}</h2>
        <p class="parada-desc">${parada.descripcion}</p>
        ${datosHTML ? `<div class="parada-datos">${datosHTML}</div>` : ""}
        ${curiosidadHTML}
        <div class="parada-360-section">
          <p class="parada-360-title">VISTA 360°</p>
          <div class="parada-panorama" id="${panoId}"></div>
          <a href="detalle.php?tren=${TREN_ID}&parada=${index}" class="parada-ver-detalle">
            ↗ ABRIR EN PANTALLA COMPLETA
          </a>
        </div>
      </div>`;

    if (typeof pannellum !== "undefined" && parada.panorama) {
      setTimeout(() => {
        try {
          panoramaViewer = pannellum.viewer(panoId, {
            type:         "equirectangular",
            panorama:     parada.panorama,
            autoLoad:     true,
            autoRotate:   -1.5,
            pitch:        parada.panorama_pitch || 0,
            hfov:         100,
            showControls: false
          });
        } catch(e) { console.warn("Pannellum error:", e); }
      }, 400);
    }

    const interior = document.querySelector(".train-interior");
    if (interior) interior.scrollTop = 0;
  }

  window.paradaSiguiente = function () {
    if (paradaActual < paradas.length - 1) {
      paradaActual++;
      mostrarParada(paradaActual, true);
      acelerarPaisaje();
    }
  };

  window.paradaAnterior = function () {
    if (paradaActual > 0) {
      paradaActual--;
      mostrarParada(paradaActual, true);
    }
  };

  function acelerarPaisaje() {
    const landscape = document.getElementById("landscape");
    if (!landscape) return;
    landscape.style.animationDuration = "5s";
    setTimeout(() => { landscape.style.animationDuration = "20s"; }, 1500);
  }

  window.mostrarCuriosidad = function () {
    if (typeof curiosidades === "undefined" || !curiosidades.length) return;
    const random = curiosidades[Math.floor(Math.random() * curiosidades.length)];
    const el = document.getElementById("datoCurioso");
    if (el) el.textContent = random;
  };

});
