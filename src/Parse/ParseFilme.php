<?php

namespace VipLeirinhas\Parse;

class ParseFilme {

    use \VipLeirinhas\Traits\RegEx;
    use \VipLeirinhas\Traits\Content;

    /** @var string */
    protected $url;

    /** @var string
     * Conteúdo da url
     */
    protected $content;

    const BASE_CDN_POSTER = 'https://static1.brasileirinhas.com.br/Brasileirinhas/images/conteudo/cenas/player/%d.jpg';
    const BASE_PLAYER = 'https://www.brasileirinhas.com.br/2020/play-2019.php?codVideo=%s&idVideo=%d&qtde=7&v=2';
    const BASE_CDN_PLAYLIST = 'https://videos01.brasileirinhas.com.br/vod/%s/playlist.m3u8';
    const BASE_CDN_PREVIEW = 'https://static2.brasileirinhas.com.br/preview/%s_%d.jpg';
    const BASE_CDN_VIDEO = 'https://videos01.brasileirinhas.com.br/vod/%s/%s';

    /**
     *
     * 
     * @param string $url URL do filme no site brasileirinhas.com.br/filmes/
     * 
     * @throws \Exception Url do filme invalida ou erro com o conteúdo de parse
     * 
     * @return void
     */
    public function __construct ($url)
    {

        if (!$this->isValidUrl ($url)){
            throw new \Exception  ("URL invalida!");
        }

        $this->url = $url;
        $this->content = $this->getContent ($url);
        
        if (!$this->validContent()) {
            throw new \Exception("Conteúdo da URL invalida!");
        }
        
    }

    /**
     * Retorna a url da instancia da classe
     * 
     * @return string
     * 
     */
    public function getURL ()
    {
        return $this->url;
    }

    /**
     * Valida url com padrão esperado
     * 
     * @param string $url
     * 
     * @return bool
     */
    protected function isValidUrl ($url)
    {
        $pattern = '~https?:\/\/(:?www\.)?brasileirinhas\.com\.br\/filme\/.+\.html$~';
        return $this->isMatch ($pattern, $url);
    }

    /**
     * Valida o conteúdo da url, verifica se é o que se espera
     * 
     * @return bool
     */
    protected function validContent ()
    {
        $pattern = '~<li\s*class="breadcrumb-item active"[^>]+>([^<]+)~';
        return $this->isMatch ($pattern, $this->content);
    }
    
    /**
     * Limpa string, formata e remove os códigos html
     * 
     * @param int $str
     * 
     * @return string
     */
    public function cleanStr ($str)
    {
        return html_entity_decode (strip_tags (trim ($str)));
    }

    /**
     * Retorna o titulo do filme
     * 
     * @throws \Exception Não conseguiu o titulo do filme
     * 
     * @return string
     */
    public function getTitulo ()
    {
        $pattern = '|<li\s*class="breadcrumb-item active"[^>]+>(?<titulo>[^<]+)|';
        if ($this->isMatch($pattern, $this->content)){

            $match = $this->getMatchPattern ($pattern, $this->content);
            return $this->cleanStr ($match [0]['titulo']);

        }else {
            throw new \Exception  ('Erro ao buscar o título do vídeo!');
        }    
    }

    /**
     * Retorna a descrição do filme
     * 
     * @return string
     */
    public function getDescicao ()
    {
        $pattern = '|<meta[^>]+name="description".*?content="(?<descricao>[^"]+)"|';
        if ($this->isMatch ($pattern, $this->content)){

            $match = $this->getMatchPattern ($pattern, $this->content);

            return $this->cleanStr ($match [0]['descricao']);

        }
        
        return null;
    }

    /**
     * Retorna um array com as capas(frente, verso) do filme
     * 
     * @throws \Exception Não conseguiu as capas do filme
     * 
     * @return array
     */
    public function getCapas ()
    {
        $pattern_img_frente = '|<img[^>]+id="capa-media-frente".*?src="(?<url>[^">]+)"|';
        $pattern_img_tras = '|<img[^>]+id="capa-media-tras".*?src="(?<url>[^">]+)"|';

        if ($this->isMatch ($pattern_img_frente, $this->content) && $this->isMatch ($pattern_img_tras, $this->content)){

            $match_frente = $this->getMatchPattern ($pattern_img_frente, $this->content);
            $match_tras = $this->getMatchPattern ($pattern_img_tras, $this->content);

            return [
                'capa_frente' => $match_frente [0]['url'],
                'capa_tras' => $match_tras [0]['url'],
            ];

        }else {
            throw new \Exception  ("Erro, não foi possível conseguir as capas do vídeo!");
        }
    }

    /**
     * Retorna um array com as tags do filme
     * 
     * @return array
     */
    public function getTags ()
    {
        $pattern = '|<li class="liTaguer">(?<tag>.*?)<\/li>|';
        if ($this->isMatch ($pattern, $this->content)){

            $list_match = $this->getMatchPattern ($pattern, $this->content);
            $tags = [];

            foreach ($list_match as $match){
                $tag = $this->cleanStr ($match ['tag']);
                $tags [] = $this->formatTag ($tag);
            }

            return $tags;
        }
        return [];
    }

    /**
     * Retorna um array com a lista do elenco do filme
     * 
     * @return array
     */
    public function getElenco ()
    {

        $pattern = '/<a href="(?<url>\/atriz\/.*?)" title="(?:.*?)">\s+<img title="(?<atriz>.*?)"[^>]+src="(?<foto>.*?)"/';
        if ($this->isMatch ($pattern, $this->content)){

            $list_match = $this->getMatchPattern ($pattern, $this->content);
            $elenco = [];

            foreach ($list_match as $match){
                $elenco [] = [
                    'atriz' => $this->cleanStr ($match ['atriz']),
                    'foto' => $this->cleanStr ($match ['foto'])
                ];
            }

            return $elenco;
        }
        
        return [];
    }

    /**
     * Retorna um array com informações das cenas do filme
     * 
     * @throws \Exception Não conseguiu as cenas do filme
     * 
     * @return array
     */
    public function getCenas ()
    {
        $pattern = '|<div[^>]+id="(?<id>\d+)".*?data-codVideo="(?<hash>[^"]+)">|';
        if ($this->isMatch ($pattern, $this->content)){

            $list_match = $this->getMatchPattern ($pattern, $this->content);
            $hash_cenas = [];

            foreach ($list_match as $key => $match){

                $id = $match ['id'];
                $hash = $match ['hash'];
                $descricao_cena = $this->descricaoCena ($key);
                $preview = $this->previewCenas ($hash);

                $poster_cena = sprintf (self::BASE_CDN_POSTER, $id);
                $playlist = sprintf (self::BASE_CDN_PLAYLIST, $hash);
                $player = sprintf (self::BASE_PLAYER, $hash, $id);
                $qualidade = $this->formatsM3U8 ($playlist, $hash);

                $data_video ['id'] = $id;
                $data_video ['poster'] = $poster_cena;
                $data_video ['playlist'] = $playlist;
                $data_video ['player'] = $player;
                $data_video ['descricao'] = $descricao_cena;
                $data_video ['preview'] = $preview;
                $data_video ['qualidades'] = $qualidade;
                
                $hash_cenas ['cenas'][] = $data_video;

            }
            
            $hash_cenas ['count'] = count ($hash_cenas ['cenas']);
            
            return $hash_cenas;

        }else {
            throw new \Exception  ("Não foi possível buscar as cenas desse filme");
        }
    }

    /**
     * Retorna a descriçao curta de uma cena
     * 
     * @param int $id ID da cena do filme
     * 
     * @return string
     */
    protected function descricaoCena ($id)
    {

        $pattern = '|<div[^<]+class="col-6 discCena">(?<descricao>.*?)<\/div>|';
        if ($this->isMatch ($pattern, $this->content)){

            $match = $this->getMatchPattern ($pattern, $this->content);
            $descricao = $this->cleanStr ($match [$id]['descricao'] ?? null);

            return $descricao;

        }
        
        return null;

    }

    /**
     * Retorna um array de imagens(preview) de cenas
     * 
     * @param string $hash_cena Hash da cena do filme
     * 
     * @return array
     */
    protected function previewCenas ($hash_cena)
    {
        $preview = [];

        for ($p=15; $p <= 59; $p+=10) {
            $imagem = sprintf(self::BASE_CDN_PREVIEW, $hash_cena, $p);
            if (strpos (get_headers ($imagem)[0], '200') !== false) {
                $preview [] = $imagem;
            }
        }

        return $preview;
    }

    /**
     * Retorna um array com as cenas, qualidades, codec, url de streaming...
     * 
     * @param string $playlist URL com as qualidades disponíveis das cenas
     * @param string $hash Hash indentificador da cena
     * 
     * @throws \Exception Se não conseguir acesso a playlist passada de paramêtro
     * 
     * @return array
     */
    public function formatsM3U8 ($playlist, $hash)
    {

        $content_playlist = @file_get_contents ($playlist);
        $pattern = '|#EXT-X-STREAM-INF:BANDWIDTH=(?<banda>[\d]+),NAME="(?<qualidade>\w+)",CODECS="(?<codecs>.*)"\n(?<url>[^\n]+)|';

        if ($content_playlist === false){
            throw new \Exception  ("Erro, não foi possível obter os dados da playlist do vídeo!");
        }
        
        if ($this->isMatch ($pattern, $content_playlist)){

            $list_match = $this->getMatchPattern ($pattern, $content_playlist);
            $qualidade = [];

            foreach ($list_match as $match){
                $qualidade [] = [
                    'qualidade_video' => $match ['qualidade'],
                    'codecs' => $match ['codecs'] ?? null,
                    'banda' => $match ['banda'] ?? null,
                    'url' => sprintf (self::BASE_CDN_VIDEO, $hash, $match ['url'])
                ];
            }

            return $qualidade;

        }else {
            throw new \Exception  ("Erro, não foi possível obter a lista de vídeos da playlist!");
        }
    }

    /**
     * Corrige tags do filme removendo caracteres indesejados
     * 
     * @param string $tag
     * @return string Retorna um nova string formatada
     */
    public function formatTag ($tag)
    {
        $tag = \strtolower ($tag);
        $erro = ['/[âáàâä]/u', '/[éèêë]/u', '/[íìîï]/u', '/[óòôõö]/u', '/úùûü/u', '/ç/u', '/[\/ -]/'];
        $correcao = ['a', 'e', 'i', 'o', 'u', 'c', '_'];

        return preg_replace($erro, $correcao, $tag);
    }
}