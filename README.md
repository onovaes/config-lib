# ConfigLib

O ConfigLib é uma librarie para fazer consultas ao microserviço de configurações

Desenvolvida para **CodeIgniter 4**.


## Instalação
```shell
composer require onovaes/config-lib
```

#### Variaveis (.env) de Configurações

```
API_CONF_URL='http://ms08_nginx/'

API_CONF_KEY=''
```

## Utilizando a Lib
```php
$configuracoes = new \Onovaes\ConfigLib\Configuracoes();

$configuracoes->get('DN_SITE_URL');
```

## Desenvolver para a ConfigLib
Para modo de desenvolvimento você pode usar o repositório diretamente no diretorio do seu projeto:

```shell
my-project$ git clone git@github.com:onovaes/config-lib.git
```
Adicione em Config/Autoload.php, a liinha seguir (PSR-4) :
```php
'Onovaes\\ConfigLib\\'      => ROOTPATH . 'config-lib/Libraries',
```


## Suporte
Se você pensa que achou um erro [crie um Issue](https://github.com/onovaes/config-lib/issues).