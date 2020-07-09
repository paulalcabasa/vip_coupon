<!DOCTYPE html>
<html lang="en">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  	<body>
        
        <div class=" col-lg-6 offset-lg-3" style="margin-top:2em;">
            <div class="card ">
                <h5 class="card-header  bg-primary text-center text-light text-bold">VIP Coupon - Claim Coupon</h5>
                <div class="card-body">
                    <form method="POST" action="<?php echo $claim_api; ?>">
                        <input type="hidden" class="form-control" name="voucher_id" value="<?php echo $voucherDetails->id; ?>"/>
                        <div class="form-group">
                            <label>Voucher Code</label>
                            <input type="text" class="form-control" name="voucher_code" value="<?php echo $voucherDetails->voucher_code; ?>" readonly="readonly"/>
                        </div>
        
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="text" class="form-control" name="amount" value="<?php echo $voucherDetails->amount; ?>"/>
                        </div>

                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" class="form-control" name="customer_name" required/>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
   
    </body>
</html>

