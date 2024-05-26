# JSX Components

The portal engine is build with customization in mind and offers a very flexible way to customized the application.
This chapter contains a small introduction to React / JSX components and describes how you can customize the appearance,
markup and behaviour of JSX components. 

## Introduction to React / JSX components

React embraces the fact that rendering logic is inherently coupled with other UI logic (like event handling and state
updates). Instead of separating by technology React encourages to combine Markup and UI Logic into encapsulated
components. JSX enables this by providing an easy way to  write HTML Markup in JavaScript.  

If you are new to React or JSX you should read the [Main concepts of React](https://reactjs.org/docs/hello-world.html)
 first.

The portal engine only uses function components which means that components are just simple JavaScript functions
that take a props object argument and return JSX. Each component file contains one main component which is
exported as the default export. 

## Customizing JSX Components

The process of customizing any component can be broken down into four steps:
1) Create the file `{PROJECT_ROOT}/public/portal-engine/{FRONTEND_BUILD_NAME}/{PATH_TO_FILE}` 
  (for Pimcore 6 `{PROJECT_ROOT}/web/portal-engine/{FRONTEND_BUILD_NAME}/{PATH_TO_FILE}`)
2) Import everything you want to reused from the bundle source file with the `portal-engine-bundle` prefix
3) Add your customizations and export them
4) Re-export everything else from the bundle source files

**NOTE**: All examples below require you to have your [Customized Frontend Build](./10_Customize_Frontend_Build.md) set
up.

### Override generic JSX components

The easiest way to start customizing the application is by overwriting a generic component completely. 
To overwrite a component we have to create the corresponding file 
`{PROJECT_ROOT}/public/portal-engine/{FRONTEND_BUILD_NAME}/{PATH_TO_FILE}` (for Pimcore 6 `{PROJECT_ROOT}/web/portal-engine/{FRONTEND_BUILD_NAME}/{PATH_TO_FILE}`). 
Make sure that `{PATH_TO_FILE}` matches the name and path of the bundle source file (relative to `assets` source folder).
The next step is to create our customized component function and declare it as default export. The function is going
to be called with the same props object argument as the original
 component function. 

The following example shows how this could look like for the generic Teaser component `/scripts/components/Teaser.js`:

````js
import React from 'react';
import Trans from "~portal-engine/scripts/components/Trans";

// Create customized component function & declare it as default export 
export default function ({
    image = {},
    title = '',
    href = '#',
    children
}) {
    return (
        <div className="card">
            <img className="card-img-top" {...image} alt={image.alt || title}/>
            <div className="card-body">
                <h5 className="card-title">{title}</h5>
                <div className="card-text">
                    {children}
                </div>
                <a href={href} className="btn btn-primary">
                    <Trans t={'teaser.cta'}/>
                </a>
            </div>
        </div>
    )
}

// Import all other functions from the bundle source file and re-export all other exports
export * from "portal-engine-bundle/scripts/components/Teaser";
````

**Note:** `portal-engine-bundle` has to be used instead of `~portal-engine` to import from the original bundle source
file. The `portal-engine-bundle` prefix should only used if you have to import from the corresponding bundle source file
directly. Other imports like the the `Trans` component import above should use the `~portal -engine` prefix in case
 they are overwritten too. 

### Customization through subcomponents
Some components allow you to customize parts of component through subcomponents. Subcomponents can be
passed in through component props and usually end with the suffix `Component`. 

If we look at the bundle source file of the Teaser component we find three of those subcomponents: 
* ImageComponent
* BodyComponent
* DetailActionComponent

The following example shows how you can customize the Image subcomponent and keep the rest of the component unchanged:

`````js
import React from 'react';

// Import base component
import Teaser from "portal-engine-bundle/scripts/components/Teaser";

// Create custom subcomponent & export it
export function Image({
    image = {},
    title,
}) {
    return (
        <div className="embed-responsive embed-responsive-16by9">
            <img {...image} alt={image.alt || title} className="embed-responsive-item"/>
        </div>
    )
}

// Reuse the base Teaser component and pass the customized Image subcomponent as ImageComponent prop 
export default function (props) {
    return <Teaser {...props} ImageComponent={Image} />;
}

// Import all other functions from the bundle source file and re-export all other exports
export * from "portal-engine-bundle/scripts/components/Teaser";
`````
 
You can also use subcomponents to customize the overall structure of a component and reuse some parts of the original
component. The example below shows how you could reuses the Image subcomponent while changing the rest of the Teaser
completely:

````js
import React from 'react';
import Trans from "~portal-engine/scripts/components/Trans";

// Import Image subcomponent from the bundle source file
import {Image} from "portal-engine-bundle/scripts/components/Teaser";

// Create custom Teaser component
export default function (props) {
    const {
        title = '',
        href = '#',
        children
    } = props;

    return (
        <div className="card">
            <div className="card-img-top">
                {/* Reuse the Image subcomponent from the bundle source file */}
                <Image {...props}/>
            </div>

            <div className="card-body">
                <h5 className="card-title">{title}</h5>
                <div className="card-text">
                    {children}
                </div>

                <a href={href} className="btn btn-primary">
                    <Trans t="teaser.open"/>
                </a>
            </div>
        </div>
    )
}

// Import all other functions from the bundle source file and re-export all other exports
export * from "portal-engine-bundle/scripts/components/Teaser";
````

### Override connected components

A lot of non-generic components from the feature subfolder are connected to the global application store and have to
be treated a bit differently if you want to customize them. While you can just overwrite the default export of a 
generic component you have to reconnect connected components to the store. A connected component file usually
exports a `mapStateToProps` function, a `mapDispatchToProps` function/object and the unconnected JSX component as
named exports. The default export is always the connected JSX component. You can connect your customized component by
calling the [`connect`](https://react-redux.js.org/api/connect) function with `mapStateToProps` and `mapDispatchToProps`
from the bundle source file.

The following example shows how this would look like for the connected DataPoolPagination component 
`/scripts/features/data-pool-list/components/DataPoolPagination.js`.

````js
import React from 'react';
import {connect} from "react-redux";

/* Import the mapDispatchToProps, mapStateToProps from the bundle source file */
import {
    mapDispatchToProps,
    mapStateToProps
} from "portal-engine-bundle/scripts/features/data-pool-list/components/DataPoolPagination";
import {noop} from "~portal-engine/scripts/utils/utils";

// Create custom pagination component
function Pagination({
    pageCount = 5,
    currentPage = 1,
    onPageClick = noop
}) {
    const pages = new Array(pageCount).fill(0).map((_, index) => index + 1);

    return (
        <nav>
            <ul className="pagination">
                {pages.map((current) => (
                    <li key={current} className={`page-item ${current === currentPage ? 'active' : ''}`}>
                        <button type="button" className="page-link" onClick={() => onPageClick(current)}>
                            {current}
                        </button>
                    </li>
                ))}
            </ul>
        </nav>
    )
}

// Connect customized component with the application store and declare it as default export
export default connect(mapStateToProps, mapDispatchToProps)(Pagination)
````

### Adding new features

If you want to take it a step further you can customize  `mapStateToProps` and `mapDispatchToProps` too. This allows
you to add new features or change the behaviour of a component completely. The following example illustrates this:

````js
import React from 'react';
import {connect} from "react-redux";

/* Import mapDispatchToProps & mapStateToProps from the bundle source file */
import {
    mapDispatchToProps,
    mapStateToProps
} from "portal-engine-bundle/scripts/features/data-pool-list/components/DataPoolPagination";
import {noop} from "~portal-engine/scripts/utils/utils";

// Customize mapStateToProps
function customizedMapStateToProps(state) {
    return {
        // Reuse mapStateToProps from the bundle source file
        ...mapStateToProps(state),

        // Add new custom props
        additionalProp: getDataFromTheState(state), 
        otherAdditionalProp: getOtherDataFromTheState(state),

        // Overwrite existing props
        pageCount: customizedPageCountSelector(state)  
    }
}

// Customize mapDispatchToProps
const customizedMapDispatchToProps = {
    // Reuse mapDispatchToProps from the bundle source file
    ...mapDispatchToProps,

    // Add custom event handler function
    onSomeCustomEvent: someCustomActionCreator,

    // Overwrite existing behaviour
    onPageClick: goToRandomPage
};

// Customize component
function Pagination({
    pageCount = 5,
    currentPage = 1,
    additionalProp,
    someOtherAdditionalProp,
    onPageClick = noop,
    onSomeCustomEvent = noop
}) {
    const pages = new Array(pageCount).fill(0).map((_, index) => index + 1);

    return (
        <nav>
            {/* Use custom props*/}
            {additionalProp}
            
            {/* Use custom event handler function */}
            <button onClick={() => onSomeCustomEvent()}>Surprise!</button>

            {/* Additional customizations */}
        </nav>
    )
}

// Connect the component with your customized mapStateToProps & mapDispatchToProps
export default connect(customizedMapStateToProps, customizedMapDispatchToProps)(Pagination)
````

## Customizing other parts of the application

This chapter showed you how you can customize the appearance and behaviour of JSX components, but you don't have to stop
there. You can use the same technique of importing, customizing and exporting to customize almost every part of the
application. 
 
