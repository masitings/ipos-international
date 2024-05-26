# Pimcore Enterprise Subscription Tools

Certain tools for Pimcore that come (or are necessary) with Enterprise Subscription

## Pimcore Subscription Status
Check Pimcore subscription status with [Pimcore License Server](https://license.pimcore.com)
- On every load of Pimcore backend check is executed and subscription status is shown in sidebar.
- Status badge is linked to [Pimcore License Server](https://license.pimcore.com) for detail status information. 
- Check subscription status all 24h via Pimcore maintenance

#### Enterprise Bundles
Enterprise bundles that should be included in the checks have to 
- implement `EnterpriseBundleInterface` and specify bundle liecense id
- implement `DependentBundleInterface` with following method: 
```php 
public static function registerDependentBundles(BundleCollection $collection)
{
    $collection->addBundle(new PimcoreEnterpriseSubscriptionToolsBundle());
}
```

## Pimcore Installations
To setup license token add following configuration to your Pimcore installation
```yml 
pimcore_enterprise_subscription_tools:
    token: '<YOUR_TOKEN_FROM_PIMCORE>'
```
