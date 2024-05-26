# Technical Details

## Migration of existing Data

To add metadata collections to assets based on existing metadata, use following command. 
It will check for existing metadata entries if they have a prefix like `License.something` and then 
adds the corresponding collection (in this case `License`) to the asset.   

```
php bin/console asset-metadata-class-definitions:populate
```


## Assign collections to an asset programmatically 

To assign metadata collections to assets, use the `Collections` model as follows: 
```php
$col = new Pimcore\AssetMetadataClassDefinitionsBundle\Model\Collections();
$col->setAssetId(290);
$col->setCollections(["License", "Credits"]);

// either only apply collections to asset without saving it
$col->applyToAsset();

// or apply collection to asset and save the asset right away 
$col->save();

// setting metadata attributes it self is the same as with standard metadata
$asset = Pimcore\Model\Asset::getById(290); 
$asset->addMetadata('CarImages.title', 'input', $title, $language);
$asset->save(); 
```


## Add a definition programmatically

```php
$configuration = new \Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration();
$configuration->setName("License");
$configuration->setPrefix("License");
$configuration->setIcon("/bundles/pimcoreadmin/img/object-icons/02_red.svg");

$panel = new \Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Layout\Panel();
$panel->setTitle("panel title");


$input = new \Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Input();
$input->setName("myinputfield");

$panel->addChild($input);

$configuration->setLayoutDefinitions($panel);
$configuration->save();
```
