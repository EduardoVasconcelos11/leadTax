<?php

namespace App\Http\Controllers;

use App\Services\ScraperService;
use App\Models\Product;

class ProductController extends Controller
{
  protected $scraperService;
  private $scrapeUrl = 'https://www.mercadolivre.com.br/ofertas';

  public function __construct(ScraperService $scraperService)
  {
    $this->scraperService = $scraperService;
  }

  public function scrape()
  {
    $products = $this->scraperService->scrapeProducts($this->scrapeUrl);
    $this->scraperService->saveProducts($products);

    return redirect()->route('products.index');
  }
  public function deleteAll()
  {
    $this->scraperService->deleteAllProducts();
  }
  public function index()
  {
    $products = Product::all();
    return view('products.index', compact('products'));
  }
}
