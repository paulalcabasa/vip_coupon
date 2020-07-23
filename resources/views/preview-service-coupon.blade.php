<!DOCTYPE html>
<html>
<head>
    <title>Coupon</title>
    <head>
        <style>

            @page {
                margin: 0cm 0cm;

            }

                /** Define now the real margins of every page in the PDF **/
            body {
                font-family:'Calibri','Sans-serif','Arial';
                margin-top: 0.65cm;
                margin-left: 0.95cm;
                margin-right: 1.0cm;
                margin-bottom: 0.16cm;
            }

            .voucher-body {
                position: relative;
                text-align: center;
                color: black;
                width:680px;
                height:260px; /* 288px */
            }

            .wrapper {
                width: 100%;
                min-height: 370px;
                border: 5px dotted black;
                overflow: hidden;
            }

            .qr-code {
                position: absolute;
                top: 30px;
                right: 0px;
            }

            .amount {
                position: absolute;
                top: 85px;
                left:180px;
                font-weight:bold;
                font-size:3em;
                color:#fff;
            }

            .coupon-no {
                position: absolute;
                top: 10px;
                right: 410px;
                font-weight:bold;
                font-size:.6em;
                color:#fff;
            }
            
            .terms {
                position: absolute;
                top: 200px;
                left:140px;
                font-size:.35em;
                text-align:left;
                line-height:1.5;
                color:#fff;
            }

            .terms-title {
                position: absolute;
                top: 200px;
                left:173px;
                font-size:.5em;
                font-weight:bold;
                text-align:left;
                color:#fff;
            }

            .validity {
                position: absolute;
                top: 250px;
                right: 15px;
                font-size:.5em;
                color:#000;
            }
        
        </style>
    </head>
<body>

    <div class="voucher-body" style="z-index:2099;">
        <img src="{{ asset('public/images/service-coupon.jpg')}}" style="border:1px solid #000;width:100%;height:260px;"/>

        <div class="qr-code" >
            <img  src="data:image/png;base64, {!! base64_encode(QrCode::format('png')
                ->size(500)->errorCorrection('H')
                ->generate(123)) !!} "
                width="70" />
        </div>
        
        <div class="amount"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span>  50,000</div>
        <div class="terms-title">Terms and Condition:</div>
        <div class="terms">
            <span class="list"><?php echo $promo->terms; ?></span>
        </div>
        <div class="coupon-no">Control No. 00001</div>
        <div class="validity">Expires at : 07/31/2020</div>
    </div>
       
      
    
        

</body>
</html>