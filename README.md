# 📍 BRASILEIRINHAS VIP

Esse pacote extrai e formata informações do site [Brasileirinhas.com.br](https://brasileirinhas.com.br) incluindo cenas completas de filmes em todos os formatos disponíveis ( ***240p***, ***480p***, ***720p***, ***1080p***, ***1440p***, ***2160p(4K)*** )

## Requisitos
- PHP >= 7.0

## Instalação

Execute esse comando usando o composer e o pacote será instalado.  
Baixe o composer aqui: https://getcomposer.org/download/

```shell
composer require m8a2p5/vipleirinhas
```

## Exemplo

Extraindo informações de um filme, preview, capas, poster, cena, tags, elenco, qualidades disponíveis...

```php
<?php

include __DIR__.'/vendor/autoload.php';

use VipLeirinhas\Filme;
use VipLeirinhas\Fotos;

try {
    
    $filme = new Filme ('https://www.brasileirinhas.com.br/filme/a-flor-da-pele.html');

    $titulo = $filme->getTitulo ();
    $capas = $filme->getCapas ();
    $cenas = $filme->getCenas ();

    echo "Filme: {$titulo}\n";
    echo "Capa: {$capas ['capa_frente']}\n";
    echo "Qualidades disponíveis: \n";
    
    foreach ($cenas ['cenas'] as $cena){
        $melhor_qualidade = $cena ['qualidades'][(count ($cena ['qualidades'])-1)];

        echo "  • {$melhor_qualidade ['qualidade_video']} - {$melhor_qualidade ['url']}\n";
    }
    

} catch (\Throwable $th) {
    echo "Erro: {$th->getMessage ()}\n";
    echo "Linha: {$th->getTraceAsString()}\n";
}

```