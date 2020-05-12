<?php

namespace App\Services;
use App\Models\Voucher;
use App\Models\Denomination;

class DashboardService {
    
    public function getStatistics(){
        $denomination = new Denomination;
        $voucher = new Voucher;   
        $totalDenomination = $denomination->getTotalDenomination();
        $voucherStats = $voucher->getVoucherStats();
        $recentClaims = $voucher->getRecentClaims();
        return [
            'totalCoupons' => $totalDenomination,
            'voucherStats' => $voucherStats,
            'recentClaims' => $recentClaims,
        ];
    }
}