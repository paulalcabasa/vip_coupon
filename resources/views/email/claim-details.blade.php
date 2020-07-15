<!DOCTYPE html>
<html lang="en">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  	<body>
        
        <div class=" col-lg-8 offset-lg-2" style="margin-top:2em;">
            
            <div class="card">
                <h5 class="card-header bg-primary text-center text-light text-bold">VIP Coupon - Claim Request</h5>
                <div class="card-body">
                    <div class="container mb-5">
                        <div class="row mb-3">
                            <div class="col-sm">
                                <label class="font-weight-bold">Claim Request No.</label>
                                <span class="d-block "><?php echo $header->id;?></span>
                            </div>
                            <div class="col-sm">
                                <label class="font-weight-bold">Dealer</label>
                                <span class="d-block"><?php echo $header->dealer;?></span>
                            </div>
                            <div class="col-sm">
                                <label class="font-weight-bold">Total Amount</label>
                                <span class="d-block"><?php echo $header->total_amount;?></span>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm">
                                <label class="font-weight-bold">Requested by</label>
                                <span class="d-block"><?php echo $header->created_by;?></span>
                            </div>
                            <div class="col-sm">
                                <label class="font-weight-bold">Coupon type</label>
                                <span class="d-block"><?php echo $header->coupon_type;?></span>
                            </div>
                            <div class="col-sm">
                                <label class="font-weight-bold">Date Requested</label>
                                <span class="d-block"><?php echo $header->date_created;?></span>
                            </div>
                        </div>
                    </div>

                    
                    <table class="table mt-10">
                        <thead>
                            <tr>
                                <th>Voucher Code</th>
                                <th>Amount</th>
                                <th>Customer</th>
                                <th>CS Number</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($lines as $line) : ?>
                            <tr>
                                <td><?php echo $line->voucher_code; ?></td>
                                <td><?php echo $line->amount; ?></td>
                                <td><?php echo $line->customer_name; ?></td>
                                <td><?php echo $line->cs_number; ?></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>

                    
                </div>

                <div class="card-footer">
                    <a href="<?php echo $approve_link; ?>" class="btn btn-success mr-3">Approve</a>
                    <a href="<?php echo $reject_link; ?>" class="btn btn-danger">Reject</a>
                </div>
            </div>
        </div>
   
    </body>
</html>

