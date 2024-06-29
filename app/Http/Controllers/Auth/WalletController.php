<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Watllet;
use App\Services\EthService;
use App\Services\WalletService;
use Illuminate\Http\Request;
/**
 * @OA\Schema(
 *     schema="Wallet",
 *     type="object",
 *     title="Wallet",
 *     description="A wallet schema",
 *     @OA\Property(
 *       property="address_wallet",
*         type="string",
*         description="The address Wallet"

 *     ),
 *     @OA\Property(
 *         property="blance",
 *         type="integer",
 *         description="The blance name"
 *     ),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         description="The user"
 *     )
 * )
 */
class WalletController extends Controller
{

    protected $walletService;

    public function __construct()
    {
         $this->middleware('auth:sanctum');
         $this->walletService = new WalletService();
    }
     /**
     * @OA\Post(
     *    path="/api/createWallet",
     *    summary="Create a new wallet",
     *    description="This endpoint creates a new wallet",
     *    tags={"Wallet"},
     *    security={{"Bearer":{}}},
     *    @OA\Response(
     *       response=200,
     *       description="Wallet created successfully",
     *       @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Wallet")
     *       )
     *    ),
     *    @OA\Response(
     *       response=400,
     *       description="Bad request",
     *       @OA\JsonContent(
     *          @OA\Property(property="err", type="string")
     *       )
     *    )
     * )
     */
    public function createWallet()
    {
        try {
            $wallet = $this->walletService->createWallet();
            return response()->json([
                'data' => $wallet,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'err' => $th->getMessage(),
            ], 400);
        }
    }
    /**
     * @OA\Get(
     *    path="/api/listWallet",
     *    summary="List wallet User",
     *    description="List wallet User",
     *    tags={"Wallet"},
     *    security={{"Bearer":{}}},
     *    @OA\Response(
     *       response=200,
     *       description="Wallet listed successfully",
     *       @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Wallet")
     *       )
     *    ),
     *    @OA\Response(
     *       response=400,
     *       description="Bad request",
     *       @OA\JsonContent(
     *          @OA\Property(property="err", type="string")
     *       )
     *    )
     * )
     */

    public function listWallet()
    {
        try {
            $wallet = $this->walletService->listWallet();
            return response()->json([
                'data' => $wallet,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'err' => $th->getMessage(),
            ], 400);
        }
    }
     /**
     * @OA\post(
     *    path="/api/transaction",
     *    summary="transaction wallet User",
     *    description="transaction wallet User",
     *    tags={"Wallet"},
     *    security={{"Bearer":{}}},
     *       @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *          required={"address"},
     *          @OA\Property(property="address", type="string"),
     *       )
     *    ),
     *    @OA\Response(
     *       response=200,
     *       description="Wallet listed successfully",
     *       @OA\JsonContent(
     *          @OA\Property(property="success", type="object", ref="#/components/schemas/Wallet")
     *       )
     *    ),
     *    @OA\Response(
     *       response=400,
     *       description="Bad request",
     *       @OA\JsonContent(
     *          @OA\Property(property="err", type="string")
     *       )
     *    )
     * )
     */
    public function transaction(Request $request)
    {
        try {
            $fromAddress  = $request->address;
            $eth = new EthService();
            return response()->json([
                'success' => $eth->transaction($fromAddress  ),
            ],200);
        } catch (\Throwable $th) {
             return response()->json([
                'err' => $th->getMessage(),
             ],400);
        }

    }

}
