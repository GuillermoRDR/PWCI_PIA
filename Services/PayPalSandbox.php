<?php

namespace Services;

use RuntimeException;

class PayPalSandbox
{
  private string $clientId;
  private string $secret;
  private string $token;

  public function __construct()
  {
    $this->clientId = (string) getenv('PAYPAL_SANDBOX_CLIENT_ID');
    $this->secret = (string) getenv('PAYPAL_SANDBOX_SECRET');
    if ($this->clientId === '' || $this->secret === '')
      throw new RuntimeException('Configura las credenciales de PayPal Sandbox.');
    $result = $this->request('/v1/oauth2/token', 'POST', 'grant_type=client_credentials', true);
    $this->token = (string) ($result['access_token'] ?? '');
    if ($this->token === '')
      throw new RuntimeException('PayPal no devolvió el token de acceso.');
  }

  private function request(string $path, string $method, string $body = '', bool $oauth = false, string $requestId = ''): array
  {
    $headers = $oauth ? ['Content-Type: application/x-www-form-urlencoded'] : ['Content-Type: application/json', 'Authorization: Bearer ' . $this->token];
    if ($requestId !== '')
      $headers[] = 'PayPal-Request-Id: ' . $requestId;
    $curl = curl_init('https://api-m.sandbox.paypal.com' . $path);
    curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_CUSTOMREQUEST => $method, CURLOPT_HTTPHEADER => $headers, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => true]);
    if ($oauth)
      curl_setopt($curl, CURLOPT_USERPWD, $this->clientId . ':' . $this->secret);
    if ($method === 'POST')
      curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
    $raw = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    $result = is_string($raw) ? json_decode($raw, true) : null;
    if ($status < 200 || $status >= 300 || !is_array($result))
      throw new RuntimeException('PayPal Sandbox rechazó la operación. Revisa las credenciales o el estado del pago.');
    return $result;
  }

  public function create(int $pedido, string $importe, string $moneda, string $returnUrl, string $cancelUrl): array
  {
    $body = json_encode(['intent' => 'CAPTURE', 'purchase_units' => [['reference_id' => (string) $pedido, 'amount' => ['currency_code' => $moneda, 'value' => number_format((float) $importe, 2, '.', '')]]], 'payment_source' => ['paypal' => ['experience_context' => ['return_url' => $returnUrl, 'cancel_url' => $cancelUrl, 'user_action' => 'PAY_NOW']]]], JSON_THROW_ON_ERROR);
    return $this->request('/v2/checkout/orders', 'POST', $body, false, 'kdemy-create-' . $pedido);
  }

  public function capture(string $order): array
  {
    if (!preg_match('/^[A-Z0-9]{10,30}$/i', $order))
      throw new RuntimeException('Identificador de orden inválido.');
    return $this->request('/v2/checkout/orders/' . $order . '/capture', 'POST', '{}', false, 'kdemy-capture-' . $order);
  }
}
