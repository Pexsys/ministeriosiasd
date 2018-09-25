<?php
class JWT
{
  private static function Base64URLEncode($data)
  {
    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($data));
  }

  private static function Base64DecodeURL($string)
  {
    return base64_decode(str_replace(array('-', '_'), array('+', '/'), $string));
  }

  public static function Encode(array $payload, string $secret): string
  {
    $header = json_encode(array("alg" => "HS256", "typ" => "JWT"));
    $payload = json_encode($payload);
    $header_payload = static::Base64URLEncode($header) . '.' . static::Base64URLEncode($payload);
    $signature = hash_hmac('sha256', $header_payload, $secret, true);
    return static::Base64URLEncode($header) . '.' . static::Base64URLEncode($payload) . '.' . static::Base64URLEncode($signature);
  }

  public static function Decode(string $token, string $secret): array
  {
    $token = explode('.', $token);
    $header = static::Base64DecodeURL($token[0]);
    $payload = static::Base64DecodeURL($token[1]);
    $signature = static::Base64DecodeURL($token[2]);
    $header_payload = $token[0] . '.' . $token[1];
    if (hash_hmac('sha256', $header_payload, $secret, true) !== $signature) throw new \Exception('Invalid signature');
    return json_decode($payload, true);
  }

  public static function Secret($value)
  {
    $method = "AES-256-CBC";
    $options = 0;
    $encryptedData = openssl_encrypt($value, $method, CFG::Get()->Var("Key"), $options, CFG::Get()->Var("Iv"));
    return substr(preg_replace('/\W+/', '', $encryptedData), 0, 8);
    // $decryptedData = openssl_decrypt($encryptedData, $method, $key, $options, $iv);
    // echo "$encryptedData|$decryptedData";  //nSFt1VHQ
  }
}
