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
                right: 340px;
                font-weight:bold;
                font-size:3.9em;
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

           


                
     

        </style>
    </head>
<body>
    

   
     
    @foreach($docs as $row)
   
 
    <div class="voucher-body" style="z-index:2099;">
        <img src="{{ asset('public/images/service-coupon.jpg')}}" style="border:1px solid #000;width:100%;height:260px;"/>

        <div class="qr-code" >
            <img  src="data:image/png;base64, {!! base64_encode(QrCode::format('png')
                ->size(500)->errorCorrection('H')
                ->generate($claimApiUrl . $row->voucher_code)) !!} "
                width="70" />
        </div>

        <div class="amount"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span>  {{ number_format($row->amount,0,'',',') }}</div>
        <div class="coupon-no">Control No. {{ $row->voucher_no }}</div>
    </div>
       
      
   
    @endforeach
   
    
        

</body>
</html>