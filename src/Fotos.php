<?php

namespace VipLeirinhas;

use \VipLeirinhas\Parse\ParseFotos;

class Fotos {

    /** @var array
     * Lista de fotos do link
     */
    public $fotos;

    /** @var string
     * Nome da atriz do link
     */
    public $atriz;

    /** @var \VipLeirinhas\Parse\ParseFotos */
    protected $objFotos;

    public function __construct ($url)
    {
        $this->objFotos = new ParseFotos ($url);
        $this->fotos = $this->objFotos->getListaFotos ();
        $this->atriz = $this->objFotos->getAtriz ();
    }

    /**
     * Retorna fotos e informações da atriz em json
     * 
     * @throws \Exception Erro ao buscar fotos
     * 
     * @return string
     */
    public function toJson ()
    {
        return \json_encode ($this->getInfo (), JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_UNESCAPED_UNICODE);
    }

    /**
     * Retorna fotos e informações da atriz
     * 
     * @throws \Exception Erro ao buscar informações
     * 
     * @return array
     */
    public function getInfo ()
    {
        return [
            'url' => $this->objFotos->getUrl (),
            'atriz' => $this->objFotos->getAtriz (),
            'fotos' => $this->objFotos->getListaFotos ()
        ];
    }

    public function getFotos ()
    {
        return $this->fotos;
    }

    public function getAtriz ()
    {
        return $this->atriz;
    }

}