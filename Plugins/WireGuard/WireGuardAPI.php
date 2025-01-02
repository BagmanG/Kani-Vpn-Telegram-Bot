<?php

class WireGuardAPI
{
    private $baseUrl;
    private $token;

    public function __construct(string $baseUrl, string $token)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $token;
    }
    
    public function checkStatus(): string
    {
        $url = $this->baseUrl . '/healthz';
        $headers = [
            'Accept: application/json'
        ];
    
        return $this->sendRequest($url, $headers, 'GET'); // Возвращаем ответ в виде JSON
    }
    
    public function getClients(): string
    {
        $url = $this->baseUrl . '/api/clients';
        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Accept: application/json'
        ];

        $response = $this->sendRequest($url, $headers, 'GET');

        return $response; // Возвращаем ответ в виде JSON
    }

    public function createClient(): string
    {
        $url = $this->baseUrl . '/api/clients';
        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        ];

        // Отправляем пустое тело запроса
        $response = $this->sendRequest($url, $headers, 'POST', json_encode([]));

        return $response; // Возвращаем ответ в виде JSON
    }

    public function getClientById(int $id, string $format = null): string
    {
        $url = $this->baseUrl . '/api/clients/' . $id;
        $headers = [
            'Authorization: Bearer ' . $this->token,
        ];

        // Добавляем формат в параметры запроса, если он указан
        if ($format) {
            $url .= '?format=' . $format;
            if ($format === 'qr') {
                $headers[] = 'Content-Type: image/png';
            } elseif ($format === 'conf') {
                $headers[] = 'Content-Type: text/plain';
            }
        }

        $response = $this->sendRequest($url, $headers, 'GET');

        return $response; // Возвращаем ответ в виде JSON или другой формат
    }
    
    public function deleteClientById(int $id): string
    {
        $url = $this->baseUrl . '/api/clients/' . $id;
        $headers = [
            'Authorization: Bearer ' . $this->token,
        ];

        $response = $this->sendRequest($url, $headers, 'DELETE');

        return $response; // Возвращаем ответ в виде JSON или другой формат
    }
    
    public function updateEnableClient(int $id, bool $enable): string
    {
        $url = $this->baseUrl . '/api/clients/' . $id;
        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        ];
    
        // Формируем тело запроса
        $body = json_encode(['enable' => $enable]);
        
        // Отправляем PATCH запрос
        $response = $this->sendRequest($url, $headers, 'PATCH', $body);
    
        return $response; // Возвращаем ответ в виде JSON
    }
    
    private function sendRequest(string $url, array $headers, string $method, string $body = null): string
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // Если метод POST, устанавливаем тело запроса
        if (($method === 'POST' || $method === 'PATCH') && $body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        return $response; // Возвращаем ответ в виде строки JSON или другого формата
    }
}