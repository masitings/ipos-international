# Using Statistics Loader

The statistics loader can be used to embed the report (table and chart) of stored 
configurations into your application. 
 
### Steps to Embed into Application

- **Include boostrap style sheets**: Statistics explorer is using bootstrap markup. To provide proper styling it 
  makes sense in include bootstrap style sheets
- **Include statistics loader style sheets**: Include them via symfony webpack encore with `{{ encore_entry_link_tags('loader', null, 'pimcoreStatisticsExplorer') }}`
- **Define statistics loader markup node**: For each report add a markup that has the `statistics-container` class assigned and the 
 the `data-config-id` attribute set, e.g. `<div class="statistics-container" data-config-id="global_global1"></div>`. 
- **Define URLs in `statisticsExplorerConfig`**: To tell statistics explorer which endpoints to use. The URLss 
  also implicitly define the context in which statistics explorer is executed (first URL part after prefix). Following
  URLs need to be defined. 
    - `loadConfigurationUrl` : `/<PREFIX>/<CONTEXT>/load`,
    - `resultDataUrl` : `/<PREFIX>/<CONTEXT>/data`,
    - `translationsUrl` : `/<PREFIX>/<CONTEXT>/translations`
- **Include statistics loader scripts**: Include them via symfony webpack encore with `{{ encore_entry_script_tags('loader', null, 'pimcoreStatisticsExplorer') }}`

A sample template could look like: 

```html 
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link
        rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk"
        crossorigin="anonymous"
    />


    {{ encore_entry_link_tags('loader', null, 'pimcoreStatisticsExplorer') }}

</head>

<body>

<div class="container">

    <h1 class="my-3">Statistics Loading Sample</h1>

    <div class="statistics-container" data-config-id="global_global1"></div>

    <hr/>

    <div class="statistics-container" data-config-id="7eb395c9-f809-11ea-9e4e-005056a349c0"></div>

</div>

<script type="text/javascript">
    statisticsExplorerConfig = {
        loadConfigurationUrl: '/admin/stats/portal/load',
        resultDataUrl: '/admin/stats/portal/data',
        translationsUrl: '/admin/stats/portal/translations'
    }
</script>

{{ encore_entry_script_tags('loader', null, 'pimcoreStatisticsExplorer') }}

</body>
</html>
```

Of course it is also possible to directly embed it into an existing react application.
See [loader.js](https://github.com/pimcore/statistics-explorer/blob/master/assets/js/loader.js) for details.  



