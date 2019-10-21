<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Deputado;
use App\VerbasIndenizatoria;
use App\RedeSocialDeputado;

class DeputadoController extends Controller
{
    // URLs webservice
    const URL_DEPUTADOS             = "http://dadosabertos.almg.gov.br/ws/deputados/situacao/";
    const URL_VERBAS_INDENIZATORIAS = "http://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/legislatura_atual/deputados/{deputado_id}/datas";
    const URL_VERBA_POR_DATA        = "http://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{deputado_id}/{ano}/{mes}";
    const URL_CONTATO_DEPUTADO      = "http://dadosabertos.almg.gov.br/ws/deputados/lista_telefonica";

    // Situação do Deputado
    const DEPUTADO_EXERCICIO       = 1;
    const DEPUTADO_EXERCEU_MANDATO = 2;
    const DEPUTADO_RENUNCIOU       = 3;
    const DEPUTADO_AFASTADO        = 4;
    const DEPUTADO_PERDEU_MANDATO  = 5;

    protected $output_format = "?formato=json";

    /*
    * Retorna a lista de deputados ordenados por reembolsos de verbas indenizatorias naquele mês
    *
    * @param Illuminate\Http\Request Request - Requisição 
    * @param $mes - Mês a ser filtrado
    **/
    public function getDeputadosOrdenadosPorVerba(Request $request, $mes) {
        if($request->has('page')) {
            $page = $request->input('page');
        }
        $deputados = Deputado::getDeputadosOrdenadosPorVerba($mes, $page, 5);
        return $deputados;
    }

    /*
    * Retorna a lista das redes sociais mais utilizadas pelos deputados
    *
    * @param Illuminate\Http\Request Request - Requisição 
    **/
    public function getRedesSociaisMaisUtilizadas(Request $request) {
        if($request->has('page')) {
            $page = $request->input('page');
        }
        $deputados = RedeSocialDeputado::getRedesSociaisMaisUtilizadas($page, 10);
        return $deputados;
    }
    
    /*
    * Busca os dados dos deputados a partir da api: http://dadosabertos.almg.gov.br/ws/ajuda/
    * e insere no banco de dados
    **/
    public function preencherDados() {

        set_time_limit(0);

        VerbasIndenizatoria::query()->delete();
        RedeSocialDeputado::query()->delete();
        Deputado::query()->delete();

        $deputadosExercicio = $this->curl(self::URL_DEPUTADOS . self::DEPUTADO_EXERCICIO . $this->output_format, 'GET');
        if(isset($deputadosExercicio["list"])) {
            // Insere os deputados em exercicios (Situação = 1)
            $this->insereDeputados($deputadosExercicio["list"], self::DEPUTADO_EXERCICIO);
        }

        $deputadosExerceramMandato = $this->curl(self::URL_DEPUTADOS . self::DEPUTADO_EXERCEU_MANDATO . $this->output_format, 'GET');
        if(isset($deputadosExerceramMandato["list"])) {
            // Insere os deputados que exerceram mandato (Situação = 2)
            $this->insereDeputados($deputadosExerceramMandato["list"], self::DEPUTADO_EXERCEU_MANDATO);
        }

        $deputadosPerderamMandato = $this->curl(self::URL_DEPUTADOS . self::DEPUTADO_RENUNCIOU . $this->output_format, 'GET');
        if(isset($deputadosPerderamMandato["list"])) {
            // Insere os deputados que renunciaram o mandato (Situação = 3)
            $this->insereDeputados($deputadosPerderamMandato["list"], self::DEPUTADO_RENUNCIOU);
        }

        $deputadosAfastados = $this->curl(self::URL_DEPUTADOS . self::DEPUTADO_AFASTADO . $this->output_format, 'GET');
        if(isset($deputadosAfastados["list"])) {
            // Insere os deputados que foram afastados (Situação = 4)
            $this->insereDeputados($deputadosAfastados["list"], self::DEPUTADO_AFASTADO);
        }

        $deputadosRenunciaram = $this->curl(self::URL_DEPUTADOS . self::DEPUTADO_PERDEU_MANDATO . $this->output_format, 'GET');
        if(isset($deputadosRenunciaram["list"])) {
            // Insere os deputados que perderam o mandato (Situação = 5)
            $this->insereDeputados($deputadosRenunciaram["list"], self::DEPUTADO_PERDEU_MANDATO);
        }

        $this->buscaRedesSociais();
    }

    /*
    * Insere varios deputados a partir de um array 
    *
    * @param $deputados - Array de deputados a serem inseridos
    * @param $situacao  - Situação dos deputados ( 1 - Em exercicio, 2 - Exerceu Mandato, 3 - Perdeu o Mandato, 4 - Afastado, 5 - Renunciou)
    **/
    private function insereDeputados(array $deputados, $situacao) {
        foreach($deputados as $deputado) {
            // Define a situação do deputado como em exercicio
            $deputado["situacao"]    = $situacao;
            $deputado["localizacao"] = $deputado["tagLocalizacao"];
            // Insere o deputado
            $novoDeputado = new Deputado($deputado);
            $novoDeputado->save();
            $this->buscarVerbasIndenizatoriasDeputado($deputado["id"]);
        }
    }

    /*
    * Busca os dados de contato 
    **/
    private function buscaRedesSociais() {
        $dadosContato = $this->curl(self::URL_CONTATO_DEPUTADO . $this->output_format, 'GET');
        if($dadosContato["list"]) {
            $dadosContatoDeputados = $dadosContato["list"];
            // Percorre a lista de contatos de todos os deputados
            foreach($dadosContatoDeputados as $dadosContatoDeputado) {
                $novaRedeSocial = array(
                    "deputado_id" => $dadosContatoDeputado["id"]
                );
                // Percorre os caontatos de um deputado especifico
                foreach($dadosContatoDeputado["redesSociais"] as $redeSocial) {
                    try{
                        $novaRedeSocial["rede_id"] = $redeSocial["redeSocial"]["id"];
                        $novaRedeSocial["nome"] = $redeSocial["redeSocial"]["nome"];
                        $novaRedeSocial["link"] = $redeSocial["url"];
                        $novaRede = new RedeSocialDeputado($novaRedeSocial);
                        $novaRede->save();
                    }
                    catch(\Exception $e){}
                } 
            }
        }
    }

    /*
    * Busca as verbas indenizatorias de um deputado específico.
    * ( Como nem todos os deputados possuem verbas indenizatorias utilizar
    *   o endpoint que lista a data das verbas reduz o número de consultas ).
    *
    * @param $deputado_id - ID do deputado do qual serão buscadas as verbas 
    **/
    private function buscarVerbasIndenizatoriasDeputado($deputado_id) {

        $url = str_replace("{deputado_id}", $deputado_id, self::URL_VERBAS_INDENIZATORIAS);
        $datas_verbas = $this->curl($url. $this->output_format, 'GET');
        $meses = array();
        if(isset($datas_verbas["list"])) {
            $datas_verbas = $datas_verbas["list"];
            foreach($datas_verbas as $data_verba) {
                $data = $data_verba["dataReferencia"]["$"];
                $data = new \DateTime($data);
                $ano = $data->format('Y');
                if($ano == '2019') {
                    $mes = $data->format('m');
                    $meses[$mes] = $mes;
                }
            }
        }
        
        foreach($meses as $mes) {
            $this->buscarVerbasIndenizatoriasDeputadoData($deputado_id, '2019', $mes);
        }

    }

    /*
    * Busca as verbas indenizatorias de um deputado específico por ano/mes.
    *
    * @param $deputado_id - ID do deputado do qual serão buscadas as verbas 
    * @param $ano         - Ano da verba
    * @param $mes         - Mês da verba
    **/
    private function buscarVerbasIndenizatoriasDeputadoData($deputado_id, $ano, $mes) {
        $url = str_replace(
                    array("{deputado_id}", "{ano}", "{mes}"), 
                    array($deputado_id, $ano, $mes), 
                    self::URL_VERBA_POR_DATA);
        

        $verbas = $this->curl($url. $this->output_format, 'GET');
        if(isset($verbas["list"])) {
            $verbas = $verbas["list"];
        } 
        if($verbas) {
            foreach($verbas as $verba) {
                foreach($verba["listaDetalheVerba"] as $detalheVerba) {
                    $dataReferencia = new \DateTime($detalheVerba["dataReferencia"]["$"]);
                    $dataEmissao    = new \DateTime($detalheVerba["dataEmissao"]["$"]);
    
                    $novaVerba = array(
                        "deputado_id" => $detalheVerba["idDeputado"],
                        "descricao"   => $detalheVerba["descTipoDespesa"],
                        "emitente"    => $detalheVerba["nomeEmitente"],
                        "data_referencia"   => $dataReferencia,
                        "data_emissao"      => $dataEmissao,
                        "documento"         => isset($detalheVerba["descDocumento"]) ? $detalheVerba["descDocumento"] : '',
                        "valor_reembolso"   => $detalheVerba["valorReembolsado"],
                        "valor_despesa"     => $detalheVerba["valorDespesa"]
                    );
                    
                    $novaVerba = new VerbasIndenizatoria($novaVerba);
                    $novaVerba->save(); 
                }
            }
        }
    }

    /*
    * Envia uma requisição do tipo GET ou POST para a url recebida como parâmetro
    *
    * @param $url  - Url para qual será feita a requisição
    * @param $type - Tipo da requisição ( 'GET' ou 'POST' )
    * @param $postFields - Dados enviados em requisições POST ( array ou string )
    **/
    private function curl($url, $type = 'GET', $postFields = '') {

        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_HEADER, TRUE); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        if($type == 'POST') {
            
            if(gettype($postFields) == "array") {
                $postFields = json_encode($postFields);
            }

            curl_setopt($ch, CURLOPT_POST, 1); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postFields))
            );
        }

        $response = curl_exec($ch); 
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        
        return json_decode($body, true); 
    }
}
