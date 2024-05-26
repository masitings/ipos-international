# Customizing Results

Depending on context and use case it might be necessary to customize the result before 
printing it to the reports. 

**Use cases might be**
- Adding additional filters due to user permissions
- Enriching result data with additional information, e.g. nice names, links, ...
- Customizing table template for including links etc.

With statistics explorer, this can be archived by subscribing to events.

#### Event `DataFilterModificationEvent`
- Fires after filter condition for data request is generated and allows to modify the filter
- Available information
  - Context
  - Configuration (if it is a saved configuration)
  - Data source name
  - Statistics mode (`statistic` or `list`)
- Changeable information
  - Filter
  
#### Event `DataPreQueryEvent`
- Fires just before query is sent to storage backend and allows to modify the query
- Available information
  - Context
  - Configuration (if it is a saved configuration)
  - Data source name
  - Statistics mode (`statistic` or `list`)
- Changeable information
  - Query (elasticsearch query array, sql statement)
  
#### Event `DataResultEvent`
- Fires before statistics data result is returned to controller and allows to modify the result
- Available information
  - Context
  - Configuration (if it is a saved configuration)
  - Data source name
  - Statistics mode (`statistic` or `list`)
- Changeable information
  - StatisticsResult
  
#### Event `TableRenderEvent`
- Fires before table template is rendered and allows to modify template name and its parameters
- Available information
  - Context
  - Configuration (if it is a saved configuration)
  - Data source name
  - Statistics mode (`statistic` or `list`)
- Changeable information
  - template
  - parameters
