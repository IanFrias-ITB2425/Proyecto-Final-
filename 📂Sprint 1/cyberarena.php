<?php
// ==========================================
// CYBERARENA - GLOBAL COMMAND CENTER
// ==========================================
date_default_timezone_set('Europe/Madrid');

// 1. Verificación de Base de Datos
$servername = "localhost";
$username   = "arena_sys";
$password   = "CyberArena_DB_2026!";
$dbname     = "arena_db";

$conn     = @new mysqli($servername, $username, $password, $dbname);
$db_alive = !$conn->connect_error;

// 2. Carga del sistema
$load         = sys_getloadavg();
$is_overloaded = $load[0] > 1.5;

// 3. Estado de Wazuh
$wazuh_status = shell_exec("pgrep wazuh-agent") ? "ACTIVE" : "OFFLINE";

// Datos derivados
$db_status_label  = $db_alive      ? "ONLINE"      : "CRITICAL";
$web_status_label = $is_overloaded ? "OVERLOAD"    : "NOMINAL";
$wazuh_label      = $wazuh_status === "ACTIVE" ? "ACTIVE" : "OFFLINE";

$db_class    = $db_alive      ? "ok"   : "err";
$web_class   = $is_overloaded ? "err"  : "ok";
$wazuh_class = $wazuh_status === "ACTIVE" ? "ok" : "err";

$ts = date("Y-m-d H:i:s");
$hostname = gethostname();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CyberArena | Command Center</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Barlow:wght@300;400;600&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --bg:        #070a0f;
  --bg2:       #0c1118;
  --bg3:       #111820;
  --border:    #1e2d3d;
  --border2:   #243447;
  --text:      #8da8c4;
  --text2:     #c4d8ed;
  --text3:     #ddeeff;
  --dim:       #3a5470;
  --ok:        #00e5a0;
  --ok-dim:    #004d36;
  --err:       #ff4757;
  --err-dim:   #4d0f16;
  --warn:      #ffa502;
  --accent:    #00a8ff;
  --accent-dim:#003a56;
  --font-mono: 'Share Tech Mono', monospace;
  --font-ui:   'Barlow', sans-serif;
  --font-cond: 'Barlow Condensed', sans-serif;
}

html, body {
  background: var(--bg);
  color: var(--text);
  font-family: var(--font-ui);
  min-height: 100vh;
  overflow-x: hidden;
}

/* ---- GRID SCAN BACKGROUND ---- */
body::before {
  content: '';
  position: fixed;
  inset: 0;
  background-image:
    linear-gradient(rgba(0,168,255,0.025) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0,168,255,0.025) 1px, transparent 1px);
  background-size: 40px 40px;
  pointer-events: none;
  z-index: 0;
}

/* ---- SCANLINE overlay ---- */
body::after {
  content: '';
  position: fixed;
  inset: 0;
  background: repeating-linear-gradient(
    0deg,
    transparent,
    transparent 2px,
    rgba(0,0,0,0.12) 2px,
    rgba(0,0,0,0.12) 4px
  );
  pointer-events: none;
  z-index: 1;
}

.wrapper {
  position: relative;
  z-index: 2;
  max-width: 1100px;
  margin: 0 auto;
  padding: 48px 24px 64px;
}

/* ---- HEADER ---- */
.header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  border-bottom: 1px solid var(--border);
  padding-bottom: 28px;
  margin-bottom: 48px;
  gap: 24px;
  flex-wrap: wrap;
}

.brand {
  display: flex;
  align-items: center;
  gap: 20px;
}

.logo-mark {
  width: 52px;
  height: 52px;
  border: 2px solid var(--accent);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  flex-shrink: 0;
}

.logo-mark::before {
  content: '';
  position: absolute;
  inset: 5px;
  border: 1px solid var(--accent);
  opacity: 0.4;
}

.logo-mark svg {
  width: 26px;
  height: 26px;
  fill: var(--accent);
}

.brand-text .name {
  font-family: var(--font-cond);
  font-size: 28px;
  font-weight: 700;
  letter-spacing: 4px;
  color: var(--text3);
  line-height: 1;
  text-transform: uppercase;
}

.brand-text .sub {
  font-family: var(--font-mono);
  font-size: 11px;
  color: var(--dim);
  letter-spacing: 2px;
  margin-top: 6px;
}

.header-right {
  text-align: right;
  font-family: var(--font-mono);
  font-size: 11px;
  color: var(--dim);
  line-height: 1.9;
}

.header-right .ts {
  color: var(--accent);
  font-size: 12px;
}

/* ---- SECTION LABEL ---- */
.section-label {
  font-family: var(--font-mono);
  font-size: 10px;
  letter-spacing: 3px;
  color: var(--dim);
  text-transform: uppercase;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.section-label::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--border);
}

/* ---- STATUS CARDS ---- */
.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2px;
  margin-bottom: 2px;
}

.card {
  background: var(--bg2);
  border: 1px solid var(--border);
  padding: 28px 28px 24px;
  position: relative;
  overflow: hidden;
  transition: border-color .25s, background .25s;
}

.card:hover {
  border-color: var(--border2);
  background: var(--bg3);
}

/* Corner accent */
.card::before {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 40px; height: 40px;
  border-top: 2px solid var(--accent);
  border-left: 2px solid var(--accent);
  opacity: 0;
  transition: opacity .3s;
}

.card:hover::before { opacity: 1; }

.card-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 20px;
}

.card-icon {
  width: 36px;
  height: 36px;
  background: var(--bg3);
  border: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: center;
}

.card-icon svg {
  width: 18px;
  height: 18px;
  fill: var(--dim);
}

.card-id {
  font-family: var(--font-mono);
  font-size: 10px;
  color: var(--dim);
  letter-spacing: 1px;
}

.card-title {
  font-family: var(--font-cond);
  font-size: 20px;
  font-weight: 500;
  letter-spacing: 1px;
  color: var(--text2);
  margin-bottom: 4px;
  text-transform: uppercase;
}

.card-desc {
  font-size: 13px;
  color: var(--dim);
  font-family: var(--font-mono);
  margin-bottom: 22px;
}

/* Status row */
.status-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.pulse {
  width: 10px; height: 10px;
  border-radius: 50%;
  flex-shrink: 0;
  position: relative;
}

.pulse::after {
  content: '';
  position: absolute;
  inset: -4px;
  border-radius: 50%;
  animation: ring 2.2s ease-out infinite;
}

.pulse.ok        { background: var(--ok); }
.pulse.ok::after { border: 1px solid var(--ok); }
.pulse.err        { background: var(--err); animation: none; }
.pulse.err::after { border: 1px solid var(--err); animation: none; }

@keyframes ring {
  0%   { transform: scale(.8); opacity: .8; }
  70%  { transform: scale(2.2); opacity: 0; }
  100% { transform: scale(.8); opacity: 0; }
}

.status-label {
  font-family: var(--font-cond);
  font-size: 22px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
}

.status-label.ok  { color: var(--ok); }
.status-label.err { color: var(--err); }

/* Background glow fill */
.card.ok-card::after  { content:''; position:absolute; inset:0; background: radial-gradient(ellipse at 0% 100%, rgba(0,229,160,.04) 0%, transparent 60%); pointer-events:none; }
.card.err-card::after { content:''; position:absolute; inset:0; background: radial-gradient(ellipse at 0% 100%, rgba(255,71,87,.05) 0%, transparent 60%); pointer-events:none; }

/* ---- BOTTOM PANELS ---- */
.bottom-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2px;
  margin-top: 2px;
}

@media (max-width: 640px) {
  .bottom-grid { grid-template-columns: 1fr; }
  .header { flex-direction: column; }
  .header-right { text-align: left; }
}

.panel {
  background: var(--bg2);
  border: 1px solid var(--border);
  padding: 22px 24px;
}

.panel-title {
  font-family: var(--font-mono);
  font-size: 10px;
  letter-spacing: 3px;
  color: var(--dim);
  text-transform: uppercase;
  margin-bottom: 16px;
  padding-bottom: 10px;
  border-bottom: 1px solid var(--border);
}

/* WAF panel */
.waf-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid var(--border);
  font-size: 13px;
}

.waf-item:last-child { border-bottom: none; }

.waf-key {
  font-family: var(--font-mono);
  color: var(--dim);
  font-size: 12px;
}

.waf-val {
  font-family: var(--font-mono);
  font-size: 12px;
  color: var(--text2);
}

.badge {
  font-family: var(--font-mono);
  font-size: 11px;
  padding: 2px 8px;
  letter-spacing: 1px;
}

.badge.ok  { background: var(--ok-dim);  color: var(--ok);  }
.badge.warn{ background: #3d2a00;        color: var(--warn);}
.badge.info{ background: var(--accent-dim); color: var(--accent); }

/* Sysload panel */
.load-row {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.load-item {
  display: flex;
  align-items: center;
  gap: 12px;
  font-family: var(--font-mono);
  font-size: 12px;
}

.load-key { color: var(--dim); width: 40px; flex-shrink: 0; }

.load-bar-track {
  flex: 1;
  height: 4px;
  background: var(--border);
  position: relative;
  overflow: hidden;
}

.load-bar-fill {
  height: 100%;
  max-width: 100%;
  transition: width 1s ease;
}

.load-bar-fill.ok  { background: var(--ok); }
.load-bar-fill.warn{ background: var(--warn); }
.load-bar-fill.err { background: var(--err); }

.load-num {
  color: var(--text2);
  width: 32px;
  text-align: right;
}

/* ---- FOOTER ---- */
.footer {
  margin-top: 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  padding-top: 20px;
  border-top: 1px solid var(--border);
}

.footer-mono {
  font-family: var(--font-mono);
  font-size: 11px;
  color: var(--dim);
  line-height: 1.8;
}

.footer-mono span { color: var(--text); }

/* ---- TICKER ---- */
.ticker-wrap {
  overflow: hidden;
  background: var(--bg3);
  border: 1px solid var(--border);
  padding: 8px 0;
  margin-bottom: 2px;
  position: relative;
}

.ticker-inner {
  display: flex;
  gap: 60px;
  white-space: nowrap;
  animation: scroll-x 30s linear infinite;
  font-family: var(--font-mono);
  font-size: 11px;
  color: var(--dim);
  letter-spacing: 1px;
}

.ticker-inner .sep { color: var(--border2); }
.ticker-inner .hi  { color: var(--ok); }
.ticker-inner .hi2 { color: var(--accent); }

@keyframes scroll-x {
  0%   { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

/* ---- ANIMATIONS ---- */
.fade-up {
  opacity: 0;
  transform: translateY(12px);
  animation: fadeup .5s ease forwards;
}

@keyframes fadeup {
  to { opacity: 1; transform: none; }
}

.card:nth-child(1) { animation-delay: .05s; }
.card:nth-child(2) { animation-delay: .15s; }
.card:nth-child(3) { animation-delay: .25s; }
</style>
</head>
<body>

<!-- TICKER -->
<div class="ticker-wrap" aria-hidden="true">
  <div class="ticker-inner">
    <span>SYS_LOAD <span class="hi"><?php echo $load[0]; ?></span></span>
    <span class="sep">//</span>
    <span>NODE <span class="hi2"><?php echo htmlspecialchars($hostname); ?></span></span>
    <span class="sep">//</span>
    <span>WAF <span class="hi">MODSEC_V3</span> RUNNING</span>
    <span class="sep">//</span>
    <span>HTTPS INSPECTION <span class="hi">ACTIVE</span></span>
    <span class="sep">//</span>
    <span>DB ENGINE <span class="hi2">MYSQL/MARIADB</span></span>
    <span class="sep">//</span>
    <span>SIEM AGENT <span class="hi"><?php echo $wazuh_label; ?></span></span>
    <span class="sep">//</span>
    <span>TS <span class="hi2"><?php echo $ts; ?></span></span>
    <span class="sep">//</span>
    <!-- repeat for seamless loop -->
    <span>SYS_LOAD <span class="hi"><?php echo $load[0]; ?></span></span>
    <span class="sep">//</span>
    <span>NODE <span class="hi2"><?php echo htmlspecialchars($hostname); ?></span></span>
    <span class="sep">//</span>
    <span>WAF <span class="hi">MODSEC_V3</span> RUNNING</span>
    <span class="sep">//</span>
    <span>HTTPS INSPECTION <span class="hi">ACTIVE</span></span>
    <span class="sep">//</span>
    <span>DB ENGINE <span class="hi2">MYSQL/MARIADB</span></span>
    <span class="sep">//</span>
    <span>SIEM AGENT <span class="hi"><?php echo $wazuh_label; ?></span></span>
    <span class="sep">//</span>
    <span>TS <span class="hi2"><?php echo $ts; ?></span></span>
  </div>
</div>

<div class="wrapper">

  <!-- HEADER -->
  <header class="header">
    <div class="brand">
      <div class="logo-mark">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4l6 2.67V11c0 3.9-2.63 7.58-6 8.93C8.63 18.58 6 14.9 6 11V7.67L12 5z"/>
        </svg>
      </div>
      <div class="brand-text">
        <div class="name">CyberArena</div>
        <div class="sub">// GLOBAL COMMAND CENTER // SEC-OPS DASHBOARD</div>
      </div>
    </div>
    <div class="header-right">
      <div class="ts"><?php echo $ts; ?></div>
      <div>NODE &mdash; <?php echo htmlspecialchars($hostname); ?></div>
      <div>UPTIME SESSION ACTIVE</div>
    </div>
  </header>

  <!-- STATUS CARDS -->
  <div class="section-label">SERVICE STATUS</div>
  <div class="cards">

    <!-- Web Server -->
    <div class="card fade-up <?php echo $web_class; ?>-card">
      <div class="card-header">
        <div class="card-icon">
          <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
        </div>
        <div class="card-id">SVC-001</div>
      </div>
      <div class="card-title">Web Server</div>
      <div class="card-desc">nginx + modsecurity</div>
      <div class="status-row">
        <div class="pulse <?php echo $web_class; ?>"></div>
        <div class="status-label <?php echo $web_class; ?>"><?php echo $web_status_label; ?></div>
      </div>
    </div>

    <!-- Database -->
    <div class="card fade-up <?php echo $db_class; ?>-card">
      <div class="card-header">
        <div class="card-icon">
          <svg viewBox="0 0 24 24"><path d="M12 3C7.58 3 4 4.79 4 7v10c0 2.21 3.58 4 8 4s8-1.79 8-4V7c0-2.21-3.58-4-8-4zm6 14c0 .55-2.24 2-6 2s-6-1.45-6-2v-2.23c1.61.78 3.72 1.23 6 1.23s4.39-.45 6-1.23V17zm0-5c0 .55-2.24 2-6 2s-6-1.45-6-2v-2.23C7.61 10.78 9.72 11.23 12 11.23s4.39-.45 6-1.23V12zm-6-3C8.24 9 6 7.55 6 7s2.24-2 6-2 6 1.45 6 2-2.24 2-6 2z"/></svg>
        </div>
        <div class="card-id">SVC-002</div>
      </div>
      <div class="card-title">Database</div>
      <div class="card-desc">mysql / mariadb</div>
      <div class="status-row">
        <div class="pulse <?php echo $db_class; ?>"></div>
        <div class="status-label <?php echo $db_class; ?>"><?php echo $db_status_label; ?></div>
      </div>
    </div>

    <!-- SIEM -->
    <div class="card fade-up <?php echo $wazuh_class; ?>-card">
      <div class="card-header">
        <div class="card-icon">
          <svg viewBox="0 0 24 24"><path d="M15 1H9v2h6V1zm-4 13h2V8h-2v6zm8.03-6.61l1.42-1.42c-.43-.51-.9-.99-1.41-1.41l-1.42 1.42C16.07 4.74 14.12 4 12 4c-4.97 0-9 4.03-9 9s4.02 9 9 9 9-4.03 9-9c0-2.12-.74-4.07-1.97-5.61zM12 20c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>
        </div>
        <div class="card-id">SVC-003</div>
      </div>
      <div class="card-title">SIEM Telemetry</div>
      <div class="card-desc">wazuh agent</div>
      <div class="status-row">
        <div class="pulse <?php echo $wazuh_class; ?>"></div>
        <div class="status-label <?php echo $wazuh_class; ?>"><?php echo $wazuh_label; ?></div>
      </div>
    </div>

  </div>

  <!-- BOTTOM PANELS -->
  <div class="bottom-grid">

    <!-- WAF Panel -->
    <div class="panel">
      <div class="panel-title">// WAF — ModSecurity v3</div>
      <div class="waf-item">
        <span class="waf-key">ENGINE</span>
        <span class="badge ok">ACTIVE</span>
      </div>
      <div class="waf-item">
        <span class="waf-key">RULESET</span>
        <span class="waf-val">OWASP CRS 4.x</span>
      </div>
      <div class="waf-item">
        <span class="waf-key">MODE</span>
        <span class="badge warn">DETECTION+BLOCK</span>
      </div>
      <div class="waf-item">
        <span class="waf-key">PROTOCOL</span>
        <span class="waf-val">HTTPS INSPECTION</span>
      </div>
      <div class="waf-item">
        <span class="waf-key">TLS</span>
        <span class="badge info">TLS 1.3</span>
      </div>
    </div>

    <!-- System Load Panel -->
    <div class="panel">
      <div class="panel-title">// SYSTEM LOAD — <?php echo htmlspecialchars($hostname); ?></div>
      <div class="load-row">
        <?php
          $intervals = ['1m' => $load[0], '5m' => $load[1], '15m' => $load[2]];
          foreach ($intervals as $label => $val) {
            $pct = min(100, round(($val / 3) * 100));
            $cls = $val > 1.5 ? 'err' : ($val > 1.0 ? 'warn' : 'ok');
            echo "<div class='load-item'>";
            echo "<span class='load-key'>$label</span>";
            echo "<div class='load-bar-track'><div class='load-bar-fill $cls' style='width:{$pct}%'></div></div>";
            echo "<span class='load-num'>$val</span>";
            echo "</div>";
          }
        ?>
      </div>
    </div>

  </div>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="footer-mono">
      <div>SYS_LOAD <span><?php echo $load[0]; ?></span> &nbsp;&nbsp; NODE <span><?php echo htmlspecialchars($hostname); ?></span></div>
      <div>CYBERARENA COMMAND CENTER &mdash; RESTRICTED ACCESS</div>
    </div>
    <div class="footer-mono" style="text-align:right;">
      <div>VERSION <span>2026.1</span></div>
      <div><?php echo $ts; ?></div>
    </div>
  </footer>

</div>

</body>
</html>
