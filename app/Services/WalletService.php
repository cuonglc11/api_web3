<?php
namespace App\Services;

use App\Models\Watllet;
use Web3\Wallet;
use Web3\Web3;
use Illuminate\Support\Facades\Auth;

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
        $createWallet->blance = \Web3\Utils::hexToDec($this->web3->eth()->getBalance($address));
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
    protected function update($data)
    {
        if (!$data) {

            return;
        }
        $balanceInWei = \Web3\Utils::hexToDec($this->web3->eth()->getBalance($data->address_wallet));
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
