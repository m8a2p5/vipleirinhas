<?php

namespace VipLeirinhas;

use VipLeirinhas\Parse\ParseFilme;

class Filme {

    /** @var string
     * Titulo do filme */
    public $titulo;

    /** @var string
     * Descriçao do filme */
    public $descricao;

    /** @var array
     * Array com frente e verso do filme */
    public $capas;

    /** @var array
     * Lista de tags do filme
     */
    public $tags;

    /** @var array
     * Lista com o elenco do filme
     */
    public $elenco;

    /** @var array
     * Lista de cenas e suas informações, streaming, descricao...
     */
    public $cenas;

    /** @var \VipLeirinhas\Parse\ParseFilme */
    protected $objFilme;

    public function __construct ($url)
    {
        $this->objFilme = new ParseFilme ($url);

        $this->titulo = $this->objFilme->getTitulo ();
        $this->descricao = $this->objFilme->getDescicao ();
        $this->capas = $this->objFilme->getCapas ();
        $this->tags = $this->objFilme->getTags ();
        $this->elenco = $this->objFilme->getElenco ();
        $this->cenas = $this->objFilme->getCenas ();
    }

    /**
     * Retorna as informações do filme como json
     * 
     * @throws \Exception Erro ao buscar informações do filme
     * 
     * @return string
     */
    public function toJson ()
    {
        return \json_encode ($this->getInfo (), JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_UNESCAPED_UNICODE);
    }

    /**
     * Retorna informações do filme em array
     * 
     * @throws \Exception Erro ao buscar informações do filme
     * 
     * @return array
     */
    public function getInfo ()
    {
        return [
            'url' => $this->objFilme->getURL (),
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'capas' => $this->capas,
            'tags' => $this->tags,
            'elenco' => $this->elenco,
            'cenas_filme' => $this->cenas
        ];
    }
    
    public function getTitulo ()
    {
        return $this->titulo;
    }

    public function getDescicao ()
    {
        return $this->descricao;
    }

    public function getCapas ()
    {
        return $this->capas;
    }

    public function getTags ()
    {
        return $this->tags;
    }

    public function getElenco ()
    {
        return $this->elenco;
    }

    public function getCenas ()
    {
        return $this->cenas;
    }

}