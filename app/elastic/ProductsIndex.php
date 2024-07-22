<?php
    require __DIR__ . '/../../vendor/autoload.php';

    use Elastic\Elasticsearch\ClientBuilder;

    $dsn = $_ENV['DB_URL'];
    $username = $_ENV['DB_NAME'];
    $password = $_ENV['DB_PASS'];

    try {
        $pdo = new PDO($dsn, $username, $password);
        $stmt = $pdo->query('SELECT * FROM products');
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }

    $client = ClientBuilder::create()
        ->setBasicAuthentication($_ENV['ELASTIC_USER'], $_ENV['ELASTIC_PASS'])
        ->setHosts(['https://localhost:9200'])
        ->setSSLVerification(false)
        ->build();

    // Create index 'products'
    $createIndexParams = [
        'index' => 'products',
        'body' => [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0
            ],
            'mappings' => [
                'properties' => [
                    'category' => ['type' => 'keyword'],
                    'sizes' => ['type' => 'keyword'],
                    'colors' => ['type' => 'keyword'],
                ]
            ]
        ]
    ];

    // Check if index exist
    if (!$client->indices()->exists(['index' => 'products'])) {
        $response = $client->indices()->create($createIndexParams);
        echo "Index 'products' created.\n";
    }

    // Indexing products
    foreach ($products as $product) {
        $indexProductParams = [
            'index' => 'products',
            'id'    => $product['id'],
            'body'  => $product
        ];

        try {
            $response = $client->index($indexProductParams);
            echo "Indexed product ID: {$product['id']}\n";
        } catch (Exception $e) {
            echo "Failed to index product ID: {$product['id']}. Error: {$e->getMessage()}\n";
        }
    }
