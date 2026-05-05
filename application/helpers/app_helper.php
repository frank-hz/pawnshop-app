<?php 
    
    function current_date_display(){
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

	function get_input($keyname){
		if (isset($_POST[$keyname])){
			return $_POST[$keyname];
		}

		$fileJSON = file_get_contents("php://input"); 
		$data = json_decode($fileJSON);
		if (is_array($data) && isset($data[$keyname])){
			return $data[$keyname];
		}

		return null;
	}

?>