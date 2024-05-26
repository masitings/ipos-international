# Upgrade Information
Following steps are necessary during updating to newer versions.

## Upgrade to 1.1.0
- Make sure to run all bundle migrations with `bin/console pimcore:bundle:migrate -b PimcoreAssetMetadataClassDefinitionsBundle`.


## Upgrade to Pimcore X
- Update to latest (allowed) bundle version in Pimcore 6.9 and execute all migrations.
- Then update to Pimcore X.