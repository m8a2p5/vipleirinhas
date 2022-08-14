<?php

namespace VipLeirinhas\Parse;

class ParseFotos {

    use \VipLeirinhas\Traits\RegEx;
    use \VipLeirinhas\Traits\Content;

    /** @var string */
    protected $url;

    /** @var string
     * Conteúdo da url de intancia
     */
    protected $content;

    /**
     * 
     * 
     * @param string $url Url de fotos do site Ex: https://www.brasileirinhas.com.br/atriz/isabella-martins/fotos.html
     * 
     * @throws \Exception Não foi possível pegar o conteúdo da url ou link invalido
     * 
     * @return void
     */
    public function __construct ($url)
    {
        $this->url = $url;

        if (!$this->isValidUrl ()){
            throw new \Exception("Url de fotos invalida!");
        }

        // pega o conteúdo da url
        $this->content = $this->getContent ($url);
        
        if (!$this->validContent ()) {
            throw new \Exception("Conteúdo da url de fotos invalido!");
        }
    }
    
    /**
     * Retorna a url de instancia da classe
     * 
     * @return string
     */
    public function getUrl ()
    {
        return $this->url;
    }

    /**
     * Valida o conteúdo da url de fotos
     * 
     * @return bool
     */
    function validContent ()
    {
        $pattern = '~<h1 class="nomeAtriz"[^>]+>.+?<\/h1>~';
        return $this->isMatch ($pattern, $this->content);
    }

    /**
     * Valida url passada para a classe
     * 
     * @return bool
     */
    function isValidUrl ()
    {
        // https://www.brasileirinhas.com.br/atriz/amanda-souza/fotos.html
        $pattern = '~https?:\/\/(?:www\.)?brasileirinhas\.com\.br\/atriz\/[\w\d-]{2,}\/fotos\.html~';
        return $this->isMatch ($pattern, $this->url);
    }

    /**
     * Retorna uma lista de fotos
     * 
     * @throws \Exception Não encontrou nenhuma foto
     * 
     * @return array
     */
    public function getListaFotos ()
    {
        $pattern = '~<img.*?class="w-100 imgGallery".*?src="(?<foto>https:\/\/static1.*?)".*?\/>~';
        $fotos = [];

        if ($this->isMatch ($pattern, $this->content))
        {
            $match = $this->getMatchPattern ($pattern, $this->content);
            foreach ($match as $img)
            {
                $fotos [] = $img ['foto'];
            }
            return $fotos;
        }
        
        throw new \Exception("Nenhuma lista de fotos nesse link!");
    }

    /**
     * Retorna o nome da atriz das fotos
     * 
     * @throws \Exception Não conseguiu o nome da atriz
     * 
     * @return string
     */
    public function getAtriz ()
    {
        $pattern = '~<h1.*?>(?<atriz>.*?)<\/h1>~';
        if ($this->isMatch ($pattern, $this->content)){
            $match = $this->getMatchPattern ($pattern, $this->content);
            return trim ($match [0]['atriz']);
        }

        throw new \Exception("Não foi possível conseguir o nome da atriz das fotos");
    }
    
}