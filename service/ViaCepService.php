<?php
declare(strict_types=1);

class ViaCepService
{
    public function lookup(string $cep): ?array
    {
        $cep = preg_replace('/\D/', '', $cep);
        if (strlen($cep) !== 8) {
            return null;
        }

        $ctx  = stream_context_create(['http' => ['timeout' => 5]]);
        $json = @file_get_contents("https://viacep.com.br/ws/{$cep}/json/", false, $ctx);

        if (!$json) {
            return null;
        }

        $data = json_decode($json, true);
        if (!$data || isset($data['erro'])) {
            return null;
        }

        return [
            'logradouro' => $data['logradouro'] ?? '',
            'bairro'     => $data['bairro']     ?? '',
            'cidade'     => $data['localidade'] ?? '',
            'uf'         => $data['uf']         ?? '',
        ];
    }
}
