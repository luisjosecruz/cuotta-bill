<?php 

use Ayeo\Barcode;
//require_once ('config/pdo.php');

if(isset($fact) && !empty($fact)){
/*  
    $stmt = $pdo->query("SELECT mandamientos.mandBarras FROM mandamientos WHERE mandamientos.mandID = $fact");
    while ($row = $stmt->fetch()) {
        $code = $row['mandBarras'];
    }
    echo $code;
*/

    //  construir el código de barras
    require_once('vendor/autoload.php');
    $builder = new Barcode\Builder();
    $builder->setBarcodeType('gs1-128');
    $builder->setFilename('bar.png');
    $builder->setImageFormat('png');
    // $builder->setWidth(700);
    $builder->setHeight(60);
    //$builder->setFontPath('FreeSans.ttf');
    $builder->setFontSize(10);
    $builder->setBackgroundColor(255, 255, 255);
    $builder->setPaintColor(0,0,0);
    $builder->output(''.$fact.'');

}else{

    echo "404 Not Found <hr>";
    echo "Missing code <br><br>";
    echo "Example: <br>";
    echo "/41574197000082933902000000000096202106068020100212000001<br>";

}

?>