# Download Cart

The download cart allows collecting multiple items of different data pools and download them together in one zip file. 
Each user has one download cart per portal, and the items also remain in download cart after logging out of the portal. 
 
### Download Cart Items
All data elements shown in a portal can be added to the download cart. When adding a data element to the download cart, 
a download cart item gets created. Each download cart item contains the element itself (e.g. asset, data object) and 
additional settings that are specified when adding the elements like: 
  - Format of structured data export
  - Thumbnail of images
  - For data objects: linked assets and their thumbnail that should be included into the download
 
 <div class="image-as-lightbox"></div>
 
![Add To Cart](../../img/user_docs/add-to-cart.png)  
  
### Download Cart Listing

<div class="image-as-lightbox"></div>

![Download Cart Listing](../../img/user_docs/download-cart.png)  

The download cart listing shows all items added to the cart and allows to manage them. 

##### Edit download cart items
Edit settings of download item by opening the 'add to download cart' dialog of corresponding data pool and modify
settings like download formats and included data. 


##### Remove download cart item
Remove download item from download cart. 

##### Execute download
Creates a ZIP file with all items respecting their settings and offers it to download. The ZIP file is generated in
a background task. 

<div class="image-as-lightbox"></div>
  
![Background Task Finished](../../img/user_docs/background-task-finished.png)
  
> The prepared download gets cleaned up on the server after the download has finished or when user deletes the 
> finished notification.  


