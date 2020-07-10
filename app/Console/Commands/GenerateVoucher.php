<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VoucherService;
use App\Models\Coupon;

class GenerateVoucher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voucher:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $coupon = new Coupon;

        $approvedCoupons = $coupon->getApproved();

        foreach($approvedCoupons as $row){
            $voucherService = new VoucherService;
            $voucherService->generateVoucher(
                $row->coupon_id, 
                -1, 
                -1
            );
        }
       
    }
}
