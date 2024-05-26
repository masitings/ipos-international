# Adding Data Sources during Runtime

It is possible to add additional data sources during runtime by listening to following event.
This event fires once per process when the `StatisticsService` is initialized.  

#### Event `StatisticsServiceInitEvent`
- Fires when `StatisticsService` is initialized
- Available information
  - StatisticsService
- Changeable information
  - call all methods of `StatisticsService`, most likely `addDataSourceAdapter` for adding 
    additional data sources. 