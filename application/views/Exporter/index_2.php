<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Exportar datos</title>
</head>
<body>
<h1>Exportar datos</h1>

<form id="frm">
  <fieldset>
    <legend>Tablas</legend>
    <?php foreach ($tablas as $t): ?>
      <label><input type="checkbox" name="tablas[]" value="<?= html_escape($t) ?>"> <?= html_escape($t) ?></label>
    <?php endforeach; ?>
  </fieldset>

  <p>
    <label>Formato
      <select name="formato">
        <option value="xlsx">Excel (.xlsx)</option>
        <option value="csv">CSV</option>
      </select>
    </label>
  </p>

  <p>
    <label>Desde <input type="date" name="fecha_desde"></label>
    <label>Hasta <input type="date" name="fecha_hasta"></label>
  </p>

  <p>
    <label>Estado
      <select name="estado">
        <option value="">(todos)</option>
        <option value="pendiente">pendiente</option>
        <option value="pagado">pagado</option>
        <option value="anulado">anulado</option>
      </select>
    </label>
    <label>Monto mín. <input type="number" step="0.01" name="monto_min"></label>
    <label>Monto máx. <input type="number" step="0.01" name="monto_max"></label>
  </p>

  <p><label><input type="checkbox" name="sin_duplicados" value="1"> Evitar filas duplicadas</label></p>

  <button type="submit" id="btn">Exportar</button>
  <p id="mensaje"></p>
</form>

<!-- Recomendado: descargar esta librería y servirla desde tu propio servidor -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
<script>
const URL_DATOS = '<?= site_url("exportacion/datos") ?>';
const frm = document.getElementById('frm');
const btn = document.getElementById('btn');
const mensaje = document.getElementById('mensaje');

frm.addEventListener('submit', async (e) => {
  e.preventDefault();
  btn.disabled = true;
  mensaje.textContent = 'Obteniendo datos…';

  try {
    const datos = await pedirDatos(new FormData(frm));
    await generarArchivos(datos.tablas, frm.formato.value);
    mensaje.textContent = 'Exportación lista.';
  } catch (error) {
    mensaje.textContent = 'Error: ' + error.message;
  } finally {
    btn.disabled = false;
  }
});

/* 1. Enviar filtros y recibir los datos (GET: no necesita token CSRF) */
async function pedirDatos(formData) {
  const params = new URLSearchParams();
  for (const [clave, valor] of formData) {
    if (valor !== '' && clave !== 'formato') params.append(clave, valor);
  }

  const respuesta = await fetch(`${URL_DATOS}?${params}`, { credentials: 'same-origin' });
  const datos = await respuesta.json();
  if (!respuesta.ok) throw new Error(datos.error || 'No se pudieron obtener los datos');
  return datos;
}

/* 2. Un archivo por tabla */
async function generarArchivos(tablas, formato) {
  const fecha = new Date().toISOString().slice(0, 10);

  for (const [nombre, { columnas, filas }] of Object.entries(tablas)) {
    const archivo = `${nombre}_${fecha}`;
    if (formato === 'xlsx') descargarXLSX(archivo, nombre, columnas, filas);
    else descargarCSV(archivo, columnas, filas);

    await new Promise(r => setTimeout(r, 300));   // evita que el navegador bloquee descargas seguidas
  }
}

function descargarXLSX(archivo, nombreHoja, columnas, filas) {
  const hoja  = XLSX.utils.aoa_to_sheet([columnas, ...filas]);
  const libro = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(libro, hoja, nombreHoja.slice(0, 31));   // Excel limita el nombre a 31 caracteres
  XLSX.writeFile(libro, archivo + '.xlsx');
}

function descargarCSV(archivo, columnas, filas) {
  const hoja = XLSX.utils.aoa_to_sheet([columnas, ...filas.map(f => f.map(protegerFormula))]);
  const csv  = XLSX.utils.sheet_to_csv(hoja, { FS: ';' });   // ';' es lo que Excel en español espera

  // '\uFEFF' (BOM) hace que Excel muestre bien tildes y ñ
  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const enlace = document.createElement('a');
  enlace.href = URL.createObjectURL(blob);
  enlace.download = archivo + '.csv';
  enlace.click();
  URL.revokeObjectURL(enlace.href);
}

/* Evita que Excel interprete un texto como fórmula al abrir el CSV */
function protegerFormula(valor) {
  const esTexto = typeof valor === 'string' && isNaN(Number(valor));
  return esTexto && /^[=+\-@\t\r]/.test(valor) ? "'" + valor : valor;
}
</script>
</body>
</html>