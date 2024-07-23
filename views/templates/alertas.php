
<?php foreach($alertas as $key => $alerta): //array alertas $key=error y $alerta= 0,1,2,3,4
    foreach($alerta as $mensaje): //alerta= 0,1,2,3 $mensaje=el mensaje del error?>
        <div class=" alerta <?php echo $key; ?>"><?php echo $mensaje; ?></div>
    <?php endforeach; ?>
<?php endforeach; ?>

<!-- array(1) {
  ["error"]=> $key
  array(4) { $alerta
    [0]=>
    string(24) "El Nombre es Obligatorio"//$mensaje
    [1]=>
    string(36) "El email no tiene un formato válido"
    [2]=>
    string(29) "El Password no puede ir vacio"
    [3]=>
    string(47) "El Password debe contener al menos 6 caracteres"
  }
} -->