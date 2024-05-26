# Upgrade Information

Following steps are necessary during updating to newer versions.

## Upgrade to 2.1.0
- Execute all migrations of the bundle.

## Upgrade to 2.0.0
- First upgrade to last 1.x version and make sure all migrations are executed properly.
- Migrate Pimcore to Pimcore 6.9
- Migrate controller references to new naming scheme (e.g. ), for example by using migration command
  `bin/console migration:controller-reference`
- Add following line to your firewalls configuration in the `security.yml` of your app after the `pimcore_admin` firewall.
```yml 
security:
    firewalls:
        pimcore_admin: 
            # ...
        portal_engine: '%pimcore_portal_engine.firewall_settings%'
```
- Clear Pimcore Caches (`bin/console pimcore:cache:clear`)
- Elastic Search mapping has changed to fix deprecations. Rebuild your index by running following commands: 
```bash
bin/console portal-engine:update:index-recreate
bin/console portal-engine:update:process-index-queue
```