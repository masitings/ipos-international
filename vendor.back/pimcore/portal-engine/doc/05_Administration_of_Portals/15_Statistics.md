# Statistics

The portal engine integrates the [Pimcore Statistics Explorer](https://pimcore.com/docs/statistics-explorer/current/) to get 
statistics about the data contained in a portal and for certain tracked user interactions.


## Statistics Area Brick

You will find a statistics area brick which can be embedded into portal engine portal and content pages within the "Content" group.

This brick can be used to create a customized dashboard which shows useful statistics.

The following statistics currently are integrated out-of-the-box.

### Statistics from data pool elasticsearch indices

#### Data object types

Pie chart with the number of data object elements summed up for each
data object class definition.

#### Asset types

Pie chart with the number of asset elements summed up for each different
asset type (image, video, document...).

#### Asset storage by types
Pie chart with the total amount of consumed storage for assets summed up
for each different asset type (image, video, document...).


### Statistics from statistics trackers

The portal engine tracks logins and downloads with a statistics tracker
into additional Elasticsearch indices. 

The trackers work like described in the
[Statistics Explorer documentation](https://pimcore.com/docs/statistics-explorer/current/Tracking_Events.html).
There you will find additional information on the index structure and
about housekeeping the indices/delete outdated statistics information.

#### Logins during last 6 months per week

Amount of logins during the last 6 months per week.

#### Most downloaded assets

Top ten most downloaded assets.

#### Downloads over time
Number of downloads within the last 6 months per week.

#### Downloads over time by context

Number of downloads within the last 6 months separated by download
context (per week):

* Multi Download
* Single Download
* Direct Download Shortcut
* Cart
* Total Collection Data Pool
* Total Guest Share Data Pool

#### Downloads over time by thumbnail

Number of downloads within the last 6 months separated by thumbnail
definition (per week).
