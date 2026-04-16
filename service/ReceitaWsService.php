<?php
declare(strict_types=1);

class ReceitaWsService
{
    public function lookup(string $cnpj): ?array
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        if (strlen($cnpj) !== 14) {
            return null;
        }

        $ctx  = stream_context_create(['http' => ['timeout' => 10]]);
        $json = @file_get_contents("https://www.receitaws.com.br/v1/cnpj/{$cnpj}", false, $ctx);

        if (!$json) {
            return null;
        }

        $data = json_decode($json, true);
        if (!$data || ($data['status'] ?? '') === 'ERROR') {
            return null;
        }

        return [
            'nome'       => $data['fantasia'] ?: ($data['nome'] ?? ''),
            'email'      => $data['email']    ?? '',
            'telefone'   => $data['telefone'] ?? '',
            'cep'        => preg_replace('/\D/', '', $data['cep']       ?? ''),
            'logradouro' => $data['logradouro'] ?? '',
            'numero'     => $data['numero']     ?? '',
            'bairro'     => $data['bairro']     ?? '',
            'cidade'     => $data['municipio']  ?? '',
            'uf'         => $data['uf']         ?? '',
        ];
    }
}
