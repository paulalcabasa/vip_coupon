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

            .validity {
                position: absolute;
                top: 190px;
                right: 10px;
                font-weight:bold;
                font-size:.4em;
                color:black;
            }
            
            .vehicle-type {
                position: absolute;
                top: 200px;
                right: 45px;
                font-weight:bold;
                font-size:.4em;
                color:black;
            }



                
     

        </style>
    </head>
<body>

    <?php if($single == 'N') : ?>
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
<!-- 
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
            <td style="border-bottom: 1px solid black;">{{ ucwords(substr($header->first_name,0,1)) }}. {{ ucwords($header->last_name)}}</td>
            <td style="border-bottom: 1px solid black;"></td>
            <td style="border-bottom: 1px solid black;">A. Dalida</td>
            <td style="border-bottom: 1px solid black;">H. Nakaguro</td>
            <td style="border-bottom: 1px solid black;">Y. Oyama</td>
        </tr>
    </table> -->

    <div style="page-break-before: always"></div>
    <?php endif; ?>
    @foreach($docs as $row)
   
 
    <div class="voucher-body" style="z-index:2099;">
        <img src="{{ asset('public/images/vip_coupon_template.jpg')}}" style="border:1px solid #000;width:100%;height:260px;"/>

        <div class="qr-code" >
            <img  src="data:image/png;base64, {!! base64_encode(QrCode::format('png')
                ->size(500)->errorCorrection('H')
                ->generate($claimApiUrl . $row->voucher_code)) !!} "
                width="83" />
        </div>

        <div class="amount">{{ number_format($row->amount,0,'',',') }}</div>
        <div class="terms-title">Terms and Condition:</div>
        <div class="terms">
            <span class="list"><?php echo $header->terms; ?></span>
        </div>
        <div class="coupon-no">{{ $row->voucher_no }}</div>
        <div class="cs-no"><span class="label">CS no.</span> <span class="cs"><?php echo $row->cs_number; ?></span></div>
        <div class="validity"><span class="label">Expires at : </span> <span class="validity-text">{{ $row->expiration_date }}</span></div>
        <div class="vehicle-type"><span class="label">Type : </span> <span class="vehicle-type-text">{{ $header->vehicle_type }}</span></div>
    </div>
       
      
   
    @endforeach
   
    
        

</body>
</html>