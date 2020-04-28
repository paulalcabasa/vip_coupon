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
                margin-top: 0.63cm;
                margin-left: 0.95cm;
                margin-right: 2.54cm;
                margin-bottom: 0.16cm;
            }

            .container {
                position: relative;
                text-align: center;
                color: black;
                width:458px;
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
                top: 240px;
                right: 12px;
                font-weight:bold;
                font-size:1em;
                color:red;
            }

        </style>
    </head>
<body>

    @foreach($docs as $row)
    <div class="container">
        <img src="{{ asset('public/images/vip_coupon_template.jpg')}}" style="border:1px solid #000;width:100%;"/>
   
        <div class="qr-code">
            <img  src="data:image/png;base64, {!! base64_encode(QrCode::format('png')
                ->size(500)->errorCorrection('H')
                ->generate($row->voucher_code)) !!} "
                width="84" />
        </div>

        <div class="amount">{{ number_format($row->amount,0,'',',') }}</div>
        <div class="coupon-no">{{ sprintf('%06d',$row->id) }}</div>
    </div>
    @endforeach
   
    
        

</body>
</html>