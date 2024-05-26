# Precondition Service for Data Pools

Data pool workspaces and permissions provide a good way to configure which assets and data objects should be visible in 
the portal engine frontend. Nevertheless, depending on the use case this might not be enough.

If you would like to add your individual filter logic which should be applied to one or multiple data pools implement a 
custom pre condition service.

To add custom pre condition services, define a Symfony service that extends `PreConditionServiceHandlerInterface` and 
has the tag `pimcore.portal_engine.pre_condition_service_handler` assigned. 

The drop down in the configuration document lists all services with the tag 
`pimcore.portal_engine.pre_condition_service_handler` assigned.



###### Sample Service
```php
class ActualCars implements PreConditionServiceHandlerInterface
{

    public function addPreCondition(Search $search)
    {
        $search->addQuery(new TermQuery("standard_fields.objectType.raw", "Actual-Car"));
    }
}
```

###### Sample Service Definition
```yml
  AppBundle\PortalEngine\PreConditionService\ActualCars:
    tags:
      - { name: pimcore.portal_engine.pre_condition_service_handler }
```

The search can be modified using the [ ONGR ElasticsearchDSL query builder library](https://github.com/ongr-io/ElasticsearchDSL).