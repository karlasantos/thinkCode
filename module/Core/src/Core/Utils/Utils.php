<?php

namespace Core\Utils;

use Datetime;
use DateTimeZone;
use Exception;

class Utils {

	/**
     * raizDir
     * Diretorio raiz de armazenamento das notas
     * @var string
     */
	const RAIZ_DIR = '../UNIMAKE/Unimake/UniNFe/';


	// ***********************************************************************************************************************************************************
    // 				FORMATACAO (string, array, etc)
    // ***********************************************************************************************************************************************************

	/**
    * cleanString
    * Remove todos dos caracteres especiais do texto e os acentos
    *
    * @param  string $texto
    * @return string Texto sem caractere especiais
    */
    public static function cleanString($texto = '')
    {
        // Pode usar o PHP para remover acentos de forma simples usando iconv, respeitando maiúsculas e minusculas sem conflito. IGNORE vai ignorar os
        //caracteres que porventura não tenham tradução. Depois preg_replace vai remover o que não for A-Z e 0-9, deixando uma string limpa sem espaços,
        //símbolos ou caracteres especiais.
        //$string = iconv( "UTF-8" , "ASCII//TRANSLIT//IGNORE" , $string );

        $texto = trim($texto);
        $aFind = array('&','á','à','ã','â','é','ê','í','ó','ô','õ','ú','ü',
            'ç','Á','À','Ã','Â','É','Ê','Í','Ó','Ô','Õ','Ú','Ü','Ç');
        $aSubs = array('e','a','a','a','a','e','e','i','o','o','o','u','u',
            'c','A','A','A','A','E','E','I','O','O','O','U','U','C');
        $novoTexto = str_replace($aFind, $aSubs, $texto);
        $novoTexto = preg_replace("/[^a-zA-Z0-9 @,-.;:\/]/", "", $novoTexto);
        return $novoTexto;
    }//fim cleanString


    /*
    * scape_array
    *   Função para trocar os caracteres a ser escapados. Todos os textos de um documento XML passam por uma análise do “parser” específico da linguagem.
    *   Alguns caracteres afetam o funcionamento deste “parser”, não podendo aparecer no texto de uma forma não controlada.
    *   Os caracteres que afetam o “parser” são:
    *   > (sinal de maior),&lt;
    *   < (sinal de menor),&gt;
    *   & (e-comercial),&amp;
    *   " (aspas),&quot;
    *   ' (sinal de apóstrofe).&#39;
    */
    public static function scape_array(&$array, $key) {
        if(is_string($array)){
            $order = array("\t","\n","&",">", "<", '"',"'");
            $replace = array(' - ',' - ','&amp;','&lt;', '&gt;', '&quot;','&#39;');
            $array = str_replace($order, $replace, $array);
        }
    }


    /**
     * Função para remover máscara de um texto
     *
     * @param  string $texto
     * @return string texto sem mascara
     */
    public static function unmask($texto) {
        return preg_replace('/[\-\|\(\)\/\. ]/', '', $texto);
    }

    /**
     * translate
     * Retorna a versão em português do texto enviado
     *
     * @param  string $texto texto em inglês
     * @return string $texto em português
     */
    public static function translate($message)
    {
        $msg = "";

        $en = array( //"{http://www.portalfiscal.inf.br/nfe}"
        			"http://www.portalfiscal.inf.br/nfe:"
        			,"'http://www.portalfiscal.inf.br/nfe'"
                    ,"[facet 'pattern']"
                    ,"The value"
                    ,"is not accepted by the pattern"
                    ,"has a length of"
                    ,"[facet 'minLength']"
                    ,"this underruns the allowed minimum length of"
                    ,"[facet 'maxLength']"
                    ,"this exceeds the allowed maximum length of"
                    ,"Element"
                    ,"attribute"
                    ,"is not a valid value of the local atomic type"
                    ,"is not a valid value of the atomic type"
                    ,"Missing child element(s). Expected is"
                    ,"The document has no document element"
                    ,"[facet 'enumeration']"
                    ,"one of"
                    ,"failed to load external entity"
                    ,"Failed to locate the main schema resource at"
                    ,"This element is not expected. Expected is"
                    ,"is not an element of the set"
                    ,"is invalid according to its datatype"
                    ,"The Enumeration constraint failed"
                    ,"element is invalid"
                    ,"The element"
        			,"in namespace"
        			,"has invalid child element"
        			,"List of possible elements expected"
        			,"has incomplete content"
        			,"The Pattern constraint failed"
                    ,"The actual length is less than the MinLength value"
        			,"The actual length is less than the MaxLength value"
        			,"The");
        $pt = array(""
        			,""
                    ,"[Erro 'Layout']"
                    ,"O valor"
                    ,"não é aceito para o padrão."
                    ,"tem o tamanho"
                    ,"[Erro 'Tam. Min']"
                    ,"deve ter o tamanho mínimo de"
                    ,"[Erro 'Tam. Max']"
                    ,"Tamanho máximo permitido"
                    ,"Elemento"
                    ,"Atributo"
                    ,"não é um valor válido"
                    ,"não é um valor válido"
                    ,"Elemento filho faltando. Era esperado"
                    ,"Falta uma tag no documento"
                    ,"[Erro 'Conteúdo']"
                    ,"um de"
                    ,"falha ao carregar entidade externa"
                    ,"Falha ao tentar localizar o schema principal em"
                    ,"Este elemento não é esperado. Esperado é"
                    ,"não é um dos seguintes possiveis"
                    ,"é inválido de acordo com seu tipo de dados"
                    ,"O valor informado não esta entre os valores aceitos pela enumeração usada para esse atributo"
                    ,"é inválido"
                    ,"O elemento"
                    ,""
                    ,"tem elemento filho inválido"
                    ,"Lista de possiveis elementos esperados"
                    ,"apresenta conteúdo incompleto"
                    ,"A restrição padrão falhou"
                    ,"O comprimento real é menor que o valor Mínimo"
                    ,"O comprimento real é maior que o valor Máximo"
                    ,"O elemento");

        $msg = str_replace($en,$pt,$message);

        return $msg;
	}




	// ***********************************************************************************************************************************************************
    // 				FISCAL
    // ***********************************************************************************************************************************************************
	/**
     * montaChaveXML
     * Remonta a chave da NFe de 44 digitos com base em seus dados
     * Isso é útil no caso da chave informada estar errada
     * se a chave estiver errada a mesma é substituida
     *
     * @param object $dom
     * @return string $chave chave gerada com 44 digitos
     */
    public static function montaChaveXML( $dom ){
        $infNFe = $dom->getElementsByTagName("infNFe")->item(0);
        $ide = $dom->getElementsByTagName("ide")->item(0);
        $emit = $dom->getElementsByTagName("emit")->item(0);
        $cUF = $ide->getElementsByTagName('cUF')->item(0)->nodeValue;
        $dhEmi = $ide->getElementsByTagName('dhEmi')->item(0)->nodeValue;
        $CNPJ_emit = $emit->getElementsByTagName('CNPJ')->item(0)->nodeValue;
        $mod = $ide->getElementsByTagName('mod')->item(0)->nodeValue;
        $serie = $ide->getElementsByTagName('serie')->item(0)->nodeValue;
        $nNF = $ide->getElementsByTagName('nNF')->item(0)->nodeValue;
        $tpEmis = $ide->getElementsByTagName('tpEmis')->item(0)->nodeValue;
        $cNF = $ide->getElementsByTagName('cNF')->item(0)->nodeValue;
        $chave = str_replace('NFe', '', $infNFe->getAttribute("Id"));
        $tempData = explode("-", $dhEmi);

        // Monta a chave com 43 digitos
        $chaveMontada =	substr(str_pad(abs((int)$cUF			), 2,'0',STR_PAD_LEFT),0, 2).
						substr(str_pad(abs((int)$tempData[0] - 2000 	), 2,'0',STR_PAD_LEFT),0, 2).
						substr(str_pad(abs((int)$tempData[1] 	), 2,'0',STR_PAD_LEFT),0, 2).
						substr(str_pad($CNPJ_emit			 	,14,'0',STR_PAD_LEFT),0,14).
						substr(str_pad(abs((int)$mod 			), 2,'0',STR_PAD_LEFT),0, 2).
						substr(str_pad(abs((int)$serie 			), 3,'0',STR_PAD_LEFT),0, 3).
						substr(str_pad(abs((int)$nNF 			), 9,'0',STR_PAD_LEFT),0, 9).
						substr(str_pad(abs((int)$tpEmis 		), 1,'0',STR_PAD_LEFT),0, 1).
						substr(str_pad(abs((int)$cNF			), 8,'0',STR_PAD_LEFT),0, 8);
		//00.20,00.00000000000000,00.000,000000000.0.18641952.x

        //caso a chave contida na NFe esteja errada então deve-se substituir a chave
        // if ($chaveMontada != $chave) {
        //     $ide->getElementsByTagName('cDV')->item(0)->nodeValue = substr($chaveMontada, -1);
        //     $infNFe = $dom->getElementsByTagName("infNFe")->item(0);
        //     $infNFe->setAttribute("Id", "NFe" . $chaveMontada);
        //     $this->chNFe = $chaveMontada;
        // }

		$cDV    = $ide->getElementsByTagName('cDV')->item(0)->nodeValue  = $data['cDV'] = self::calculaDV($chaveMontada);

		//Adiciona o Digito Verificador com o ultimo digito da chave, que passa agora a ter 44 digitos
		$chave  = $data['Id'] = $chaveMontada .= $cDV;
		$infNFe = $dom->getElementsByTagName("infNFe")->item(0);
		if(empty($infNFe))		return("'infNFe' não encontrado");
		$infNFe->setAttribute("Id", "NFe" . $chave);

        return $chaveMontada;
    }

    /**
     * calculaDv
     * Descobre o digito verificador para o ultimo digito da chave da NFe
     *
     * @param string $chave43
     * @return int $valor com o valor do digito verificador
     */
    public static function calculaDV($chave43) {
        $soma_ponderada = 0;
	    $multiplicadores = array(2,3,4,5,6,7,8,9);
	    $i = 42;

	    while ($i >= 0) {
	        for ($m=0; $m<count($multiplicadores) && $i>=0; $m++) {
	            $soma_ponderada+= $chave43[$i] * $multiplicadores[$m];
	            $i--;
	        }
	    }

	    $resto = $soma_ponderada % 11;

	    if ($resto == '0' || $resto == '1') {
	       return 0;
	    } else {
	       return (11 - $resto);
	    }
	}

    /**
     * searchForFile
     * Procura por um determinado arquivo durante um determinado tempo
     *
     * @param sting $fileName nome do arquivo a ser encontrado
     * @param int $tentativas quantidade de vezes para repetir a procura (após cada tentativa irá ocorrer uma espera de dois segundos)
     * @param sting $msg Mensagem a ser exibida
     * @return array $arq_result array contendo todos os arquivos que coincidiram com a busca
     */
    public static function searchForFile( $fileName, $tentativas, $extensao, $msg = null ) {
        // NFE = aguardar num-lot por no máximo 20 segundos
        // NFE = aguardar pro-rec por no minimo um minuto
        // $tentativas =  30 vezes => 60 segundos => 1 minuto

        $fileNameSearch = $fileName.'*';

        if(empty( $tentativas ) ||  $tentativas == 0 ){
            // $tentativas = 120; // 120 vezes de 1/2 meio segundo equivale a 1 minutos(60 segundos)
            $tentativas = 30; // 30 vezes de 2 segundos equivale a aproximadamente 1 minutos(60 segundos)
        }

        $arq_result = array(
            'errors'    => array(),
            'files_'    => array(),
        );

        $cont = 0;

        // Enquanto não encontrar nenhum retorno ou não exceder o contador de tentativas continua procurando
        while ( empty($arq_result['files_']) && $cont < $tentativas) {
            try {
                // procura por algum arquivo que tenha retornado para a nota transmitida
                $arq_result['files_'] = glob( $fileNameSearch );

                // Se ainda não encontrou aguarda dois segundos para tentar denovo
                if( empty($arq_result['files_']) ){
                    // usleep(500000); //aguarda meio segundo..
                    sleep(2); //aguarda 2 segundos..

                } else {
                    //primeiro verificar se o xml retornou com erros de validação (com o schema) na pasta retorno
                    $file_return = $fileName.$extensao;

                    if ( file_exists($file_return) ){ //retornou com erro de validação
                        $arq_result['errors'] = self::isXMLinconsistente( $file_return );
                    }
                }

                $cont ++;

            } catch (Exception $e) {

            }
        }

        return $arq_result;
    }

	/**
     * isXMLinconsistente
     * Retorna o erro de validação do xml quando o erro for do tipo XML INCONSISTENTE, ou seja, erro de validação com o schema.
     * Quando o erro não for XML INCONSISTENTE, então vai retornar o Message do erro.
     * @param  string $arquivo nome do arquivo .err a ser verificado
     * @return string $message mensagem a ser retornada para o usuário
     */
    public static function isXMLinconsistente($arquivo = '')
    {
        $message = "Ocorreu um erro, por favor tente novamente mais tarde, ou, entre em contato com o nosso suporte e informe a chave da nota que esta apresentando erro.";
        $search = "XML INCONSISTENTE";
        $array_explode_validacao = array();
        $array_explode_erro = array();
        $separa_erro_e_linha = array();
        $array_erros = array();


        $file = file_get_contents($arquivo);
        // Procura pela frase XML INCONSISTENTE dentro do arquivo
        if(strpos($file, $search)) {

            // salvamos o resultado explode na variável $array_explode - onde o arquivo resultará em um array de tres indices .:
            // [0] o que esta antes de 'Início da validação...'
            // [1] o que esta depois de 'Início da validação...' mas antes de '...Final da validação'
            // [2] o que esta depois de '...Final da validação'
            $array_explode_validacao = explode('...', $file);

            $array_explode_erro = explode( "\n", $array_explode_validacao[1] ); // Separa a frase em duas usando como delimitador a palavra 'Erro:''

            foreach ($array_explode_erro as $k=>$error){

                if( ( strpos($error,"Arquivo")!== false ) || (empty($error)) || (!strlen($error)) ) {//verificar se tem conteúdo
                    // remove os textos que dizem qual a nota e qual o schema que estão sendo utilizados
                    unset($array_explode_erro[$k]);

                } else {

                    $separa_erro_e_linha = explode("Erro:", $error); // Separa a frase que contém o numero da linha de erro da frase que contem o erro.

                    if(!empty($separa_erro_e_linha[1])){
                        array_push($array_erros, self::translate( $separa_erro_e_linha[1] ) );// Pega  apenas a parte com a frase de descrição do erro
                    }
                }
            }
            //Se não encontrou a frase XML INCONSISTENTE retorná-ra o Message
        } else {

            $array_explode_erro = explode( "\n", $file ); // Separa em array observando as quebras de linha

            if( strpos( $array_explode_erro[2], "Message") !== FALSE ){

                $separa_erro_e_linha = explode("|", $array_explode_erro[2]);

                array_push($array_erros, $separa_erro_e_linha[1] );// Pega  apenas a parte que contém a frase com a Mensagem do erro

                // Se retornar o nome do método RecepcionarLoteRps - Adiiconar a informação para pesoa tentar novamente, pois trata-se de erro de timeout
                if( strpos($separa_erro_e_linha[1], "Erro ao invocar o método") !== FALSE ){
                    array_push($array_erros, "Tempo limite excedido, execute a operação novamente!");
                }
            }
        }

        $message = $array_erros;

        return $message;
    }

    /** NÂO USADO
    * validXML
    * Verifica o xml com base no xsd
    * Esta função pode validar qualquer arquivo xml do sistema de NFe
    * Há um bug no libxml2 para versões anteriores a 2.7.3
    * que causa um falso erro na validação da NFe devido ao
    * uso de uma marcação no arquivo tiposBasico_v1.02.xsd
    * onde se le {0 , } substituir por *
    * A validação não deve ser feita após a inclusão do protocolo !!!
    * Caso seja passado uma NFe ainda não assinada a falta da assinatura será desconsiderada.
    * @name validXML
    * @author Roberto L. Machado <linux.rlm at gmail dot com>
    * @param    string  $xml  string contendo o arquivo xml a ser validado ou seu path
    * @param    string  $xsdfile Path completo para o arquivo xsd
    * @param    array   $aError Variável passada como referencia irá conter as mensagens de erro se houverem
    * @return   boolean
    */
    public static function validXML($xml='', $xsdFile='', &$aError){
        try{
            $flagOK = true;
            // Habilita a manipulaçao de erros da libxml
            libxml_use_internal_errors(true);
            //limpar erros anteriores que possam estar em memória
            libxml_clear_errors();
            //verifica se foi passado o xml
            if(strlen($xml)==0){
                $msg = 'Você deve passar o conteudo do xml assinado como parâmetro ou o caminho completo até o arquivo.';
                $aError[] = $msg;
                throw new nfephpException($msg);
            }
            // instancia novo objeto DOM
            $dom = new DomDocument('1.0', 'utf-8');
            $dom->preserveWhiteSpace = false; //elimina espaços em branco
            $dom->formatOutput = false;
            // carrega o xml tanto pelo string contento o xml como por um path
            if (is_file($xml)){
                $dom->load($xml,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
            } else {
                $dom->loadXML($xml,LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
            }
            // pega a assinatura
            $Signature = $dom->getElementsByTagName('Signature')->item(0);
            //recupera os erros da libxml
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                //o dado passado como $docXml não é um xml
                $msg = 'O dado informado não é um XML ou não foi encontrado. Você deve passar o conteudo de um arquivo xml assinado como parâmetro.';
                $aError[] = $msg;
                throw new nfephpException($msg);
            }
            if($xsdFile==''){
                if (is_file($xml)){
                    $contents = file_get_contents($xml);
                } else {
                    $contents = $xml;
                }
                $sxml = simplexml_load_string($contents);
                $nome = $sxml->getName();
                $sxml = null;
                //determinar qual o arquivo de schema válido
                //buscar o nome do scheme
                switch ($nome){
                    case 'evento':
                        //obtem o node com a versão
                        $node = $dom->$dom->documentElement;
                        //obtem a versão do layout
                        $ver = trim($node->getAttribute("versao"));
                        $tpEvento = $node->getElementsByTagName('tpEvento')->item(0)->nodeValue;
                        switch ($tpEvento){
                            case '110110':
                                //carta de correção
                                $xsdFile = "CCe_v$ver.xsd";
                                break;
                            default:
                                $xsdFile = "";
                                break;
                        }
                        break;
                    case 'envEvento':
                        //obtem o node com a versão
                        $node = $dom->getElementsByTagName('evento')->item(0);
                        //obtem a versão do layout
                        $ver = trim($node->getAttribute("versao"));
                        $tpEvento = $node->getElementsByTagName('tpEvento')->item(0)->nodeValue;
                        switch ($tpEvento){
                            case '110110':
                                //carta de correção
                                $xsdFile = "envCCe_v$ver.xsd";
                                break;
                            default:
                                $xsdFile = "envEvento_v$ver.xsd";
                                break;
                        }
                        break;
                    case 'NFe':
                        //obtem o node com a versão
                        $node = $dom->getElementsByTagName('infNFe')->item(0);
                        //obtem a versão do layout
                        $ver = trim($node->getAttribute("versao"));
                        $xsdFile = "nfe_v$ver.xsd";
                        break;
                    case 'nfeProc':
                        //obtem o node com a versão
                        $node = $dom->documentElement;
                        //obtem a versão do layout
                        $ver = trim($node->getAttribute("versao"));
                        $xsdFile = "procNFe_v$ver.xsd";
                        break;
                    default:
                        //obtem o node com a versão
                        $node = $dom->documentElement;
                        //obtem a versão do layout
                        $ver = trim($node->getAttribute("versao"));
                        $xsdFile = $nome."_v".$ver.".xsd";
                        break;
                }
                $aFile = $this->listDir($this->xsdDir . $this->schemeVer. DIRECTORY_SEPARATOR,$xsdFile,true);
                if (isset($aFile[0]) && !$aFile[0]) {
                    $msg = "Erro na localização do schema xsd. ";
                    $aError[] = $msg;
                    throw new nfephpException($msg);
                } else {
                    $xsdFile = $aFile[0];
                }
            }
            //limpa erros anteriores
            libxml_clear_errors();
            // valida o xml com o xsd
            if ( !$dom->schemaValidate($xsdFile) ) {
                /**
                 * Se não foi possível validar, você pode capturar
                 * todos os erros em um array
                 * Cada elemento do array $arrayErrors
                 * será um objeto do tipo LibXmlError
                 */
                // carrega os erros em um array
                $aIntErrors = libxml_get_errors();
                $flagOK = false;
                if (!isset($Signature)){
                    // remove o erro de falta de assinatura
                    foreach ($aIntErrors as $k=>$intError){
                        if(strpos($intError->message,'( {http://www.w3.org/2000/09/xmldsig#}Signature )')!==false){
                            // remove o erro da assinatura, se tiver outro meio melhor (atravez dos erros de codigo) e alguem souber como tratar por eles, por favor contribua...
                            unset($aIntErrors[$k]);
                        }
                    }
                    reset($aIntErrors);
                    $flagOK = true;
                }//fim teste Signature
                $msg = '';
                foreach ($aIntErrors as $intError){
                    $flagOK = false;
                    $en = array("{http://www.portalfiscal.inf.br/nfe}"
                                ,"[facet 'pattern']"
                                ,"The value"
                                ,"is not accepted by the pattern"
                                ,"has a length of"
                                ,"[facet 'minLength']"
                                ,"this underruns the allowed minimum length of"
                                ,"[facet 'maxLength']"
                                ,"this exceeds the allowed maximum length of"
                                ,"Element"
                                ,"attribute"
                                ,"is not a valid value of the local atomic type"
                                ,"is not a valid value of the atomic type"
                                ,"Missing child element(s). Expected is"
                                ,"The document has no document element"
                                ,"[facet 'enumeration']"
                                ,"one of"
                                ,"failed to load external entity"
                                ,"Failed to locate the main schema resource at"
                                ,"This element is not expected. Expected is"
                                ,"is not an element of the set");
                    $pt = array(""
                                ,"[Erro 'Layout']"
                                ,"O valor"
                                ,"não é aceito para o padrão."
                                ,"tem o tamanho"
                                ,"[Erro 'Tam. Min']"
                                ,"deve ter o tamanho mínimo de"
                                ,"[Erro 'Tam. Max']"
                                ,"Tamanho máximo permitido"
                                ,"Elemento"
                                ,"Atributo"
                                ,"não é um valor válido"
                                ,"não é um valor válido"
                                ,"Elemento filho faltando. Era esperado"
                                ,"Falta uma tag no documento"
                                ,"[Erro 'Conteúdo']"
                                ,"um de"
                                ,"falha ao carregar entidade externa"
                                ,"Falha ao tentar localizar o schema principal em"
                                ,"Este elemento não é esperado. Esperado é"
                                ,"não é um dos seguintes possiveis");
                    switch ($intError->level) {
                        case LIBXML_ERR_WARNING:
                            $aError[] = " Atençao $intError->code: " . str_replace($en,$pt,$intError->message);
                            break;
                        case LIBXML_ERR_ERROR:
                            $aError[] = " Erro $intError->code: " . str_replace($en,$pt,$intError->message);
                            break;
                        case LIBXML_ERR_FATAL:
                            $aError[] = " Erro Fatal $intError->code: " . str_replace($en,$pt,$intError->message);
                            break;
                    }
                    $msg .= str_replace($en,$pt,$intError->message);
                }
            } else {
                $flagOK = true;
            }
            if(!$flagOK){
                throw new nfephpException($msg);
            }
        } catch (nfephpException $e) {
            $this->__setError($e->getMessage());
            if ($this->exceptions) {
                throw $e;
            }
            return false;
        }
        return true;
    } //fim validXML


    /** NÂO USADO
     * Valida o arquivo xml criado com o schema valido
     *
     * @param string xml contendo os dados da nota
     * @return string xml validado
     **/
    public static function valida($arquivo){
    	if(!defined('sugarEntry'))define('sugarEntry', true);

		libxml_use_internal_errors(true);

		/* Cria um novo objeto da classe DomDocument */
		$objDom = new DomDocument('1.0','utf-8');

		echo $objDom->xmlEncoding;
		/* Carrega o arquivo XML */
		$arquivoAValidar = $arquivo;// "XMLS_teste/copianovaNFE2.xml";

		$objDom->load($arquivoAValidar);

		/* Tenta validar os dados utilizando o arquivo XSD */
		if (!$objDom->schemaValidate("schema_XML/PL_006e/nfe_v2.00.xsd")) {
		    $arrayAllErrors = libxml_get_errors();
		    /* Cada elemento do array $arrayAllErrors ser� um objeto do tipo LibXmlError */
		    echo "<br> XML <b>: ".$arquivoAValidar."</b><br>";
		    echo "<br> n&uacute;mero de Erros:".count($arrayAllErrors)."<br>";
		    for($i=0; $i<count($arrayAllErrors); $i++){
		             print_r_pre($arrayAllErrors[$i]);
		    }

		} else {
		    /* XML validado! */
		    echo "XML <b>".$arquivoAValidar."</b> obedece as regras definidas no arquivo XSD!";
		}
    }

    /** NÂO USADO
     * __montaChaveXMLNFE
     * Monta a chave da NFe dentro do documento xml
     *
     * @return boolean inndicando se deu tudo certo
     */
    public static function __montaChaveXMLNFE(& $dom){
		$ide    = $dom->getElementsByTagName("ide")->item(0);
		if(empty($ide))		return("'ide' não encontrado");
		$emit   = $dom->getElementsByTagName("emit")->item(0);
		if(empty($emit))	return("'emit' não encontrado");
		$cUF    = $ide->getElementsByTagName('cUF')->item(0);
		if(empty($cUF))		return("'cUF' não encontrado");		$cUF = $cUF->nodeValue;
		$dEmi   = $ide->getElementsByTagName('dEmi')->item(0);
		if(empty($dEmi))	return("'dEmi' não encontrado");	$dEmi = $dEmi->nodeValue;
		$CNPJ   = $emit->getElementsByTagName('CNPJ')->item(0);
		if(empty($CNPJ))	return("'CNPJ' não encontrado");	$CNPJ = $CNPJ->nodeValue;
		$mod    = $ide->getElementsByTagName('mod')->item(0);
		if(empty($mod))		return("'mod' não encontrado");		$mod = $mod->nodeValue;
		$serie  = $ide->getElementsByTagName('serie')->item(0);
		if(empty($serie))	return("'serie' não encontrado");	$serie = $serie->nodeValue;
		$nNF    = $ide->getElementsByTagName('nNF')->item(0);
		if(empty($nNF))		return("'nNF' não encontrado");		$nNF = $nNF->nodeValue;
		$tpEmis = $ide->getElementsByTagName('tpEmis')->item(0);
		if(empty($tpEmis))	return("'tpEmis' não encontrado");	$tpEmis = $tpEmis->nodeValue;
		$cNF    = $ide->getElementsByTagName('cNF')->item(0);
		if(empty($cNF))		return("'cNF' não encontrado");		$cNF = $cNF->nodeValue;
		$cDV    = $ide->getElementsByTagName('cDV')->item(0);
		if(empty($cDV))		return("'cDV' não encontrado");


		if( strlen($cNF) != 8 ){	// gera o numero aleatório
			$cNF = $ide->getElementsByTagName('cNF')->item(0)->nodeValue = rand( 0 , 99999999 );
		}
		$tempData = explode("-", $dEmi);
		if(!isset($tempData[0]))	$tempData[0]=0;
		if(!isset($tempData[1]))	$tempData[1]=0;

		$CNPJ = preg_replace("/[^0-9]/", "", $CNPJ);
		$tempChave =	substr(str_pad(abs((int)$cUF			), 2,'0',STR_PAD_LEFT),0, 2).
				substr(str_pad(abs((int)$tempData[0] - 2000 	), 2,'0',STR_PAD_LEFT),0, 2).
				substr(str_pad(abs((int)$tempData[1] 		), 2,'0',STR_PAD_LEFT),0, 2).
				substr(str_pad($CNPJ 				 ,14,'0',STR_PAD_LEFT),0,14).
				substr(str_pad(abs((int)$mod 			), 2,'0',STR_PAD_LEFT),0, 2).
				substr(str_pad(abs((int)$serie 			), 3,'0',STR_PAD_LEFT),0, 3).
				substr(str_pad(abs((int)$nNF 			), 9,'0',STR_PAD_LEFT),0, 9).
				substr(str_pad(abs((int)$tpEmis 		), 1,'0',STR_PAD_LEFT),0, 1).
				substr(str_pad(abs((int)$cNF			), 8,'0',STR_PAD_LEFT),0, 8);
		//		00.20,00.00000000000000,00.000,000000000.0.18641952.6
		//$forma = 	"%02d%02d%02d%s%02d%03d%09d%01d%08d";//%01d";
		$cDV    = $ide->getElementsByTagName('cDV')->item(0)->nodeValue  = self::calculaDV($tempChave);
		$chave  = $tempChave .= $cDV;
		$infNFe = $dom->getElementsByTagName("infNFe")->item(0);
		if(empty($infNFe))		return("'infNFe' não encontrado");
		$infNFe->setAttribute("Id", "NFe" . $chave);
		return(true);
	} //fim __calculaChave





    // ***********************************************************************************************************************************************************
    // 				DOCUMENTOS (CNPJ, CPF, etc)
    // ***********************************************************************************************************************************************************

    /**
     * Verifica se um CNPJ é válido
     *
     * @param  string $_cnpj CNPJ
     * @return boolean
     */
    public static function isCnpj($_cnpj) {
        $valid = true;
        $_cnpj = str_pad(self::unmask($_cnpj), 14, '0', STR_PAD_LEFT);

        for ($x = 0; $x < 10; $x++) {
            if ($_cnpj == str_repeat($x, 14)) {
                $valid = false;
            }
        }

        if ($valid) {
            if (strlen($_cnpj) != 14) {
                $valid = false;
            } else {
                for ($t = 12; $t < 14; $t ++) {
                    $d = 0;
                    $c = 0;
                    for ($m = $t - 7; $m >= 2; $m --, $c ++) {
                        $d += $_cnpj {$c} * $m;
                    }
                    for ($m = 9; $m >= 2; $m --, $c ++) {
                        $d += $_cnpj {$c} * $m;
                    }
                    $d = ((10 * $d) % 11) % 10;
                    if ($_cnpj {$c} != $d) {
                        $valid = false;
                        break;
                    }
                }
            }
        }

        return $valid;
    }

    /**
     * Verifica se um CPF é válido
     *
     * @param  string $_cpf CPF
     * @return boolean
     */
    public static function isCpf($_cpf) {
        $valid = true;
        $_cpf = str_pad(self::unmask($_cpf), 11, '0', STR_PAD_LEFT);

        for ($x = 0; $x < 10; $x ++) {
            if ($_cpf == str_repeat($x, 11)) {
                $valid = false;
            }
        }

        if ($valid) {
            if (strlen($_cpf) != 11) {
                $valid = false;
            } else {
                for ($t = 9; $t < 11; $t ++) {
                    $d = 0;
                    for ($c = 0; $c < $t; $c ++) {
                        $d += $_cpf {$c} * (($t + 1) - $c);
                    }
                    $d = ((10 * $d) % 11) % 10;
                    if ($_cpf{$c} != $d) {
                        $valid = false;
                        break;
                    }
                }
            }
        }

        return $valid;
    }




    // ***********************************************************************************************************************************************************
    // 				DATE HORA
    // ***********************************************************************************************************************************************************

    /**
    * __convertTime
    * Converte o campo data time retornado pelo webservice
    * em um timestamp unix
    * Exemplo de string
    *					Recebe => 2016-12-15T15:21:14-02:00
    *			 Converte para => 15/12/2016 15:21:14
    *
    * @name __convertTime
    * @param    string   $DH  // 2016-12-15T15:21:14-02:00
    * @return   timestamp     // 15-12-2016 15:21:14
    **/
    public static function __convertTime( $DH ){
        if ($DH) {
            $aDH = explode('T', $DH);  //[0]2016-12-15 [1]09:14:14-02:00
            $adDH = explode('-', $aDH[0]);
            $atimeDH = explode('-', $aDH[1]); // para tirar o fuso horario fora
            $atDH = explode(':', $atimeDH[0]);
                                   // hora      min.,    seg.,     mês,       dia,     ano
            $timestampDH = mktime($atDH[0], $atDH[1], $atDH[2], $adDH[1], $adDH[2], $adDH[0]);

            return strftime("%d-%m-%Y %H:%M:%S ", $timestampDH);
        }
    } //fim __convertTime

    /**
    * convertTimeZone
    * Converte o campo data time para um padrAo Time Zone UTC
    *
    * Exemplo de string
    *                   Recebe => 15/12/2016 15:21:14
    *            Converte para => 2016-12-15T15:21:14-02:00
    *
    * @name convertTimeZone
    * @param    string $fuso   // '-02:00'
    * @param    string $UF      // 'RS'
    * @param    timestamp $DH   // 15/12/2016 15:21:14
    * @return   string          // 2016-12-15T15:21:14-02:00
    **/
    public static function convertTimeZone($DH, $UF, $fuso = "ola mundo"){

        $time_from = self::getTimezone( $UF );
        $time_fuso = $fuso;

        $time_to = 'UTC';
        // Formato NFe = 'AAAA-MM-DDThh:mm:ssTZD';
        $time_format = 'Y-m-d\TH:i:sP';

        $dt_convert = new DateTime();

        $dt_convert = DateTime::createFromFormat('d/m/Y H:i:s', $DH, new DateTimeZone($time_from));
        $DH = $dt_convert->format($time_format);

        return $DH;
    }

    /**
    * __convertStringToTime -- FUNCIONA
    * Converte o campo data time retornado pelo webservice
    * em um timestamp unix
    * Exemplo de string
    *                   Recebe => 2016-12-15T15:21:14-02:00
    *            Converte para => 15/12/2016 15:21:14
    *
    * @name __convertStringToTime
    * @param    string   $servDH  // Data a ser formatada -> exemplo: 2016-12-15T15:21:14-02:00
    * @param    string   $format  // formato a ser aplicado -> exemplo: d-m-Y H:i:s
    * @return   timestamp     // 15-12-2016 15:21:14
    **/
    public static function __convertStringToTime( $servDH, $format ){
        $result_date = '';

        if ($servDH) {
            $result_date = DateTime::createFromFormat('Y-m-d\TH:i:sP', $servDH)->format($format);
            return $result_date;
        }
    } //fim __convertStringToTime

    /**
     * isHorarioVerao
     * Verifica se estamos no horário de verão *** depende da configuração do servidor ***
     *
     * @return boolean $valor indicando se estamos no horário de verão
     */
    public static function isHorarioVerao($UF){
        //definir o timezone default para o estado do emitente
        $timezone = self::getTimezone($UF);
        date_default_timezone_set($timezone);
        //estados que participam do horario de verão
        $aUFhv = array('ES','GO','MG','MS','PR','RJ','RS','SP','SC');
        //corrigir o timeZone
        if ($this->UF == 'AC' ||
            $this->UF == 'AM' ||
            $this->UF == 'MT' ||
            $this->UF == 'MS' ||
            $this->UF == 'RO' ||
            $this->UF == 'RR' ) {
            $this->timeZone = '-04:00';
        }
        //verificar se estamos no horário de verão *** depende da configuração do servidor ***
        if (date('I') == 1) {
            //estamos no horario de verão verificar se o estado está incluso
            if (in_array($UF, $aUFhv)) {
                $itz = (int) $timezone;
                $itz++;
                $timezone = '-'.sprintf("%02d",abs($itz)).':00'; //poderia ser obtido com date('P')
            }
        }//fim check horario verao
        return true;
    }

    /**
     * tzUFlist
     * Retorna a TimeZone do Estado
     * @var array
     */
    public static function getTimezone($UF) {

        /**
         * tzUFlist
         * Lista das zonas de tempo para os estados brasileiros
         */
        $tzUFlist = array('AC'=>'America/Rio_Branco',
                              'AL'=>'America/Sao_Paulo',
                              'AM'=>'America/Manaus',
                              'AP'=>'America/Sao_Paulo',
                              'BA'=>'America/Bahia',
                              'CE'=>'America/Fortaleza',
                              'DF'=>'America/Sao_Paulo',
                              'ES'=>'America/Sao_Paulo',
                              'GO'=>'America/Sao_Paulo',
                              'MA'=>'America/Sao_Paulo',
                              'MG'=>'America/Sao_Paulo',
                              'MS'=>'America/Campo_Grande',
                              'MT'=>'America/Cuiaba',
                              'PA'=>'America/Belem',
                              'PB'=>'America/Sao_Paulo',
                              'PE'=>'America/Recife',
                              'PI'=>'America/Sao_Paulo',
                              'PR'=>'America/Sao_Paulo',
                              'RJ'=>'America/Sao_Paulo',
                              'RN'=>'America/Sao_Paulo',
                              'RO'=>'America/Porto_Velho',
                              'RR'=>'America/Boa_Vista',
                              'RS'=>'America/Sao_Paulo',
                              'SC'=>'America/Sao_Paulo',
                              'SE'=>'America/Sao_Paulo',
                              'SP'=>'America/Sao_Paulo',
                              'TO'=>'America/Sao_Paulo');

        return $tzUFlist[$UF];
    }



    // ***********************************************************************************************************************************************************
    //              REQUEST
    // ***********************************************************************************************************************************************************

    /**
     * Chama API externa (GET, PUT, POST)
     *
     * @param $uri
     * @param array $data
     * @param array $headers
     *
     * @return array
     */
    public static function requestAPI($method, $url, $data = false)//$uri, array $data = [], array $headers = [])
    {
        $curl = curl_init();

        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                if($data){
                    curl_setopt($curls, CURLOPT_POSTFIELDS, $data);
                }
                break;

            case 'PUT':
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;

            default:
                if($data){
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
                break;
        }

        // curl_setopt_array($curl, [
        //     CURLOPT_URL            => $url,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_HTTPHEADER => $headers, ??
        //     CURLOPT_HEADER => 1,
        //     CURLOPT_SSL_VERIFYPEER => false,
        //     CURLOPT_SSL_VERIFYHOST => false,
        //     CURLOPT_FOLLOWLOCATION => true
        // ]);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10 );  // timeout on connect
        curl_setopt($curl, CURLOPT_TIMEOUT, 10 );  // timeout on response
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($curl);

        // $size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        /* Verifica se retornou com erro retorna null */
        if ($httpCode >= 200 && $httpCode < 300) {
            return $response;;
        } else {
            return null;
        }



        // $headers = [];

        // foreach (explode(PHP_EOL, substr($response, 0, $size)) as $i)
        // {
        //     $t = explode(':', $i, 2);
        //     if(isset($t[1]))
        //         $headers[trim($t[0])] = trim($t[1]);
        // }

        // $response = substr($response, $size);

        // return compact('response', 'headers');
    }

}
