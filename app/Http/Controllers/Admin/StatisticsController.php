<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;


class StatisticsController extends Controller
{
    public function index()
    {
        $totalRevenue = $this->getTotalRevenue();
        $totalSales = $this->getTotalSales();
        $productsSold = $this->getProductsSold();
        $averageSale = $this->getAverageSale();

        return view('admin.statistics.index', compact(
            'totalRevenue',
            'totalSales',
            'productsSold',
            'averageSale'
        ));
    }

    private function getTotalRevenue()

    {
        return Sale::sum('total');
    }

    private function getTotalSales()
    {
        return Sale::count();
    }

    private function getProductsSold()
    {
        return SaleItem::sum('quantity');
    }

    private function getAverageSale()
    {
        return Sale::avg('total');
    }
}
