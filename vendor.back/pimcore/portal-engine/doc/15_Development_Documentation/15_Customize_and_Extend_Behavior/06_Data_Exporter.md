# Data Exporter

The Portal Engine ships with a service which can make structured data of data objects and assets downloadable via the 
frontend (for example as CSV or JSON file).

If setup correctly the structured download format will be provided at all places where downloads are possible 
(single download, multi download, cart download, collection download, public shares download) as additional download format option.

The portal engine ships with two simple default implementations for CSV and JSON downloads. These default implementations 
will add all data object and asset metadata fields for a list of quite simple field types which are supported out-of-the-box. 
If you have more advanced or specific requirements in your projects, it's possible to implement your own download format service.


## Enable download format in the data pool config

To provide a download format in the frontend it's needed that the format is activated in the "Available Download Formats" 
config option of the related data pool configuration document.


## Provide your own download format implementation

As mentioned above, it is possible to create your own download formats if needed. To add a new format add a service which 
implements the `DownloadFormatInterface` into the service container configuration.

If your download format is suitable for some data objects or assets only just return `false` in the `supports` Method of 
the `DownloadFormatInterface`. As a starting point for the implementation of such a download format service you might 
take a look at the `JsonDownloadFormat` implementation.

As soon as the download format PHP class is implemented, add it to your service container configuration with the tag 
`pimcore.portal_engine.download_format`:

```yaml
AppBundle\PortalEngine\DownloadFormat\MyCustomDownloadFormat:
        tags:
            - { name: pimcore.portal_engine.download_format }
```