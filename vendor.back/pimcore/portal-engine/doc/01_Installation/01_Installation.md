# Installation of Portal Engine

The installation of the portal engine follows standard composer procedures. 
There are a couple of things to consider though.

### Prerequisites 

#### System Requirements
Following requirements additionally to Pimcore apply:
- PHP >= 7.4
- Pimcore >= 6.9
- [Elasticsearch 6](https://www.elastic.co/guide/en/elasticsearch/reference/6.8/index.html) or 
  [Elasticsearch 7](https://www.elastic.co/guide/en/elasticsearch/reference/7.10/index.html)
- [Mercure](https://mercure.rocks/docs/hub/install) (optional for Pimcore Direct Edit)
- MySQL/MariaDB innodb_default_row_format option needs to be set to "DYNAMIC" (which is the default value)

#### Required Bundles
The portal engine requires a couple of bundles which need be configured and installed first: 
- [Enterprise Metadata Bundle](https://pimcore.com/docs/asset-metadata/current/Installation/index.html)
- [Statistics Explorer](https://pimcore.com/docs/statistics-explorer/current/Installation_and_Configuration/index.html)
- [Direct Edit](https://pimcore.com/docs/direct-edit/current/Installation.html) (optional)

#### Required Parameter
Furthermore, before the portal engine installation is possible, the following parameter has to be configured in the system: 

- `pimcore_portal_engine.elasticsearch.host`: Host of your Elasticsearch cluster
- `pimcore_portal_engine.elasticsearch.index_prefix`: Prefix for all index names created by portal engine


### Installation
As soon as all prerequisites are met, installation follows standard composer and Pimcore procedure. 

```bash
composer require pimcore/portal-engine
./bin/console pimcore:bundle:enable PimcorePortalEngineBundle
./bin/console pimcore:bundle:install PimcorePortalEngineBundle
```

#### Configure Symfony firewall
To configure the symfony firewall for the portals, add following line to your firewalls configuration
in the `security.yml` of your app after the `pimcore_admin` firewall. 

```yml 
security:
    firewalls:
        pimcore_admin: 
            # ...
        portal_engine: '%pimcore_portal_engine.firewall_settings%'
```
Of course, it is also possible to customize the firewall settings for the portals by defining your custom settings instead
of using the provided parameter. But, this is not recommended and might break in future versions of the portal engine 
(because the portal engine does changes at the firewall configuration)!   


#### Commands after Installation

It is recommended to run following commands after installation (at least) once:
```bash
./bin/console portal-engine:update:index-recreate
./bin/console portal-engine:update:process-index-queue --processes=5
```

### Followup Configuration
For running the portal engine need following commands up and running permanently or on regular basis.

#### Index Updating Queue Cronjob
Add the following cronjob to your crontab.

```
# it is ok to run it once a minute as it will be locked by a Symfony lock
* * * * * php /path/to/your/app/bin/console portal-engine:update:process-index-queue

# if needed it is possible to execute this with multiple parallel processes too
* * * * * php /path/to/your/app/bin/console portal-engine:update:process-index-queue --processes=4
```

#### Messaging Queue
Setup the Symfony messenger messaging queue like described in the 
[Background Processes](../05_Administration_of_Portals/12_Background_Processes.md) chapter.


#### (optional) Configuring ES client params (like basic auth)

For ES communication the official [ES PHP client library is used](https://github.com/elastic/elasticsearch-php). 
The portal engines `EsClientFactory` uses the `ClientBuilder` to build a client and configure it. 
Host and logger are configured automatically. In addition to that, it passes a `connection_params` array 
that can be set in `pimcore_portal_engine` configuration tree and allows to configure additional connection settings for the client
(example see below).

Thus, it is for example possible to configure for example basic authentication, api key, additional headers and more. 
For details see `ClientBuilder` docs and [implementation](https://github.com/elastic/elasticsearch-php/blob/master/src/Elasticsearch/ClientBuilder.php).

##### Setup Basic Auth
To 
```yml
pimcore_portal_engine:
    index_service:
        es_client_params:
            connection_params:
                client:
                    curl:
                        !php/const CURLOPT_HTTPAUTH: !php/const CURLAUTH_BASIC
                        !php/const CURLOPT_USERPWD: 'my_user:my_password'
```


#### (optional) Multi-Node setup

When setting up the portal engine in a multi node setup, please first follow Pimcore instructions for multi-node setup 
and shared storages. 

In addition to the Pimcore instructions also consider `/var/tmp/portal-engine/downloads` which needs to be a shared tmp
folder and can be configured via flysystem configuration:

```yml
flysystem:
    storages:
        pimcore.portalEngine.download.storage:
            adapter: 'local'
            visibility: private
            options:
                directory: '%kernel.project_dir%/var/tmp/portal-engine/downloads'
```


##### Style Settings Feature
If you are using the [style settings feature](../05_Administration_of_Portals/05_Configuration/30_Styling_Settings_and_Frontend_Build.md)
the location `public/var/portal-engine` needs to be shared along the nodes. 

It contains some configuration files for webpack setup and customized frontend builds
for portals that are using the style settings feature. 



