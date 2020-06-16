<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PurposeService;


class PurposeController extends Controller
{

    private $purposeService;

    public function __construct(){
        $this->purposeService = new PurposeService;
    }

    public function getActive(){
        return response()->json($this->purposeService->getActive());
    }
}
