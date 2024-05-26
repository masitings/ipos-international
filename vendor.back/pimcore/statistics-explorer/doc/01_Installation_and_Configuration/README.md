# Installation and Configuration

### Install Bundle

To install statistics explorer bundle use following commands:

```bash 
composer require pimcore/statistics-explorer
./bin/console pimcore:bundle:enable PimcoreStatisticsExplorerBundle
./bin/console pimcore:bundle:install PimcoreStatisticsExplorerBundle
```

### Configuration

The statistics explorer needs to be embedded into an existing host application that
provides the context in which the statistics explorer is executed.

The statistics explorer can be executed in multiple contexts within one host application and each
context is connected to user & login information. 
The name for contexts can be freely selected, example would be `pimcore_admin`, `portal`, `frontend`, ...

To set up a context and embed the statistics explorer, there are three additional setup steps necessary
besides the actual installation of the bundle. 

#### Setup User Provider

The host application needs to take about user authentication (e.g. via a symfony
firewall) and provide the statistics explorer the user information. 

This is done by creating a service that is implementing the
[`UserProviderInterface`](https://github.com/pimcore/statistics-explorer/blob/master/src/User/UserProviderInterface.php) and that is tagged with
the `pimcore.statistics_explorer.user_provider_context` tag which defines the context the user provider is for. 
This service has methods to return the current logged-in user and other users that configurations can be shared with.
 
The bundle ships with a sample implementation for Pimcore users which also can be used for your application. 

###### Sample User Provider Configuration
```yaml
    statistics_explorer.userprovider_portal:
        autowire: true
        autoconfigure: true
        public: false
        class: Pimcore\Bundle\StatisticsExplorerBundle\User\PimcoreUserProvider
        tags:
            - { name: 'pimcore.statistics_explorer.user_provider_context', context: 'portal' } 
```


#### Setup Routes

All controller actions of the statistics explorer need to be executed in the defined context 
and most probably in the context of the corresponding firewall. To provide maximal flexibility,
the statistics explorer does not predefine any routes but allows you to define the route prefix(es) 
in which the statistics explorer should be reachable. 

This can be done by adding following entry to your `routing.yml` whereas the prefix can be selected freely. 

```yaml
pimcore_statistics_explorer:
    resource: "@PimcoreStatisticsExplorerBundle/Controller"
    type:     annotation
    prefix:   /admin/stats
```

The urls for statistics explorer will add the context name and the actual action to it, and look like similar as 
`/admin/stats/portal/data-sources`.  

See also [Using Statistics Explorer](./02_Usage/04_Using_Statistics_Explorer.md) and 
[Using Statistics Loader](./02_Usage/06_Using_Statistics_Loader.md)
for further details. 


#### Setup Data Sources

The last setup step is to configure data sources that should be used by the statistics explorer. Data sources
are symfony services that implement the [`StatisticsStorageAdapterInterface`](https://github.com/pimcore/statistics-explorer/blob/master/src/StatisticsStorageAdapter/StatisticsStorageAdapterInterface.php) 
and are tagged with the `pimcore.statistics_explorer.data_source` which defines the data source name and the 
context in which it should be available. 

As an alternative to tagged services, there is also the possibility to 
[add data sources during runtime](./05_Further_Customizing/10_Adding_Data_Sources_during_Runtime.md) via events.  

The bundle ships with two implementations for the interface which can be used right away to define data sources:
- [ElasticsearchAdapter](https://github.com/pimcore/statistics-explorer/blob/master/src/StatisticsStorageAdapter/ElasticsearchAdapter.php)
  - expects index name as argument.
  - define the elasticsearch cluster host by setting `pimcore_statistics_explorer.es_hosts` configuration.
  - optionally, a `ElasticsearchClientFactory` can be provided to specify a special elasticsearch cluster 
  (by default the cluster defined in `pimcore_statistics_explorer.es_hosts` configuration is used).
  - Supports elasticsearch versions 6 and 7.
  
- [MySqlAdapter](https://github.com/pimcore/statistics-explorer/blob/master/src/StatisticsStorageAdapter/MySqlAdapter.php)
  - expects table name as argument
  - optionally, a `Connection` can be provided to specify a special database connection 
  (by default the default Pimcore connection is used)

By implementing the interface, also additional data sources could be supported. 


###### Sample Data Source Configuration
```yaml
    datasources.data_objects_car:
        class: Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\ElasticsearchAdapter
        autowire: true
        autoconfigure: true
        public: false
        arguments:
            $indexName: 'data_objects_car'
            $label: 'Data Objects Car'
        tags:
            - { name: 'pimcore.statistics_explorer.data_source', 'dataSourceName': 'data_objects_car', context: 'portal' }


    datasources.assets:
        autowire: true
        autoconfigure: true
        public: false
        class: Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\MySqlAdapter
        arguments:
            $tableName: 'assets'
            $label: 'Assets'  
        tags:
            - { name: 'pimcore.statistics_explorer.data_source', 'dataSourceName': 'assets', context: 'portal' }
            - { name: 'pimcore.statistics_explorer.data_source', 'dataSourceName': 'assets', context: 'another_portal' }
```
