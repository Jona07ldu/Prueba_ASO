<?php
// Obtén los datos enviados desde el carrito (JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Verifica que los datos estén completos
if (!isset($data['amount']) || !isset($data['storeId']) || !isset($data['currency'])) {
    echo json_encode(['error' => 'Faltan datos requeridos']);
    exit;
}

// Datos del pago (debes completar con tu información)
$token = 'sk_live_tu_token_aqui'; // Token público de producción de PayPhone
$storeId = $data['storeId'];
$amount = $data['amount'];
$currency = $data['currency'];
$clientTransactionId = $data['clientTransactionId']; // Generado en el carrito
$amountWithoutTax = $data['amountWithoutTax'];
$tax = $data['tax'];

// URL de la API de PayPhone para crear un pago
$url = 'https://api.payphonetodoesposible.com/payment/create';

// Datos a enviar a la API de PayPhone
$payload = json_encode([
    'storeId' => $storeId,
    'amount' => $amount,
    'currency' => $currency,
    'clientTransactionId' => $clientTransactionId,
    'amountWithoutTax' => $amountWithoutTax,
    'tax' => $tax,
]);

// Configuración de la solicitud cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

// Ejecuta la solicitud
$response = curl_exec($ch);
curl_close($ch);

// Si hay errores en la solicitud cURL
if ($response === false) {
    echo json_encode(['error' => 'Error en la conexión con PayPhone']);
    exit;
}

// Convierte la respuesta de la API a formato de array
$responseData = json_decode($response, true);

// Si la respuesta contiene una URL de pago, redirige
if (isset($responseData['paymentUrl'])) {
    echo json_encode(['paymentUrl' => $responseData['paymentUrl']]);
} else {
    echo json_encode(['error' => 'No se pudo generar el link de pago']);
}
?>
