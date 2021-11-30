<?php 
    require_once "../config/pdo.php";

// validar que sean datos por medio del método POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // capturar las variables
    $community = intval($_POST['community']);
    $month = (isset($_POST['month'])) ? intval($_POST['month']) : 0;
    $background = (isset($_POST['background'])) ? intval($_POST['background']) : 1;
}else{
    return false;
}

$data_month = ($month == 0) ? "" : "AND MONTH(mand.mandFechaFin) = $month";

//echo "Community: ".$community. " Month: ".$month." Done!";

// -------------------------------------------------------------------------------------------------------
// EDIT THIS: your auth parameters
$username = 'lcruz';
$password = 'soporte247';

// EDIT THIS: the query parameters
//$url     = 'http://planetozh.com/blog/';    // URL to shrink
$keyword = 'xyz12';                         // optional keyword
$title   = '-----';                         // optional, if omitted YOURLS will lookup title with an HTTP request
$format  = 'json';                          // output format: 'json', 'xml' or 'simple'
// EDIT THIS: the URL of the API file
$api_url = 'https://ctta.link/yourls-api.php';
// -------------------------------------------------------------------------------------------------------


$html = '';

$bg = ($background == 1) ? "background:#f5f5f5 url('https://developer.cuotta.com/invoices/assets/images/bg2.jpg') no-repeat center;background-size:cover;" : "";
$bg = ($background == 1 && $month == 0) ? "background:#ffffff url('https://developer.cuotta.com/invoices/assets/images/bg_user.jpg') no-repeat center;background-size:cover;" : $bg;

$sql = "SELECT p.propID, p.propShortLnk, com.comID, com.comNombre, cont.contNombre, cont.contApellido, p.propDireccion1, mand.mandCorrelativo, mand.mandHash,
            DATE_FORMAT(mand.mandFechaInicio, '%d-%m-%Y') mandFechaInicio, mand.mandFechaInicio mandIni, mand.mandFechaFin mandFin, mand.mandFechaPago, DATE_FORMAT(mand.mandFechaFin, '%d-%m-%Y') mandDateFin, 
            DATE_FORMAT(mand.mandFechaPago, '%d-%m-%Y') mandDatePago, com.comCta1, mand.mandBarras, mand.mandNPE, mand.mandSaldoInicial, mand.mandSaldoFinal
        FROM comunidades com
        INNER JOIN propiedades p ON p.comID = com.comID
        /*INNER JOIN contactos cont ON cont.contID = p.contFact*/
        INNER JOIN vinculacion v ON v.vincPropID = p.propID
        INNER JOIN contactos cont ON v.vincContactoID = cont.contID
        INNER JOIN mandamientos mand ON mand.propID = p.propID
        WHERE com.comID = $community
        $data_month";

$query = $pdo->prepare($sql);
$query->execute();
$result = $query->fetchAll(PDO::FETCH_OBJ);

// '.$r->comCuenta1Nombre.'
if($query->rowCount() > 0){
    $counter = 0;
    foreach($result as $r){
        //-------------------------------------------------------------------------------------------------------
        $url     = 'https://developer.cuotta.com/pay/'.$r->mandHash;
        // Init the CURL session
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $api_url );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );            // No header in the result
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // Return, do not echo result
        curl_setopt( $ch, CURLOPT_POST, 1 );              // This is a POST request
        curl_setopt( $ch, CURLOPT_POSTFIELDS, array(      // Data to POST
                'url'      => $url,
                /*'keyword'  => $keyword,
                'title'    => $title,*/
                'format'   => $format,
                'action'   => 'shorturl',
                'username' => $username,
                'password' => $password
            ) );
        // Fetch and return content
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data);
        $link = $data->shorturl;
        //-------------------------------------------------------------------------------------------------------

        if($counter % 2 == 0){
            $html .= '<article class="page" id="print" style="'.$bg.'">';
        }
        //$html .= $link.' ||||';
        $html .= '
                <input type="hidden" value="'.$link.'" class="data_qrcode'.$counter.'">
                <div class="page__parts">
                    <h1 class="account"><div id="qcode'.$counter.'" class="codes"></div></h1>
                    <h2 class="correlative">'.$r->mandCorrelativo.'</h2>
                    <h3 class="community">'.$r->comNombre.'</h3>
                    <h4 class="fullname">'.$r->contNombre.' '.$r->contApellido.'</h4>
                    <h4 class="direction">'.$r->propDireccion1.'</h4>

                    <h5 class="cutdate">'.$r->mandDateFin.'</h5>
                    <h5 class="lastdate">'.$r->mandDatePago.'</h5>

                    <div class="table-transactions">
                        <table>
                            <tr>
                                <td>'.$r->mandFechaInicio.'</td>
                                <td>SALDO ANTERIOR</td>
                                <td>$ '.$r->mandSaldoInicial.'</td>
                            </tr>';
                                // obtener las transacciones para la propiedad y el mes.
                                /*$sql_2 = "SELECT transDesc, transMonto, DATE_FORMAT(transacciones.transFecha, '%d-%m-%Y') FechaOperacion
                                            FROM transacciones 
                                            WHERE transacciones.propID = '".$r->propID."'
                                            AND MONTH(transacciones.transFecha) = ".$month."
                                            AND ".$month." ";*/
                                $sql_2 = "SELECT transDesc, transMonto, DATE_FORMAT(transacciones.transFecha, '%d-%m-%Y') FechaOperacion 
                                            FROM transacciones 
                                            WHERE transacciones.propID = '".$r->propID."'
                                            AND transacciones.transFecha BETWEEN '".$r->mandIni."' AND '".$r->mandFin."'";
                                
                                $query_2 = $pdo->prepare($sql_2);
                                $query_2->execute();
                                $result_2 = $query_2->fetchAll(PDO::FETCH_OBJ);
                                if($query_2->rowCount() > 0){
                                    foreach($result_2 as $r_2){
                                        $html .= '<tr>';
                                            $html .= '<td width="20%">'.$r_2->FechaOperacion.'</td>';
                                            $html .= '<td width="55%">'.$r_2->transDesc.'</td>';
                                            $html .= '<td width="25%">$ '.$r_2->transMonto.'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                        $html.='
                        </table>
                    </div>
                    <div class="table-total">
                        <table>
                            <tr>
                                <td><span class="bold-total invisible">---- -- -- ---- --</span></td>
                                <td><strong class="bold-total invisible">TOTAL A CANCELAR</strong></td>
                                <td><strong class="bold-total">$ '.$r->mandSaldoFinal.'</strong></td>
                            </tr>
                        </table>
                    </div>

                    <h6 class="message">';
                        // obtener el mensaje para la comunidad y que este en las fechas validas.
                        $sql_3 = "SELECT mensTexto FROM mensajes 
                                    WHERE mensajes.comID = '".$r->comID."' 
                                    AND ('".$r->mandFin."' BETWEEN mensajes.mensInicio AND mensajes.mensFinal)";
                        $query_3 = $pdo->prepare($sql_3);
                        $query_3->execute();
                        $result_3 = $query_3->fetchAll(PDO::FETCH_OBJ);
                        if($query_3->rowCount() > 0){
                            foreach($result_3 as $r_3){
                                $html .= $r_3->mensTexto;
                            }
                        }else{
                            $html .= '<p style="color: transparent;">Nothing</p>';
                            $html .= '<p style="color: transparent;">Nothing</p>';
                        }
            $html.='</h6>

                    <h6 class="npe"><span>NPE</span> '.$r->mandNPE.'</h6>
                    <h6 class="barcode"><img src="/cuotta-bill/barcode/'.$r->mandBarras.'"></h6>
                    <!-- <h6 class="barcode_number">'.$r->mandBarras.'</h6> -->
                    <p></p>
                </div>
        ';
        if($counter % 2 != 0){
            $html .= '</article>';
        }
        $counter++;
    }
}else{
    $html = "empty";
}

echo $html;
 
?>