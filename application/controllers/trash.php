<?php




// Credito_api
  function get_stash_string($data){
    $string = "";
    $parameters = [];
    $targetAccepted = ["ESTADO","FECHA","FECHA_RANGO"];
    $statesAccepted = ["E","A","CC","R"];
    $conditionalsAccepted = [
      "CONJUNTA" => '&&',
      "OPCIONAL" => '||'
    ];

    $StateFilterStash = [
      'CONJUNTA' => [],
      'OPCIONAL' => []
    ];
    // $opcional_stash = [];
    foreach ($data as $item) {
      if (!in_array($item->target,$targetAccepted)) continue;
      $condition = (array_key_exists($item->condition,$conditionalsAccepted))
        ? $item->condition : 'CONJUNTA';

      if ($item->target === 'ESTADO'){
        array_push($StateFilterStash[$condition],"cred_estado=?");
        array_push($parameters, $item->value);
      }

    }
    $conjunta_stash = implode(" && ",$StateFilterStash['CONJUNTA']);
    $opcional_stash = implode(" || ",$StateFilterStash['OPCIONAL']);
    $total_string = (!empty($opcional_stash))
      ? $conjunta_stash ||

    return [
      'query' => $string,
      'parameters' => $parameters,
      "log" => ($states_opt)
    ];
  }

?>
