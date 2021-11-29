<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> 
    <title>Printing Bills</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Quicksand:wght@700&display=swap">
    <link rel="shortcut icon" href="<?=$URLSERVER?>assets/images/bill.png" type="image/x-icon">
    <link rel="stylesheet" href="<?=$URLSERVER?>assets/css/style.css">
</head>
<body>
    <section class="options" id="options">
        <div class="options__content">
            <label for="community" class="label-inputs">
                <span>Comunidad</span>
                <select name="community" id="community" class="input-select">
                    <option value="0" disabled selected>Seleccione</option>
                    <?php 
                        // obtener los códigos de las comunidades para llenar el select
                        $sql = "SELECT * FROM comunidades";
                        $query = $pdo->prepare($sql);
                        $query->execute();
                        $result = $query->fetchAll(PDO::FETCH_OBJ);

                        if($query->rowCount() > 0){
                            foreach($result as $r){
                                echo "<option value=".$r->comID.">".$r->comNombre."</option>: ";
                            }
                        }
                    ?>
                </select>
            </label>
            <label for="month" class="label-inputs">
                <span>Mes</span>
                <select name="month" id="month" class="input-select">
                    <option value="0" disabled selected>Seleccione</option>
                    <option value="01">Enero</option>
                    <option value="02">Febrero</option>
                    <option value="03">Marzo</option>
                    <option value="04">Abril</option>
                    <option value="05">Mayo</option>
                    <option value="06">Junio</option>
                    <option value="07">Julio</option>
                    <option value="08">Agosto</option>
                    <option value="09">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
            </label>
            <label for="radio_bg_without" class="label-inputs">
                <span>Sin Fondo</span>
                <input type="radio" class="radio_bg" name="radio_bg" id="radio_bg_without" value="0" checked>
            </label>
            <label for="radio_bg_with" class="label-inputs">
                <span>Con Fondo</span>
                <input type="radio" class="radio_bg" name="radio_bg" id="radio_bg_with" value="1">
            </label>
            <button class="btn start-pages" id="startPages">Ver facturas</button>
            <button class="btn btn-print" type="button" onclick="printing();">Print</button>
        </div>
    </section>
    <button class="btn btn-print" type="button" onclick="printing();"> <img src="assets/images/printer.png"> Print</button>
    <section class="pages">
        <!-- <article class="page" id="print">
            <div class="page__parts"></div>
            <div class="page__parts"></div>
        </article> -->
    </section>

    <div class="loader">
        <div class="lds-dual-ring"></div>
    </div>

    <script src="<?=$URLSERVER?>assets/scripts/jquery-3.6.0.min.js"></script>
    <script src="<?=$URLSERVER?>assets/scripts/qrcode.min.js"></script>
</body>
</html>

<script>

// print page
function printing(){
    window.print();
}

// show data page
$("#startPages").click(() => startPages());

function startPages(){
    let community = $("#community");
    let month = $("#month");
    let bg = ($("#radio_bg_without").is(':checked')) ? $("#radio_bg_without").val() : $("#radio_bg_with").val();

    if(community.val() == null || month.val() == null){
        console.log("No se han seleccionado todos los datos.");
        return false;
    }
    
    $(".loader").css("display", "flex");
    
    let data = { community: community.val(), month: month.val(), background: bg };
    
    $.ajax({
        type: "POST",
        url: "<?=$URLSERVER?>ajax/getPages.php",
        data: data,
        beforeSend: function(){
            $(".pages").html("");
        },
        success: function(data){

            $(".loader").css("display", "none");

            console.log("Showing data.");

            if(data === 'empty'){
                console.log("No Records :"+data);
            }else{
                // mostrar la data
                $(".pages").html(data);
                // dibujar el código QR
                // let data_qrcode = $(".data_qrcode").val();
                // new QRCode(document.getElementByClassName("img-qr"), data_qrcode);
                
                var codes = document.getElementsByClassName("codes").length;

                for(let i = 0; i < codes; i++){
                    let data_qrcode = $(".data_qrcode"+i).val();
                    new QRCode(document.getElementById("qcode"+i), data_qrcode);
                    
                    // Hacer la petición al api de YOURLS
                    /*var api_url  = 'http://ctta.link/yourls-api.php';
                    var response = $.get( api_url, {
                            username: "lcruz",
                            password: "soporte247",
                            action:   "shorturl",
                            format:   "json",
                            url:      "http://clarkode.com"
                        },
                        // callback function that will deal with the server response
                        function( data) {
                            // now do something with the data, for instance show new short URL:
                            console.log(data.shorturl);
                        }
                    );*/
                }
            }
        }
    });
}

</script>


