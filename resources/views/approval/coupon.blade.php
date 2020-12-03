<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            .display-value {
                display: inline-block;
                clear: both;
                width: 100%;
            }
        </style>
    </head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  	<body>
        
        <div class="col-lg-8 offset-lg-2" style="margin-top:2em;margin-bottom:2em;">
            <div class="card">
                <h5 class="card-header  bg-danger text-center text-light text-bold">VIP Coupon - Coupon Request Approval</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                           
                            <div class="mb-3">
                                <span class="font-weight-bold">Coupon No.</span>
                                <span class="display-value"><?php echo $couponDetails->coupon_id; ?></span>
                            </div>

                            <div class="mb-3">
                                <span class="font-weight-bold">Dealer.</span>
                                <span class="display-value"><?php echo $couponDetails->account_name; ?></span>
                            </div>

                            <div class="mb-3">
                                <span class="font-weight-bold">Description</span>
                                <span class="display-value"><?php echo $couponDetails->description; ?></span>
                            </div>

                            

                        </div>
                        <div class="col-md-4">

                           
                            <div class="mb-3">
                                <span class="font-weight-bold">Coupon Type</span>
                                <span class="display-value"><?php echo $couponDetails->coupon_type; ?></span>
                            </div>

                            <div class="mb-3">
                                <span class="font-weight-bold">Purpose</span>
                                <span class="display-value"><?php echo $couponDetails->purpose; ?></span>
                            </div>

                            <div class="mb-3">
                                <span class="font-weight-bold">Promo</span>
                                <span class="display-value"><?php echo $couponDetails->promo_name; ?></span>
                            </div>    
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <span class="font-weight-bold">Requestor</span>
                                <span class="display-value"><?php echo $couponDetails->created_by; ?></span>
                            </div>
                            <div class="mb-3">
                                <span class="font-weight-bold">Date Requested</span>
                                <span class="display-value"><?php echo $couponDetails->date_created; ?></span>
                            </div>
                        </div>
                    </div>

                    <hr />
                    <h6>Denomination</h6>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach($denominations as $row) : ?>
                            <tr>
                                <td><?php echo $row->amount; ?></td>
                                <td><?php echo $row->quantity; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="font-weight-bold">
                            <tr>
                                <td>Total</td>
                                <td>4,000</td>
                            </tr>
                        </tfoot>
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

