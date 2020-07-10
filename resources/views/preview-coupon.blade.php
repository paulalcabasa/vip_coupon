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
                width:500px;
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
                top: 1px;
                right: 0px;
            }

            .amount {
                position: absolute;
                top: 90px;
                right: 30px;
                font-weight:bold;
                font-size:3.9em;
                color:#fff;
            }

            .coupon-no {
                position: absolute;
                top: 238px;
                right: 20px;
                font-weight:bold;
                font-size:1em;
                color:red;
            }

            .cs-no {
                position: absolute;
                top: 245px;
                left: 10px;
                font-weight:bold;
                font-size:.7em;
                color:black;
            }

            .cs-no .cs {
                color:red;
            }

            .terms {
                position: absolute;
                top: 190px;
                left:-20px;
                font-size:.35em;
                text-align:left;
                line-height:1.5;
            }

            .terms-title {
                position: absolute;
                top: 195px;
                left:10px;
                font-size:.5em;
                font-weight:bold;
                text-align:left;
            }

         
         

           


                
     

        </style>
    </head>
<body>
    
 
    <div class="voucher-body" style="z-index:2099;">
        <img src="{{ asset('public/images/vip_coupon_template.jpg')}}" style="border:1px solid #000;width:100%;height:260px;"/>

        <div class="qr-code" >
            <img  src="data:image/png;base64, {!! base64_encode(QrCode::format('png')
                ->size(500)->errorCorrection('H')
                ->generate(1)) !!} "
                width="83" />
        </div>
            
        <div class="amount">1,000</div>
        <div class="terms-title">Terms and Condition:</div>
        <div class="terms">
            <span class="list"><?php echo $promo->terms; ?></span>
        </div>
        <div class="coupon-no">0001</div>
                
        <div class="cs-no"><span class="label">CS no.</span> <span class="cs">CS1234</span></div>
    </div>
       
      
    
        

</body>
</html>