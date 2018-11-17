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
    
    $tools = new Tools($configJson, Certificate::readPfx($content, '123456'));
    //só funciona para o modelo 55
    $tools->model('55');
    //este serviço somente opera em ambiente de produção
    $tools->setEnvironment(1);
    $chave = '42180981783912000189550020000292191000000012';

    $tpEvento = '210210'; //ciencia da operação
    $xJust = ''; //a ciencia não requer justificativa
    $nSeqEvento = 1; //a ciencia em geral será numero inicial de uma sequencia para essa nota e evento

    $response = $tools->sefazManifesta($chave,$tpEvento,$xJust = '',$nSeqEvento = 1);
    $response = $tools->sefazDownload($chave);

    $stz = new Standardize($response);
    $std = $stz->toStd();
    if ($std->cStat != 138) {
        echo "Documento não retornado. [$std->cStat] $std->xMotivo";  
        die;
    }    
    $zip = $std->loteDistDFeInt->docZip;
    $xml = gzdecode(base64_decode($zip));

    header('Content-type: text/xml; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$chave.'.xml"');
    echo $xml;
    
} catch (\Exception $e) {
    echo str_replace("\n", "<br/>", $e->getMessage());
}