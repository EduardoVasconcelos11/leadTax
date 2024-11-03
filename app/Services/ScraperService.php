<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use App\Helpers\ResponseHelper;

class ScraperService
{
  protected $client;

  public function __construct()
  {
    $this->client = new Client([
      'headers' => [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
      ],
      'verify' => false
    ]);
  }

  public function scrapeProducts($url)
  {
    Log::info('Iniciando o scraping da URL: ' . $url);

    try {
      $response = $this->client->get($url);
      $html = $response->getBody()->getContents();
      $crawler = new Crawler($html);

      Log::info('Página carregada com sucesso.');

      $products = $crawler->filterXPath('//div[contains(@class, "andes-card poly-card")]')->each(function (Crawler $node) {
        try {
          $imageUrl = $node->filterXPath('.//div[@class="poly-card__portada"]//img')->attr('data-src') ?: $node->filterXPath('.//div[@class="poly-card__portada"]//img')->attr('src');
          $title = $node->filterXPath('.//a[@class="poly-component__title"]')->text();
          $price = $node->filterXPath('.//span[@class="andes-money-amount__fraction"]')->text();

          Log::info('Produto encontrado: ' . $title . ' - Preço: R$ ' . $price);

          return [
            'name' => $title,
            'price' => floatval(str_replace('.', '', $price)),
            'image_url' => $imageUrl
          ];
        } catch (\Exception $e) {
          Log::error('Erro ao processar produto: ' . $e->getMessage());
          return null;
        }
      });

      $products = array_filter($products);
      Log::info(count($products) . ' produtos foram encontrados e processados com sucesso.');

      return $products;
    } catch (\Exception $e) {
      Log::error('Erro ao acessar a URL: ' . $e->getMessage());
      return ResponseHelper::apiResponse('Erro ao acessar a URL', null, 500);
    }
  }

  public function saveProducts($products)
  {
    Log::info('Iniciando o salvamento de produtos no banco de dados.');

    foreach ($products as $productData) {
      try {
        Product::create($productData);
        Log::info('Produto salvo com sucesso: ' . $productData['name']);
      } catch (\Exception $e) {
        Log::error('Erro ao salvar produto: ' . $productData['name'] . ' - ' . $e->getMessage());
      }
    }

    Log::info('Todos os produtos foram salvos no banco de dados.');
  }

  public function deleteAllProducts()
  {
    Log::info('Iniciando exclusão de todos os produtos no banco de dados.');

    try {
      Product::truncate();
      Log::info('Todos os produtos foram excluídos com sucesso.');
      return ResponseHelper::apiResponse('Todos os produtos foram excluídos com sucesso!', null, 200);
    } catch (\Exception $e) {
      Log::error('Erro ao excluir produtos: ' . $e->getMessage());
      return ResponseHelper::apiResponse('Erro ao excluir produtos', null, 500);
    }
  }
}
