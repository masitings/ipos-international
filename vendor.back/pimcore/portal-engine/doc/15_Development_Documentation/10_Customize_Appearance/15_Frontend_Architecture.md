# Frontend folder structure

The following chapter contains a deep dive into the folder structure of the frontend application and some important
additional information. It provides an overview of the frontend architecture and should help you to locate the files
you need. 

## The `assets` folder

The `assets` folder serves as root folder for the whole frontend application.

* `assets/fonts`  
    Self-hosted web font files.
* `assets/icons`  
    SVG icons 
* `assets/images`  
    Static images (e.g. placeholder image).
* `assets/scripts`  
    JS files and JSX components. See [The `assets/scripts` folder](#page_The-folder-2) below for more details.
* `assets/styles`  
    SCSS files for the base styling 

### The `assets/scripts` folder
The `assets/scripts` is home for all JavaScript files. It contains all JSX components, the whole frontend
application logic and entry files for all pages.

* `components`   
    Generic JSX components that are used throughout the whole application
* `consts`  
    Global constants (e.g. media query breakpoints, fetching state types)
* `features`  
    All JSX components and logic concerning one feature of the application.
     See [Feature folders](#feature-folders) below for more details.
* `pages`  
    Entry files for different pages and shared initialization files.
    Although most parts of the application are rendered on the client, the portal engine is not a single page
    application. The portal engine uses server side routing and only mounts one part of the frontend application on
    each page through these entry files. All entry files have to be listed in the `webpack.config.js`. The subfolder 
    `\assets\scripts\pages\shared` contains the shared React root component `AppRoot.js` and some JavaScript files
     which are used by all entry files.      
* `sliceHelper`  
    Shared feature slice helper functions.
* `utils`  
    Shared utility functions.
* `entry.js`  
    Shared global initialization function for all entry files.
* `store.js`  
    Global redux store that contains the whole application state.
    

#### Feature folders
The `assets/scripts/features` folder consists of multiple subfolders. Each of these subfolders contains all JSX
components and logic concerning one part (or feature) of the application. Every feature folder usually contains the
following content:

* `components` folder  
    JSX components for a specific feature. Some of these components are connected to the store through the redux
    [`connect`](https://react-redux.js.org/api/connect) function. The connect function takes the arguments
    `mapStateToProps` and `mapDispatchToProps` and returns a function that takes a JSX component. The result of this
    call is a connected component. The `mapStateToProps` uses selector functions to read data from the application store
    and maps the data to the component's props. `mapDispatchToProps` defines which action creators should be used
    to create and dispatch actions in response to component events. Some components do not include any JSX and only connect
    generic components to a feature slice of the global application state (e.g. pagination components).
* `{FEATURE_NAME}-actions.js`  
    Action creator functions and action type constants. Action creators are responsible for creating action objects. 
    Action objects describe events that happened in the application. This could be an user interaction, the
    fulfillment of a fetch request or some other event. An action object contains a required ``type`` and an optional
    `payload` to describe the event further. Action type constants are string identifiers that are used as `type`s for
     action objects and by reducers.
* `{FEATURE_NAME}-api.js`  
    Api functions handle the communication with the backend.
* `{FEATURE_NAME}-reducer.js`  
    A reducer function with some initial state for the feature slice. The reducer function is responsible for updating 
    the feature slice state in response to actions. It is called with the previous state slice and the current action
    object every time an action is dispatched. Reducers must not include any side effects like manipulating external state, making fetch requests or
    generating random numbers or identifiers.
* `{FEATURE_NAME}-selectors.js`  
    Selector functions get data from the global application store. A selector function takes the application state
    and any number of additional arguments and returns pieces of the state or some derived data. 

You can find further information about these concepts on the [Redux Website](https://redux.js.org/).    