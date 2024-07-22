<?php
namespace App\Model;


class SearchManager
{
    private $client;

    public function __construct(\Elasticsearch\Client $client)
    {
        $this->client = $client;
    }

    public function search($index, $type, $query)
    {
        $params = [
            'index' => $index,
            'type'  => $type,
            'body'  => [
                'query' => [
                    'match' => [
                        'text' => $query
                    ]
                ]
            ]
        ];

        return $this->client->search($params);
    }
}
