<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Invoice</title>
    <link rel="stylesheet" href="https://developer.cuotta.com/invoices/assets/style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
</head>
<body>  
    <input type="hidden" value="<?=$comunidad;?>" id="community">
    <button class="btn btn-print" type="button" onclick="printing();">Print</button>
    <section class="pages">

    </section>

    <script src="https://developer.cuotta.com/invoices/assets/scripts/jquery-3.6.0.min.js"></script>
    <script src="https://developer.cuotta.com/invoices/assets/scripts/qrcode.min.js"></script>
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
    let data = { community: community.val() };
    
    $.ajax({
        type: "POST",
        url: "http://localhost/cuotta-bill/ajax/getPages.php",
        data: data,
        success: function(data){
            console.log("Showing data.");

            if(data === 'empty'){
                console.log("No Records :"+data);
            }else{
                // mostrar la data
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


