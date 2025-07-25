<?php

namespace Xentixar\KhaltiSdk;

class Khalti
{
    private string $secret_key;
    protected string $return_url;
    protected string $website_url;
    protected string $amount;
    protected string $purchase_order_id;
    protected string $purchase_order_name;
    protected ?array $customer_info;
    protected ?array $amount_breakdown;
    protected ?array $product_details;

    public function setSecretKey(string $secret_key)
    {
        $this->secret_key = $secret_key;
    }

    public function config(string $return_url, string $website_url, string $amount, string $purchase_order_id, string $purchase_order_name, ?array $customer_info = null, ?array $amount_breakdown = null, ?array $product_details = [])
    {
        $this->return_url = $return_url;
        $this->website_url = $website_url;
        $this->amount = $amount;
        $this->purchase_order_id = $purchase_order_id;
        $this->purchase_order_name = $purchase_order_name;
        $this->customer_info = $customer_info;
        $this->amount_breakdown = $amount_breakdown;
        $this->product_details = $product_details;
    }

    public function init(bool $production = false)
    {
        $url = $production ? 'https://khalti.com/api/v2/epayment/initiate/' : 'https://a.khalti.com/api/v2/epayment/initiate/';

        $postData = [
            "return_url" => $this->return_url,
            "website_url" => $this->website_url,
            "amount" => $this->amount * 100,
            "purchase_order_id" => $this->purchase_order_id,
            "purchase_order_name" => $this->purchase_order_name,
        ];

        if ($this->customer_info) {
            $postData["customer_info"] = $this->customer_info;
        }

        if ($this->amount_breakdown) {
            $postData["amount_breakdown"] = $this->amount_breakdown;
        }

        if ($this->product_details) {
            $postData["product_details"] = $this->product_details;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                "Authorization: key $this->secret_key",
                "Content-Type: application/json",
            ),
        ));

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);

        if (isset($response['payment_url'])) {
            $redirectUrl = $response['payment_url'];
            header("Location: $redirectUrl");
            exit;
        } else {
            echo "Error: Invalid response from Khalti API.";
        }
    }


    public function decode()
    {
        $data = [
            'pidx' => $_GET['pidx'] ?? '',
            'txnId' => $_GET['txnId'] ?? '',
            'amount' => $_GET['amount'] ?? '',
            'total_amount' => $_GET['total_amount'] ?? '',
            'status' => $_GET['status'] ?? '',
            'mobile' => $_GET['mobile'] ?? '',
            'tidx' => $_GET['tidx'] ?? '',
            'purchase_order_id' => $_GET['purchase_order_id'] ?? '',
            'purchase_order_name' => $_GET['purchase_order_name'] ?? '',
            'transaction_id' => $_GET['transaction_id'] ?? '',
        ];

        return $data;
    }

    public function validate(string $transaction_code, bool $production = false)
    {

        $url = $production ? 'https://khalti.com/api/v2/epayment/lookup/' : 'https://a.khalti.com/api/v2/epayment/lookup/';

        $curl = curl_init();

        $postData = [
            "pidx" => $transaction_code
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                "Authorization: key $this->secret_key",
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
