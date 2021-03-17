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
               
            </tr>
        </table>
        <hr/>
        
    </header>
    <?php  
        $total = 0;
        foreach($lines as $row) {
            $total += $row->claimed_amount;
        }
    ?>
    <table border="0" style="border-collapse: collapse;width:100%;font-size:11px;"  cellpadding="2" >
        <tr>
            <td style="font-weight:bold;" width="450px">Claim Request No. : {{ $header->id }}</td>
            <td style="font-weight:bold;" width="150px">{{ date('F d, Y', strtotime($header->creation_date)) }}</td>
        
        </tr>
        <tr>
            <td style="font-weight:bold;width:10%;">Dealer Name : {{ $header->dealer }}</td>
            
            <td style="font-weight:bold;width:10%;">Count : {{ count($lines) }}</td>
        </tr>
        <tr>
            <td style="font-weight:bold;width:10%;">Vehicle Type : {{ $header->vehicle_type }}</td>
          

            <td style="font-weight:bold;width:10%;">Total Coupon Claim : {{ number_format($total,2) }}</td>
        </tr>
    </table>
    <br/>
 
    <table border="1" style="border-collapse: collapse;width:100%;font-size:11px;" cellpadding="5" >
        <tr>
            <th>#</th>
            <th>Coupon number</th>
            <th>Customer</th>
            <th>Control No.</th>
            <th>CS Number</th>
            <th>Claimed amount</th>

        </tr>
        <?php
            $total = 0;
            $ctr = 1;

            foreach($lines as $row) {
                $total += $row->claimed_amount;
        ?>
        <tr>
            <td align="center">{{ $ctr }}</td>
            <td align="center">{{ $row->coupon_no }}</td>
            <td align="left">{{ $row->customer_name }}</td>
            <td align="center">{{ sprintf('%06d', $row->id) }}</td>
            <td align="center">{{ $row->cs_number }}</td>
            <td align="right">{{ number_format($row->claimed_amount, 2) }}</td>
        </tr>
        <?php
                $ctr++; 
            }
        ?>

        <tr>
            <th  align="center">Total</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th align="right">{{ number_format($total, 2) }}</th>
        </tr>
    </table>

    
    <div style="font-size:11px;margin-top:1em;">
        <span style="line-height: 2;display:block;">Submitted by</span>
        <strong>{{ strtoupper($header->created_by) }}</strong>
    </div>
</body>
</html>