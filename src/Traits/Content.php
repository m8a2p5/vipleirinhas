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

        $cookie = new \GuzzleHttp\Cookie\CookieJar;

        $client = new \GuzzleHttp\Client ([
            'headers' => [
                "accept-language" => "pt-BR,pt;q=0.9",
                "origin" => "https://www.brasileirinhas.com.br",
                "referer" => "https://www.brasileirinhas.com.br/",
                "user-agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36"
            ],
            'cookies' => $cookie
        ]);

        $content = $client->get ($url);

        if ($content->getStatusCode() != 200){
            throw new \Exception  ("Falha ao abrir URL {$url}");
        }

        return $content->getBody();
    }
}
