# Installation 

## Minimum Requirements

* Pimcore >= 6.7

## Install

Install bundle via composer:
```bash 
composer require pimcore/asset-metadata-class-definitions
```

Enable bundle via console or extensions manager:
```bash
php bin/console pimcore:bundle:enable PimcoreAssetMetadataClassDefinitionsBundle 
php bin/console pimcore:bundle:install PimcoreAssetMetadataClassDefinitionsBundle 
```


## Required Backend User Permission
To define asset metadata class definitions, user needs to meet one of following criteria:  
* be an `admin` or
* have `classes` permission