<?php
namespace App\Model;

use Nette\Database\Context;
use Elastic\Elasticsearch\Client as ElasticsearchClient;

class ProductManager
{
    private $database;
    private $client;

    public function __construct(Context $database, ElasticsearchClient $client)
    {
        $this->database = $database;
        $this->client = $client;
    }

    public function searchProducts(array $searchParams, int $page, int $perPage)
    {
        $params = [
            'index' => 'products',
            'body'  => [
                'from' => ($page - 1) * $perPage,
                'size' => $perPage,
                'query' => [
                    'bool' => [
                        'must' => []
                    ]
                ]
            ]
        ];

        foreach ($searchParams as $key => $value) {
            if ($key === 'category' || $key === 'sizes' || $key === 'colors') {
                if (in_array($key, ['colors', 'categories'])) {
                    $value = json_encode(explode(',', $value));
                }
                if (in_array($key, ['sizes'])) {
                    $value = (explode(',', $value));
                    print_r($value);
                    die();
                }

                $queryType = (in_array($key, ['sizes'])) ? 'terms' : 'term';
                $params['body']['query']['bool']['must'][] = [
                    $queryType => [
                        $key => $value
                    ]
                ];
            }
        }

        $response = $this->client->search($params);
        $totalItems = $response['hits']['total']['value'];
        $totalPages = ceil($totalItems / $perPage);

        $results = $this->formatSearchResults($response);

        return [
            'products' => $results,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'totalItems' => $totalItems
        ];
    }

    private function formatSearchResults($response)
    {
        $results = [];
        foreach ($response['hits']['hits'] as $hit) {
            $results[] = $hit['_source'];
        }
        return $results;
    }

    public function addProduct($name, $url, $imageURL, $description, $sizes, $colors, $price, $categories): void
    {
        $colorsJson = json_encode($colors);
        $categoriesJson = json_encode($categories);
        // write to DB
        try {
            $this->database->table('products')->insert([
                'name' => $name,
                'url' => $url,
                'image' => $imageURL,
                'description' => $description,
                'sizes' => $sizes,
                'colors' => $colorsJson,
                'categories' => $categoriesJson,
                'price' => $price,
            ]);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    public function getProducts($params = null, $page = 1, $perPage = 10): array
    {
        try {
            $selection = $this->database->table('products');

            if (!empty($params['category'])) {
                $categoryJson = json_encode([$params['category']]);
                $selection->where('json_contains(categories, ?)', $categoryJson);
            }

            if (!empty($params['colors'])) {
                $colorsJson = json_encode([$params['colors']]);
                $selection->where('json_contains(colors, ?)', $colorsJson);
            }

            if (!empty($params['sizes'])) {
                $sizes = explode(',', $params['sizes']);
                foreach ($sizes as $size) {
                    $selection->where('sizes LIKE ?', "%$size%");
                }
            }

            $totalProducts = $selection->count('*');
            $totalPages = ceil($totalProducts / $perPage);
            $selection->limit($perPage, ($page - 1) * $perPage);

            $products = $selection->fetchAll();

            return [
                'products' => $products,
                'page' => $page,
                'totalPages' => $totalPages,
                'perPage' => $perPage,
                'totalItems' => $totalProducts,
            ];

        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //Get current product
    public function getProductById($id)
    {
        try {
            return $this->database->table('products')->get($id);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //update Product
    public function updateProduct($id, $name, $url, $imageURL, $description, $sizes, $colors, $price, $categories)
    {
        $colorsJson = json_encode($colors);
        $categoriesJson = json_encode($categories);
        try {
            $this->database->table('products')->where('id', $id)->update([
                'name' => $name,
                'url' => $url,
                'image' => $imageURL,
                'description' => $description,
                'sizes' => $sizes,
                'colors' => $colorsJson,
                'categories' => $categoriesJson,
                'price' => $price,
            ]);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //delete
    public function deleteProduct($id): void
    {
        try {
            $this->database->table('products')->where('id', $id)->delete();
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }
}
