<?php
namespace App\Services;

use Web3\Web3 as Web3Php;
class EthService {
    protected $web3 ;
    public function __construct()
    {
           $this->web3  = new Web3Php(env('WEB3_ETH'));
    }
    public function transaction($address)
    {
        try {
          $eth = $this->web3->eth();
          return $eth->getTransactionByHash($address);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => $th->getMessage(),
            ],400);
        }

    }
    protected function update($data)
    {
        if (!$data) {

            return;
        }
        $balanceInWei = \Web3\Utils::hexToDec($this->web3->getBalance($data->address_wallet));
        $sepolia =  bcdiv($balanceInWei, bcpow('10', '18', 0), 0);
        if($data->blance !==  $sepolia ) {
            $data->blance = $sepolia;
            $data->save();
            return;
        }
        return;

    }
}
?>
