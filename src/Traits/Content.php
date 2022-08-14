<?php

namespace VipLeirinhas\Traits;

/**
 * Faz requisição pelo conteudo da url
 */
trait Content
{

    /**
     * Abre url e pega o conteúdo
     * 
     * @return string
     */
    private function getContent ($url)
    {
        $context = stream_context_create ([
            'http' => [
                'method' => 'GET',
                'header' => "accept-language: pt-BR,pt;q=0.9\r\n".
                    "origin: https://www.brasileirinhas.com.br\r\n".
                    "referer: https://www.brasileirinhas.com.br/\r\n".
                    "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36"
            ]
        ]);

        $content = @file_get_contents ($url, false, $context);

        if ($content === false){
            throw new \Exception  ("Falha ao abrir URL {$url}");
        }

        return $content;
    }
}
