<?php
declare(strict_types=1);

class BrasilApiService
{
    public function proximosFeriados(int $limite = 5): array
    {
        $hoje    = date('Y-m-d');
        $feriados = $this->fetchAno((int) date('Y'));

        $proximos = array_values(array_filter($feriados, fn($f) => $f['date'] >= $hoje));

        if (count($proximos) < $limite) {
            $proxAno  = $this->fetchAno((int) date('Y') + 1);
            $proximos = array_merge($proximos, $proxAno);
        }

        return array_slice($proximos, 0, $limite);
    }

    private function fetchAno(int $ano): array
    {
        $ctx  = stream_context_create(['http' => ['timeout' => 5]]);
        $json = @file_get_contents("https://brasilapi.com.br/api/feriados/v1/{$ano}", false, $ctx);

        if (!$json) {
            return [];
        }

        return json_decode($json, true) ?? [];
    }
}
