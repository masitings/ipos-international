# Add additional Area Bricks

Area bricks are principally implemented the same way as described in the 
[Pimcore documentation](https://pimcore.com/docs/pimcore/current/Development_Documentation/Documents/Editables/Areablock/Bricks.html).

The only difference is that the area brick class needs to extend the `Pimcore\Bundle\PortalEngineBundle\Document\AbstractAreaBrick` 
class. As soon as a service with this parent class is registered in the service container with the tag `pimcore.area.brick`,
the area brick will be provided on portal and content pages.

 ```yaml
 # a service.yml file defining services
 services:
    # needs to extend Pimcore\Bundle\PortalEngineBundle\Document\AbstractAreaBrick 
    AppBundle\Document\Areabrick\TestArea:
        tags:
            - { name: pimcore.area.brick, id: testarea }
 ```