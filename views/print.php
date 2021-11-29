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
    <link rel="shortcut icon" href="../assets/images/bill.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>  
    <input type="hidden" value="<?=$comunidad;?>" id="community">
    <button class="btn btn-print" type="button" onclick="printing();"> <img src="../assets/images/printer.png"> Print</button>
    <section class="pages">

    </section>

    <div class="loader">
        <div class="lds-dual-ring"></div>
    </div>

    <script src="../assets/scripts/jquery-3.6.0.min.js"></script>
    <script src="../assets/scripts/qrcode.min.js"></script>
</body>
</html>

<script>

// print page
function printing(){
    window.print();
}

// show data page
$(document).ready(function(){
    startPages();
});


function startPages(){
    let community = $("#community");

    if (community.val().length == 0) {
        $(".pages").html(`
    <pre>  
        {
            error: 404 Not Found,
            test: 1003
        }
    </pre>`);
        return false;   
    }

    let data = { community: community.val() };

    $(".loader").css("display", "flex");
    
    $.ajax({
        type: "POST",
        url: "../ajax/getPages.php",
        data: data,
        success: function(data){
            
            $(".loader").css("display", "none");

            console.log("Showing data.");

            if(data === 'empty'){
                console.log("No Records :"+data);
            }else{
                $(".pages").html(data);
                // dibujar el código QR
                // let data_qrcode = $("#data_qrcode").val();
                // new QRCode($(".img-qr"), data_qrcode);

                let codes = document.getElementsByClassName("codes").length;
                
                for(let i = 0; i < codes; i++){
                    let data_qrcode = $(".data_qrcode"+i).val();
                    new QRCode(document.getElementById("qcode"+i), data_qrcode);
                }
            }
        }
    });
}

</script>


