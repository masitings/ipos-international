# Multi Language Portals

Multi language portals are supported via language variant document trees. The easiest way setting up a multi language is 
via the [configuration wizard](./02_Configuration_Wizard.md).

## Create Multiple Language Trees
If you would like to do it manually, create sub documents for each language variant below the root document (en, de, fr...). 
Depending on your use case, these sub documents might be (portal engine) content pages or data pool pages. But do not use 
a portal page for this purpose. Each portal should consist of exactly one portal page document only (the root document). 

## Data Pool Language Variants

Language variant data pools documents should not be real/additional data pool documents as then the system would count 
them as separate data pools. Therefore, the document type `Portal Engine - Data Pool Language Variant` exists. This acts 
just as a reference to the original data pool within the language variant tree. All settings (except the language) will 
be taken from the original document.

<div class="image-as-lightbox"></div>

![Language Variant Reference](../../img/admin_docs/config-language-reference.png)

## Navigation Root Property

The portal engine offers a `Portal Engine - Navigation Root` (`portal-engine_navigation-root`) predefined document property. 
Add this to all language variant root documents and add a document reference to itself. The main navigation will start at 
the navigation root (all children will be the main level of the navigation). 

Additionally, the portal engine language variant will list all data pool and data pool language variant documents starting 
from the current navigation root. This means that all other data pool documents outside of the current navigation root 
path will be hidden.

## Language Redirect

If you would like to use multi language portals check the `Enable Language Redirect` in the portal engine portal/root 
document. This will enable a language redirect based on the browser locale. The user additionally will have the possibility 
to configure his preferred language on the 
[portal profile page](../../10_User_Documentation_for_Portals/05_General_Features/27_Users_and_Profile.md).