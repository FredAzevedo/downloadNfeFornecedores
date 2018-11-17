<?php

require_once "vendor/autoload.php";

use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;

try {

    $arr = [
        "atualizacao" => "2016-11-03 18:01:21",
        "tpAmb" => 1,
        "razaosocial" => "WIZARD SYSTEM TECNOLOGIA DA INFORMACAO",
        "cnpj" => "15581977000117",
        "siglaUF" => "RN",
        "schemes" => "PL008i2",
        "versao" => '4.00',
        "tokenIBPT" => "AAAAAAA",
        "CSC" => "GPB0JBWLUR6HWFTVEAS6RJ69GPCROFPBBB8G",
        "CSCid" => "000001",
        "proxyConf" => [
            "proxyIp" => "",
            "proxyPort" => "",
            "proxyUser" => "",
            "proxyPass" => ""
        ]   
    ];
    //monta o config.json
    $configJson = json_encode($arr);

    $content = file_get_contents('certs/certificado.pfx');

    $certificate = Certificate::readPfx($content, '123456');
    $tools = new Tools($configJson, $certificate);
    $tools->model('55');

    $chNFe = '42181081783912000189550020000301461000000017'; //chave de 44 digitos da nota do fornecedor
    $tpEvento = '210210'; //ciencia da operação
    $xJust = ''; //a ciencia não requer justificativa
    $nSeqEvento = 1; //a ciencia em geral será numero inicial de uma sequencia para essa nota e evento

    $response = $tools->sefazManifesta($chNFe,$tpEvento,$xJust = '',$nSeqEvento = 1);
    //você pode padronizar os dados de retorno atraves da classe abaixo
    //de forma a facilitar a extração dos dados do XML
    //NOTA: mas lembre-se que esse XML muitas vezes será necessário, 
    //      quando houver a necessidade de protocolos
    $st = new Standardize($response);
    //nesse caso $std irá conter uma representação em stdClass do XML
    $stdRes = $st->toStd();
    //nesse caso o $arr irá conter uma representação em array do XML
    $arr = $st->toArray();
    //nesse caso o $json irá conter uma representação em JSON do XML
    $json = $st->toJson();

} catch (\Exception $e) {
    echo $e->getMessage();
}