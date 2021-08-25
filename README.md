# Librarie Configurações

Uma librarie para CodeIgniter 4 com a função de consultar o microserviço de configuracoes.


## Requeriementos

Requer **CodeIgniter 4**.


## Instalação

composer require onovaes/config-lib


### Variaveis de Configuracoes

API_CONF_URL='http://ms08_nginx/'

API_CONF_KEY=''


## Usage

$configuracoes = new \Onovaes\ConfigLib\Configuracoes();

$configuracoes->get('DN_SITE_URL');


## Support
If you think you've found a bug please [Create an Issue](https://github.com/onovaes/config-lib/issues).
