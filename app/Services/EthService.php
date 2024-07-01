<?php
namespace App\Services;

use App\Models\transaction;
use Carbon\Carbon;
use GuzzleHttp\Client;

class EthService {
    protected $client ;
    protected $apiKey;
    public function __construct()
    {
            $this->client = new Client();
            $this->apiKey = env('KEY_BSC');
    }
    public function getBalance($address)
    {
        try {
            $res  =  $this->client->request('GET',env('API_BSC'),[
              'query' => [
                'module' => 'account',
                'action' => 'balance',
                'address' => $address,
                'apikey' => $this->apiKey,
              ]
            ]);
            $data =  json_decode($res->getBody(), true);
            return isset($data['result']) ? $data['result'] : null;

        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage(),
            ],400);
        }

    }
    public function getTransactions($address)
    {
        try {
            $response = $this->client->request('GET', env('API_BSC'), [
                'query' => [
                    'module' => 'account',
                    'action' => 'txlist',
                    'address' => $address,
                    'startblock' => 0,
                    'endblock' => 99999999,
                    'sort' => 'asc',
                    'apikey' => $this->apiKey,
                ]
            ]);
            $data =  json_decode($response->getBody(), true);
            \Log::info('API Response: ' . $response->getBody());
             if(isset($data['result'])) {
                 foreach ($data['result'] as $item) {
                    transaction::create([
                        'blockNumber' => $item['blockNumber'],
                        'to' => $item['to'],
                        'from' => $item['from'],
                        'timeStamp' => $this->fomatDate($item['timeStamp']),
                        'hash' => $item['hash'],
                        'value' => $item['value']

                    ]);
                 }
             }
            return isset($data['result']) ? $data['result'] : null;
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage(),
            ],400);
        }
    }
    public function transactionHash($hash) {
        try {
            $response = $this->client->request('GET', env('API_BSC'), [
                'query' => [
                    'module' => 'account',
                    'action' => 'txlistinternal',
                    'txhash' => $hash,
                    'apikey' => $this->apiKey,
                ]
            ]);
            $data =  json_decode($response->getBody(), true);
            \Log::info('API Response: ' . $response->getBody());
            return isset($data['result']) ? $data['result'] : null;


        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage(),
            ],400);
        }
    }
    protected function fomatDate($timestamp )
    {
        $dataTime  =  Carbon::createFromTimestamp($timestamp);
        return $dataTime->format('Y-m-d H:i:s');

    }
    protected function getValue($value) {
        return $value / pow(10, 18);
    }
}
?>
