# Provide Predefined Statistic Configuration

There are two options to provide predefined statistic configurations that are globally visible
to every user in a certain context. 

### Global Configurations via Symfony Configuration Tree 

These predefined configurations can be defined on application level or by any bundle and
are configured in symfony configuration tree with the `pimcore_statistics_explorer.global_configurations` 
configuration. 

Following information is necessary: 
- name: The unique name of the configuration, that is also shown in the loading list.
- context: Context in which configuration should be shown. 
- configuration: JSON encoded string of configuration. The best way is to create a configuration in statistics 
explorer, save it and then copy configuration from database table (which is also JSON encoded). 


##### Sample Configuration
```yaml
pimcore_statistics_explorer:
    global_configurations:
        Global Sample 1:
            context: 'portal'
            configuration: '{"showTable":true,"showChart":false,"chartType":null,"selectedDataSource":{"value":"tracking_test","label":"tracking_test","type":"ELASTIC_SEARCH"},"statisticMode":"statistic","rows":[{"value":"dimensions.download.package","label":"dimensions.download.package","typeGroup":"default"}],"columns":[{"value":"dimensions.download.context","label":"dimensions.download.context","typeGroup":"default"}],"filters":[],"fieldSettings":{"dimensions.download.package":{"typeGroup":"default","order":"_key||asc","max":"60"},"dimensions.download.context":{"typeGroup":"default","order":"_key||asc","max":200}}}'
        Global Sample 2:
            context: 'portal'
            configuration: '{"showTable":false,"showChart":true,"chartType":"Pie","selectedDataSource":{"value":"tracking_test","label":"tracking_test","type":"ELASTIC_SEARCH"},"statisticMode":"statistic","rows":[{"value":"dimensions.download.package","label":"dimensions.download.package","typeGroup":"default"}],"columns":[{"value":"dimensions.download.context","label":"dimensions.download.context","typeGroup":"default"}],"filters":[],"fieldSettings":{"dimensions.download.package":{"typeGroup":"default","order":"_key||asc","max":"60"},"dimensions.download.context":{"typeGroup":"default","order":"_key||asc","max":200}}}'

``` 

### Inject dynamic Configurations via Event

#### Event `LoadConfigurationEvent`
- Fires before a configuration with given `id` and `context` is loaded from default sources 
(global configurations, database). If configuration is set to event, this one is used and further configuration
loading is skipped.  
- Available information
  - Id
  - Context
- Changeable information
  - Configuration 