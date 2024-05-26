# Tracking Events

The bundle also ships with basis to easily implement a tracker that tracks events 
to an elasticsearch index.

Supports elasticsearch versions 6 and 7.

### Implement Tracker

To implement a tracker, extend the `AbstractElasticsearchTracker` and implement all abstract methods:
 
- `getIndexName`: Define the name of index. 
- `buildMappingArray`: Define elasticsearch mapping for index as array.
- `trackEvent`: Method for actual event tracking that is called by outside world. It takes a parameter array, 
  prepares data for tracking (e.g. extracting information, adding timestamp, etc.) and builds structure expected by 
  the mapping, and then calls `doTrackEvent` (which does the elasticsearch communication).   

The abstract base class takes care of all the generic index creation, alias management and elasticsearch
communication. 

A sample implementation can be found in [`DummyTracker`](https://github.com/pimcore/statistics-explorer/blob/master/src/StatisticsTracker/Elasticsearch/DummyTracker.php).


### Tracking Events
For each event type, a tracker needs to be implemented. The tracker implementations then need to be registered
as services and then can be injected to any service or controller.
 
By calling `$tracker->trackEvent($parameters)` the events get tracked.  


### Elasticsearch Index Structure
For performance and data house keeping reasons, the tracker implements some kind of index rotating. 
This means data is tracked to an index named `<INDEX_NAME>__<YEAR>_<MONTH>` which always corresponds to the
current month. If the index doesn't exist, it is created automatically.

In addition to creation of the indices, there is also an alias `<INDEX_NAME>` is created. This alias shows to 
all existing rolled indices and can be used for querying the statistics. 

The abstract base class for the tracker takes care of all necessary steps. There are only two things to consider: 
- When implementing tracker is to extend from the abstract base class and implement the abstract methods.
- When setting up a data source for the explorer, the index alias needs to be used as index name.  

Housekeeping itself needs to be done by the administrator of the elasticsearch cluster - 
by closing/archiving deleting old indices that are not needed anymore for statistics and updating the index alias. 


