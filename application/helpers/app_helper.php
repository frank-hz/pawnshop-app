<?php

function current_date_display()
{
  try {
    date_default_timezone_set('America/Lima');
    $days = array(
      "Monday" => "Lunes",
      "Tuesday" => "Martes",
      "Wednesday" => "Miércoles",
      "Thursday" => "Jueves",
      "Friday" => "Viernes",
      "Saturday" => "Sabado",
      "Sunday" => "Domingo",
    );
    $months = array(
      "January" => "Enero",
      "February" => "Febrero",
      "March" => "Marzo",
      "April" => "Abril",
      "May" => "Mayo",
      "June" => "Junio",
      "July" => "Julio",
      "August" => "Agosto",
      "September" => "Septiembre",
      "October" => "Octubre",
      "November" => "Noviembre",
      "December" => "Diciembre",
    );
    $hoy = getdate();
    $year = $hoy['year'];
    $dayNum = $hoy['mday'];
    $dayNom = $days[$hoy['weekday']];
    $month = $months[$hoy['month']];

    return "$dayNom, $dayNum de $month de $year";
  } catch (Exception $e) {
    return "";
  }
}

function get_input($keyname)
{
  if (isset($_POST[$keyname])) {
    return $_POST[$keyname];
  }

  $fileJSON = file_get_contents("php://input");
  $data = json_decode($fileJSON);
  if (is_array($data) && isset($data[$keyname])) {
    return $data[$keyname];
  }

  return null;
}



// Imprime errores codificado en json
function response_json($message, $config = [])
{
  echo json_encode([
    'status' => $config['status'] ?? 'ERR',
    'message' => $message,
    'errors' => $config['errors'] ?? []
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

// Obtiene y formatea valores POST
function catchPostValue($target, $isInt = false)
{
  $CI =& get_instance();
  $value = $CI->input->post($target, TRUE);
  if ($value === null)
    return null;

  $resp = $isInt ? (int) $value : mb_strtoupper($value);
  return $resp;
}

function response_api($message, $config = [])
{
  $CI =& get_instance();
  $CI->output
    ->set_status_header($config['code'] ?? 400)
    ->set_content_type('application/json')
    ->set_output(json_encode([
      'success' => $config['success'] ?? false,
      'success' => $config['status'] ?? 'ERR',
      'message' => $message,
      'errors' => $config['errors'] ?? []
    ]));
}

// Obtener y validar datos POST (JSON o form-data)
function get_post_data()
{
  // Obtener como JSON
  $CI =& get_instance();
  $input_raw = $CI->input->raw_input_stream;
  if (!empty($input_raw)) {
    $json_data = json_decode($input_raw, true);
    if (json_last_error() === JSON_ERROR_NONE && !empty($json_data)) {
      return $json_data;
    }
  }

  // Obtenr como POST norma, como opcion por defecto
  return $CI->input->post();
}

// Sanitizar datos 
function sanitize_input($value)
{
  $value = trim($value);
  $value = stripslashes($value);
  $value = htmlspecialchars($value);
  return $value;
}