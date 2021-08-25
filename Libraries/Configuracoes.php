<?php namespace App\Libraries;


/**
 * A Librarie Configuracões é feita para obter as configuracoes de um Site junto ao microservico de configuracoes
 * 
 * @author Gabriel Novaes <gabriel@dothcom.net>
 * 
 */
class Configuracoes
{
 
	/**
	 * String com o nome/identificador do cache
	 *
	 * @var string
	 */
	protected $cache_name = 'query_configuracao';

	/**
	 * Int com tempo sem segundos de expiração do cache 
	 * 
	 * @var int
	 */
	protected $cache_configuracoes_timeout = 300;

	/**
	 * URL de acesso ao microserviço de configuracao.
	 * Padrao em produção http://ms08_nginx/
	 * 
	 * @var string
	 */
	protected $api_conf_url;

	/**
	 * String com a chave de acesso ao microservico
	 * Em producao essa chave não é usada pois os microservicos conversam entre si, sem passar pelo kong
	 * 
	 * @var string
	 */
	protected $api_conf_key;




    // -------------------------------------------------------------------



    /**
     * __construct ()
     *
     */
    public function __construct()
    {
		$this->api_conf_url = env('API_CONF_URL');
		$this->api_conf_key = env('API_CONF_KEY');
    }



   	/**
	 * 
	 * Registra no REDIS/CACHE SOMENTE os resutados de 'configuracao' e 'valor' num array
	 * 
	 * @return void
	 */
	private function registraConfiguracoesEmCache()
	{
		$cache_item_id = $this->getCacheName($this->cache_name);
		$confs = cache()->get($cache_item_id);

		if (empty($confs))
		{
			$configuracoes = $this->consultaApiConfiguracoes();
			$configuracoes = json_decode($configuracoes, true);
			//dd($configuracoes['resultado']);

			$retorno = array();
			foreach ($configuracoes['resultado'] as $conf)
				$retorno[ $conf['configuracao'] ] = $conf['valor'];

			cache()->save($cache_item_id, $retorno , $this->cache_configuracoes_timeout );
		}
	}
    


    /**
	 * 
	 * Método principal da librarie responsavel por retorna uma determinada configuração
	 *
	 * @author Shiguenori Junior <junior@dothcom.net
	 * 
	 * @return string
	 */
	public function get($nome_config='DN_SITE_URL')
	{
		$cache_item_id = $this->getCacheName($this->cache_name);
		$configuracoes = cache()->get($cache_item_id);

		if(empty($configuracoes))
			$this->registraConfiguracoesEmCache();

		$configuracoes = cache()->get($cache_item_id);

		if(empty($configuracoes))
			throw new \Exception("Parece não haver nenhuma Configuracao Cadastrada");

		if (!isset($configuracoes[$nome_config]))
			throw new \Exception("Configuracao ".$nome_config. " não encontrada ");

		return $configuracoes[$nome_config];
	}


    
    /**
	 * Gera um nome/identificador unico para a KEY do redis, com base nos parametros recebidos
	 * Por questões de perfomance e boas práticas no REDIS o nome da usara ":" para separação
	 * 
	 * @author Gabriel Novaes <gabriel@dothcom.net>
	 * 
	 * @param string $base - nome base do cache
	 * 
	 * @return string localhost:query_configuracao	  
	 */
	private function getCacheName($base='edicaoimpressa')
	{
		$separador = '__';
		$host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : 'localhost';
		$remover = array('[', ']', '[', ']', '{', '}', '"', "'");
		$retorno = $host.$separador.$base;
		return str_replace(array(',', ':'), $separador, $retorno);
	}



    /**
	 * Consulta dados no microserviço de configuracoes (MS08)
	 * 
	 * @return json - json com status, mensagem e resultado da requisicao
	 */
	private function consultaApiConfiguracoes()
	{
		$curl = \Config\Services::curlrequest();

		$opcoes = [];
		$opcoes['timeout'] = 5;
		$opcoes['connect_timeout'] = 3;
		$opcoes['http_errors'] = FALSE;
		$opcoes['headers'] = ['apiKey'=> $this->api_conf_key , 'content-type' => 'application/json'];

		$response = $curl->request('GET', $this->api_conf_url , $opcoes);
		$code = $response->getStatusCode();
		$body = $response->getBody();
		$contentType = $response->getHeader('Content-Type');

		if (!isset($body))
			throw new \Exception("Nao retornou nada ao consultar api de ocnfiguracos");

		return $body;
	}
    // -------------------------------------------------------------------

}   // End of Name Library Class.

/**
 * -----------------------------------------------------------------------
 * Filename: ConfiguracoesLibrary.php
 * Location: ./app/Libraries/ConfiguracoesLibrary.php
 * -----------------------------------------------------------------------
 */ 