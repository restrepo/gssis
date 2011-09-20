
<?php

// retorna < 0 si $fecha2 es mayor que $fecha1
function compararFechas($fecha1, $fecha2)
{
    $aFecha1 = explode("/", $fecha1);
    $aFecha2 = explode("/", $fecha2);

    if(!checkdate($aFecha1[1], $aFecha1[0], $aFecha1[2]))
    {
        // la primera fecha no es valida
        return 0;
    }
    elseif(!checkdate($aFecha2[1], $aFecha2[0], $aFecha2[2]))
    {
        // la segunda fecha no es valida
        return 0;
    }
    else
    {
        $fecha1Jul = gregoriantojd($aFecha1[1], $aFecha1[0], $aFecha1[2]);
        $fecha2Jul = gregoriantojd($aFecha2[1], $aFecha2[0], $aFecha2[2]);

        return $fecha1Jul - $fecha2Jul;
    }
}

?>