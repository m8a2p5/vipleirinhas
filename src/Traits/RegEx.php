<?php

namespace VipLeirinhas\Traits;

/**
 * Processa expressões regulares
 */
trait RegEx
{

    /**
     * Testa um expresão regular com o valor passado
     * 
     * @param string $pattern
     * @param string $value
     * 
     * @return bool
     */
    public function isMatch ($pattern, $value)
    {
        $match = $this->getMatchPattern ($pattern, $value);

        if (isset ($match [0])){
            return (count ($match [0])>=1);
        }

        return false;
    }
    
    /**
     * Retorna um array com os valores casados pela expresão regular
     * 
     * @param string $pattern
     * @param string $value
     * 
     * @return array Lista de match
     */
    public function getMatchPattern ($pattern, $value)
    {
        $status_match = preg_match_all ($pattern, $value, $match, PREG_UNMATCHED_AS_NULL|PREG_SET_ORDER);

        if ($status_match !== false){
            return $match;
        }
        return [];
    }
    
}
