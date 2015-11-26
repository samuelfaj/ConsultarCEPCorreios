<?php
/*
      ---------------------------------------------------------------
     |           Função para consultar nos Correios via CEP.         |
     |---------------------------------------------------------------|
     |             Uso:                                              |
     |                -> Ex: ConsultarCEP(90010191)                  |
     |---------------------------------------------------------------|
     |  Retorno:                                                     |
     |---------------------------------------------------------------|
     |  array(4) {                                                   |
     |     ["uf"]=>                                                  |
     |     string(2) "RS"                                            |
     |     ["cidade"]=>                                              |
     |     string(12) "Porto Alegre"                                 |
     |     ["bairro"]=>                                              |
     |     string(17) "Centro Histórico"                             |
     |     ["logradouro"]=>                                          |
     |     string(42) "Rua Sete de Setembro - de 1000/1001 ao fim"   |
     |  }                                                            |
     |---------------------------------------------------------------|
     |           Por Samuel Fajreldines - http://samukt.com          |
     |---------------------------------------------------------------|
 
*/
 
function ConsultarCEP($cep){
    $url = 'http://www.buscacep.correios.com.br/sistemas/buscacep/resultadoBuscaCepEndereco.cfm';
    $fields = array(
        'relaxation' => urlencode(intval($cep)) ,
        'tipoCEP'    => urlencode('ALL')        ,
        'semelhante' => urlencode('N')          ,
    );
 
    $fields_string = '';
 
    foreach($fields as $key=>$value):
        $fields_string .= $key.'='.$value.'&';
    endforeach;
 
    rtrim($fields_string, '&');
    $ch = curl_init();
 
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
 
    $result = utf8_decode(curl_exec($ch));
 
    curl_close($ch);
 
    $doc = new DOMDocument;
 
    $doc->preserveWhiteSpace = false;
    $doc->strictErrorChecking = false;
    $doc->recover = true;
 
    $doc->loadHTML(mb_convert_encoding($result, 'HTML-ENTITIES', 'UTF-8'));
 
    $xpath = new DOMXPath($doc);
 
    $query = "//table[@class='tmptabela']//td";
 
    $entries = $xpath->query($query);
 
    $uf         = explode('/',$entries->item(2)->nodeValue)[1];
    $cidade     = explode('/',$entries->item(2)->nodeValue)[0];
    $bairro     = substr($entries->item(1)->nodeValue,0,-2);
    $logradouro = substr($entries->item(0)->nodeValue,0,-2);
 
    $return = array(
        'uf'         => trim($uf)         ,
        'cidade'     => trim($cidade)     ,
        'bairro'     => trim($bairro)     ,
        'logradouro' => trim($logradouro) ,
    );
 
    if(!empty($return))
        return $return;
    else
        return false;
}
?>