# Development Documentation

The portal engine offers quite a lot of configuration possibilities through its administration features. 
Further more it was implemented with project specific customization requirements in mind. 
As the portal engine is a regular Symfony bundle it is possible to extend its parts like described 
in the [Symfony documentation](https://symfony.com/doc/current/bundles/override.html).

The development documentation explains several customization aspects which are offered by the portal engine
and might be needed in your project. 

#### Documentation Parts 
- [Extending Search Index](./05_Search_Index_Management/05_Extend_Search_Index.md): 
  Describes how to add additional data attributes to the Elasticsearch index.
- [Customize Appearance](./10_Customize_Appearance/README.md): 
  Describes how to change the appearance of the portal engine via twig templates and customized frontend builds.
- [Customize and Extend Behavior](./15_Customize_and_Extend_Behavior/README.md): 
  Describes how to customize the behaviour of the portal engine through code changes. 