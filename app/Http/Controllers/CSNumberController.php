<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CSNumber;

class CSNumberController extends Controller
{
    public function getCSNumbers(Request $request)
    {
        $term = $request->term;
        $csNumber = new CSNumber;
        $csNos = $csNumber->getCSNumbers($term);
        return response()->json($csNos);
    }

}
