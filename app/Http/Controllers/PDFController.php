<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\stock_places;
use PDF;

class PDFController extends Controller
{
    public function pdf(){
        $stocks = stock_places::all();
        $pdf = PDF::loadView('pdf',['stocks'=>$stocks]);
        return $pdf->stream();
    }
}
