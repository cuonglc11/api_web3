<?php
namespace App\Services;

use App\Models\Watllet;
use Web3\Wallet;
use Web3\Web3;
use Illuminate\Support\Facades\Auth;
use Web3\Contract;

class WalletService {
    protected $web3 ;
    public function __construct()
    {
           $this->web3  = new Web3(env('WEB3_PROVIDER'));
    }
    public function createWallet()
    {
        $wallet  = Wallet::create($this->web3);
        $createWallet = new Watllet();
        $address = $wallet->getAddress();
        $key = $wallet->getPrivateKey();
        $createWallet->customer_id =   Auth::id();
        $createWallet->address_wallet = $address;
        $createWallet->private_key = $key;
        $createWallet->blance = \Web3\Utils::hexToDec($this->web3->getBalance($address));
        $createWallet->save();
        return [
            'customer' => $createWallet->user->name,
            'email' => $createWallet->user->email,
            'address' => $createWallet->address_wallet,
            'private_key' =>  $createWallet->private_key,
            'blance' => $createWallet->blance

        ];

    }
    public function ListWallet()
    {
            try {

                $listWallet = Watllet::where('customer_id' , Auth::id())
                ->select('address_wallet','blance','customer_id' ,'created_at')
                ->get()
                ->load('user')
                ->each(function ($wallet) {
                    $wallet->makeHidden(['customer_id']);
                    $wallet->makeHidden(['updated_at']);

                });
                if($listWallet){
                foreach($listWallet as  $item )
                {
                    $this->update($item);
                    $item->load('user');
                }
                return [
                    $listWallet
                ];
                }
                return [
                'not fount wallet'
            ];
            } catch (\Throwable $th) {
                throw new \Exception("Error Processing Request", $th->getMessage());

            }

    }
    public function contract($fromAddress ,$toAddress , $key , $price )
    {
        try {
        $abi = '[{"inputs":[{"internalType":"string","name":"name","type":"string"},{"internalType":"string","name":"symbol","type":"string"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"}]';

        $wallet = Wallet::createByPrivate($key);
        $contract = Contract::at($this->web3 , $abi , $fromAddress);
        $res =$contract->send($wallet,'transfer',[$toAddress,\Web3\Utils::ethToWei($price)]);
        $res = $contract->decodeEvent($res);
        $res =$contract->call('balanceOf',[$toAddress]);
        return  response()->json([
            'transaction' => $res,
        ],200);
        // // $amountInWei  = Utils::ethToWei($price ,  'ether');
        // $transaction =  [
        //     'from' => $fromAddress,
        //     'to' => $toAddress ,
        //     'value' => $amountInWei,
        //     'gasPrice' => Utils::toWei('10' , 'gwai')
        // $eth = new Eth
        // ];
        // $this->web3->personal->sendTransaction($transaction , $key , function ($err , $transactionHash ){

        // });
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
