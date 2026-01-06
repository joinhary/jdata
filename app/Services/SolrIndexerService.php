<?php

namespace App\Services;

use App\Models\SolrCheckModel;

class SolrIndexerService
{
    public static function reindex($model, object $item): void
    {
        self::delete($model->st_id);
        sleep(1);
        self::insert($model->st_id, $item);
    }

    public static function insert(int $stId, object $item): void
    {
        SolrCheckModel::create(['st_id' => $stId]);

        $data = array_merge(['st_id' => $stId], (array)$item);

        self::curl(
            'http://localhost:8983/solr/timkiemsuutra/update/json/docs?commit=true',
            json_encode($data),
            'application/json'
        );
    }

    public static function delete(int $stId): void
    {
        self::curl(
            'http://localhost:8983/solr/timkiemsuutra/update?commit=true',
            "<delete><query>st_id:$stId</query></delete>",
            'application/xml'
        );
    }

    private static function curl(string $url, string $payload, string $type): void
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ["Content-Type: $type"],
        ]);
        curl_exec($ch);
        curl_close($ch);
    }
}
