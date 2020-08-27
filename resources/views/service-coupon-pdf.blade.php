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
    

     <header>
        <table width="100%" style="margin-bottom:-0.5em;">
            <tr>
                <td align="left">
                    <span style="font-size:16px;font-weight:bold;">Isuzu Philippines Corporation</span>
                </td>
            </tr>
            <tr>

                <td align="left">
                    <span style="font-size:12px;">114 Technology Avenue, Laguna Technopark Phase II, Biñan, Laguna 4024 Philippines</span>
                </td>
                 <td align="right" style="vertical-align: bottom;font-size:11px;">VIP Coupon System</td>
            </tr>
        </table>
        <hr/>
        
    </header>

    <h5>VIP Coupon Form (Dealers Receiving Copy)</h5>
    
    <table border="0" style="border-collapse: collapse;width:100%;font-size:11px;" cellpadding="2" >
        <tr>
            <td style="font-weight:bold;width:10%;">Coupon No. :</td>
           
            <td style="width:75%;">{{ $header->coupon_id }}</td>
        </tr>
        <tr>
            <td style="font-weight:bold;width:10%;">Dealer Name :</td>
         
            <td style="width:75%;">{{ $header->account_name }}</td>
        </tr>
         <tr>
            <td style="font-weight:bold;width:10%;">Date :</td>
           
            <td style="width:75%;">{{ $header->date_created }}</td>
        </tr>
    </table>
    <br/>
    <table border="1" style="border-collapse: collapse;width:100%;font-size:11px;" cellpadding="5" >
        <tr>
            <th>Voucher No</th>
            <th>Amount</th>
            <th>Voucher Code</th>
            <th>CS No</th>
        </tr>
        {{ $total = 0 }}
        @foreach($docs as $row)
            {{ $total += $row->amount }}
        <tr>
            <td>{{ $row->voucher_no }}</td>
            <td align="right">{{ number_format($row->amount,0,'',',') }}</td>
            <td>{{ $row->voucher_code }}</td>
            <td>{{ $row->cs_number }}</td>
        </tr>
        @endforeach

        <tr>
            <th align="left">Total : {{ count($docs) }}</th>
            <th align="right">{{ number_format($total) }}</th>
            <th></th>
            <th></th>
        </tr>
    </table>
    <div style="page-break-before: always"></div>
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
        <div class="terms-title">Terms and Condition:</div>
        <div class="terms">
            <span class="list"><?php echo $header->terms; ?></span>
        </div>
        <div class="coupon-no">Control No. {{ $row->voucher_no }}</div>
        <div class="validity">Expires at : {{ $row->expiration_date }}</div>
    </div>   
    @endforeach

</body>
</html>