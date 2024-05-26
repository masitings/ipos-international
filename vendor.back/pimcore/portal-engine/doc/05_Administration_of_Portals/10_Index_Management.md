# Index Management

All data pool listings and the full text search are powered by Elasticsearch. 
For this reason it is needed to index all assets and data objects into Elasticsearch to make them visible in the frontend.

## Created Elasticsearch indices

The portal engine generates Elasticsearch indices for the following entities:

* Assets search index (one alias and one index)
* Data objects search index (one alias and one index per class definition)
* Login tracker for login statistics (one index)
* Download tracker for download statistics (one index)

For the asset and data object indices the portal engine uses an alias (e.g. `<index_prefix>_asset`) that points to the 
most current index (e.g. `<index_prefix>_asset-odd`). The alias name always stays the same, the index names alternate 
between `-odd` and `-even` suffix. For more details also see 'Updating index structure for data indices' in the next section. 


## Keep the indices up to date

Whereas the tracker indices are created automatically as soon as some gets tracked, the element search indices need to be 
created via the following console commands:

```
# create/update all indices + their mappings and add all items to the index queue
bin/console portal-engine:update:index

# process the index queue with 5 parallel processes
bin/console portal-engine:update:process-index-queue --processes=5
``` 

The index queue processing command needs to be setup as cronjob like described in the 
[installation guide](../01_Installation/01_Installation.md).
This will ensure that the Elasticsearch indices will be updated if the data of the related Pimcore elements change.


### Updating index structure for data indices
When updating asset meta data definitions or class definitions, updates of the index mapping might be necessary. While 
some updates (e.g. adding new fields) can be applied to existing indices, other update (e.g. changing type of an attribute) 
cannot be applied to an existing index. 

In this case, the portal engine automatically creates a new index with the next suffix (either `-odd` or `-even`), uses
the reindex command of ES to populate the data from the old index to the new one, and once finished, it changes the alias 
to the newly created index and deletes the old index.

To make sure, the data is really updated properly, all assets or corresponding data objects are added to the index queue
and are reindexed from the original data index queue processing command. 

Thus, updates of index structure should be transparent and applied fully automatically without any manual action needed
and the frontend always should have data available.


## Repairing indices

Sometimes it might be needed to delete and recreate the index (for example if the Elasticsearch mapping changed and 
cannot be updated).

Do this with the index recreate command and execute the index queue afterwards.
```
# delete all indices + create them and add all items to the index queue
bin/console portal-engine:update:index-recreate

# process the index queue with 5 parallel processes
bin/console portal-engine:update:process-index-queue --processes=5
``` 

## Extending indices

If you would like to extend the data within the indices read the 
[Extend Search Index](../15_Development_Documentation/05_Search_Index_Management/05_Extend_Search_Index.md) chapter in 
the development documentation.