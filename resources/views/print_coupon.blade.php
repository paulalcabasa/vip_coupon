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
                margin-right: 1.0cm;
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
         
            <td style="width:75%;">PASIG</td>
        </tr>
         <tr>
            <td style="font-weight:bold;width:10%;">Date :</td>
           
            <td style="width:75%;">May 21, 2020</td>
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
            <td>{{ sprintf('%06d',$row->id) }}</td>
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

    <br/>
    <hr/>

    <table border="0" style="width:100%;font-size:11px;" cellpadding="5" cellspacing="10" >
        <tr>
            <td>Prepared by: </td>
            <td>Checked by: </td>
            <td>Noted by: </td>
            <td>Approved by: </td>
            <td>Approved by: </td>
        </tr>
        <tr>
            <td colspan="6"><br/></td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black;"></td>
            <td style="border-bottom: 1px solid black;"></td>
            <td style="border-bottom: 1px solid black;">A. Dalida</td>
            <td style="border-bottom: 1px solid black;">H. Nakaguro</td>
            <td style="border-bottom: 1px solid black;">Y. Oyama</td>
        </tr>
    </table>

    <div style="page-break-before: always"></div>

<!--     @foreach($docs as $row)
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
    @endforeach -->
   
    
        

</body>
</html>