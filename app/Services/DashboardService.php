<?php

namespace App\Services;
use App\Models\Voucher;
use App\Models\Denomination;

class DashboardService {
    
    public function getStatistics(){
        $denomination = new Denomination;
        $voucher = new Voucher;   
        
        
        
        $user = Auth()->user();
        
        if($user->user_type_id == 51){ // if dealer, get only their claims
            $recentClaims = $voucher->getRecentClaimsByDealer([
                'dealer_id' => $user->dealer_id
            ]);
            $voucherStats = $voucher->getVoucherStatsByDealer([
                'dealer_id' => $user->dealer_id
            ]);
            $totalDenomination = $denomination->getTotalDenominationByDealer([
                'dealer_id' => $user->dealer_id
            ]);
        }
        else {
            $recentClaims = $voucher->getRecentClaims([
                'user_type_id' => $user->user_type_id
            ]);
            $voucherStats = $voucher->getVoucherStats([
                'user_type_id' => $user->user_type_id
            ]);
            $totalDenomination = $denomination->getTotalDenomination([
                'user_type_id' => $user->user_type_id
            ]);
        }
        return [
            'totalCoupons' => $totalDenomination,
            'voucherStats' => $voucherStats,
            'recentClaims' => $recentClaims
        ];
    }
}