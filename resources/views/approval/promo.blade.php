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
                <h5 class="card-header  bg-danger text-center text-light text-bold">VIP Coupon - Promo Approval</h5>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-3 font-weight-bold">Promo Code</div>
                        <div class="col-md-9"><?php echo $promoDetails->id; ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 font-weight-bold">Promo Name</div>
                        <div class="col-md-9"><?php echo $promoDetails->promo_name; ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 font-weight-bold">Coupon Type</div>
                        <div class="col-md-9"><?php echo $promoDetails->coupon_type; ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 font-weight-bold">Effective date from</div>
                        <div class="col-md-9"><?php echo $promoDetails->effective_date_from; ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 font-weight-bold">Effective date to</div>
                        <div class="col-md-9"><?php echo $promoDetails->effective_date_to; ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 font-weight-bold">Coupon expiry date</div>
                        <div class="col-md-9"><?php echo $promoDetails->coupon_expiry_date; ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 font-weight-bold">Terms</div>
                        <div class="col-md-9"><?php echo $promoDetails->terms; ?></div>
                    </div>
                    
                   
                </div>

                <div class="card-footer">
                    <a href="<?php echo $approve_link; ?>" class="btn btn-success mr-3">Approve</a>
                    <a href="<?php echo $reject_link; ?>" class="btn btn-danger">Reject</a>
                </div>
            </div>
        </div>
   
    </body>
</html>

