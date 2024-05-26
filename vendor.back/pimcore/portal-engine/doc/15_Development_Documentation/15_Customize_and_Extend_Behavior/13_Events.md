# Events

Within the portal engine the following events are triggered to allow influencing certain behaviors. All of these events 
are triggered via the Symfony event dispatcher. Therefore, the related 
[Symfony Events and Event Listeners](https://symfony.com/doc/current/event_dispatcher.html) documentation is a good starting 
point if you do not know how it works already.

The events do not have dedicated event names but are represented by there full class name and namespace. You will find 
one example how to listen to a portal engine event [here](./03_Name_Extractor.md).

## List of Events

Take a look at the event PHP classes. You will find a hint for the purpose of the events as PHP doc. Additionally, the 
getters and setters describe which attributes can be influenced by event listeners.

### Asset

* `Pimcore\Bundle\PortalEngineBundle\Event\Asset\ExtractMappingEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Asset\ExtractNameEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Asset\UpdateIndexDataEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Asset\Upload\PreAssetCreateEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Asset\Upload\PostAssetCreateEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Asset\Upload\PostUploadEvent`

### Data Object
* `Pimcore\Bundle\PortalEngineBundle\Event\DataObject\ExtractMainImageEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\DataObject\ExtractMappingEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\DataObject\ExtractNameEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\DataObject\UpdateIndexDataEvent`

### Auth

* `Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginCheckPasswordEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginFieldTypeEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginGetUserEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginPasswordChangeableEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Auth\RecoverPasswordEmailTemplateEvent`

### Download

* `Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadAssetEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadAssetPathInZipEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadSourcesEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadTypesEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadZipFilenameEvent`

### Element
* `Pimcore\Bundle\PortalEngineBundle\Event\Element\VersionHistoryEvent`

### Permission
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolAccessEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolCreateItemEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolDeleteItemEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolDownloadAccessEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolDownloadItemEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolEditItemEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolShowItemEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolSubfolderItemEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolUpdateItemEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolVersionHistoryEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\DataPoolViewOwnedAssetsOnlyEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\PortalAccessEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\ResolveAssetDataPoolWorkspacesEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\ResolveDataObjectDataPoolWorkspacesEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\ResolveUserAssetWorkspacesEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Permission\ResolveUserDataObjectWorkspacesEvent`

### Search
* `Pimcore\Bundle\PortalEngineBundle\Event\Search\FilterableFieldsEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Search\ListableFieldsEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Search\SearchQueryEvent`
* `Pimcore\Bundle\PortalEngineBundle\Event\Search\SortableFieldsEvent`