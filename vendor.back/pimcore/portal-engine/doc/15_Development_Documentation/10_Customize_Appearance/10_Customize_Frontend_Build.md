# Customize Frontend Build

Most of the frontend parts of the portal engine are built with client side technologies. The JavaScript components are 
built with the help of [React](https://reactjs.org/) whereas all CSS files are powered by [SASS](https://sass-lang.com/).

As all of these libraries consist of a lot of files and SASS needs to be compiled to CSS, the portal engine uses 
[webpack](https://webpack.js.org/) to pack all the frontend stuff to single minimized files. The integration into the 
portal engine and the Pimcore/Symfony stack is done via [webpack encore](https://symfony.com/doc/current/frontend.html),
which is the Symfony standard way to integrate webpack into Symfony applications.

Within the portal engine there are two ways to change the appearance of the frontend build:

1. Customize the colors and look and feel of the frontend via the admin interface. 
  See [Styling Settings and Frontend Build](../../05_Administration_of_Portals/05_Configuration/30_Styling_Settings_and_Frontend_Build.md) 
  for more details.

2. Customize parts of the SASS and JavaScript files with a customized frontend build via code changes. The next sections 
  will inform you how to do this.

## Create your own customized frontend build

The following steps are needed to create a customized frontend build.

### Step 1: Configure a customized frontend build in the configuration tree

```yaml
# put this into your yml config (i.e. config.yml)
pimcore_portal_engine:
    customized_frontend_builds:
        - my_portal 
```

The build name (`my_portal`) will be the final build folder in the filesystem, therefore do not use spaces or special 
characters here.

### Step 2: Install Node.js

To be able to create webpack builds it's needed to install [Node.js](https://www.npmjs.com/) as you will need the tools 
[npm](https://www.npmjs.com/) and [npx](https://www.npmjs.com/package/npx). You will need at least npm version 6 to make 
it up and running.

### Step 3: Setup your development environment

Npm is a package manager (similar to composer) and uses a file called `package.json` to handle all the dependencies. 
Therefore, it's needed setting up a suitable package.json into the root directory of your project.

The portal engine provides a console command to support your with this action. Just call the following command as a starting point:

```
bin/console portal-engine:update:frontend-build
```

The final result of this command will be a `package.json` in your root folder with all needed dependencies of the portal 
engine. If your project already has a `package.json` in the root directory, the command will update not overwrite the 
existing `package.json`.

At the end, the command will instruct you to call `npm install` in the root folder of your project to install all dependencies. 

```
npm install
```

### Step 4: Create some customized files

If your build name is `my_portal`, the correct place to put all the files which you would like to replace or add into your 
final build would be the directory `{PROJECT_ROOT}/public/portal-engine/my_portal` (for Pimcore 6: `{PROJECT_ROOT}/web/portal-engine/my_portal`). 
If needed, it's possible to add additional 
files, but for the beginning it would be a good idea to overwrite some existing source files which are shipped with the 
portal engine. All these files are placed in the `assets` folder (top level folder within the bundle source code). 

A very powerful file to change the appearance of the frontend without changing any real CSS codes is the `variables.less` 
located in `assets/styles/settings/variables.scss`. This file contains a lot of SASS variables to modify the look and feel. 
The portal engine is built on top of the [Bootstrap CSS framework](https://getbootstrap.com/) therefore all bootstrap SASS 
variables can be manipulated too.

The convention for overwriting source files is to put them into the same sub paths and give them the same file names like 
in the portal engine `assets` source folder.

Your customized `variables.scss` might look like this:

```css
/* put this file into {PROJECT_ROOT}/public/portal-engine/my_portal/styles/settings/variables.scss */
@import "portal-engine-bundle/styles/settings/variables";

$primary: #FF0000;
```

This example would change the primary color to red. If you do not want to completely replace but extend the original 
file do not forget to import it. This can be done with regular webpack `@import` statements. Take a look at the webpack 
docs for more details.

The portal engine bundle can be referenced with the `portal-engine-bundle` module name. This module name will always 
reference the original bundle source folder.

It's also possible to use `~portal-engine` as bundle name for imports. If you do it this way the logic is some kind of 
fallback logic:

1. First take a look at your customized frontend build asset folder. If the file/module exists there, use it.
2. If not take the file/module from the original bundle source folder.

Within the portal engine bundle all files/modules are referenced with `~portal-engine`. This makes it possible to extend 
all of them. If you would like to reference always the original source file always use `portal-engine-bundle`.

Following this convention it's possible to overwrite and extend all portal engine SASS and JavaScript files. To do this 
effectively and if you want to do heavy customizations, it would be needed to have or get deeper knowledge of webpack, 
React, SASS and bootstrap. See [Customize JSX Components](./20_JSX_Components.md) for more details on how to
customize JSX components. 

### Step 5: Setup the customized frontend build in your portal

Take a look at the portal/root document of your portal engine site. There is a setting called "Customized Frontend Build". 
If you added your build into the configuration tree it should appear in the select box. Select it and save the document.

### Step 6: Execute the development build

The `portal-engine:update:frontend-build` will add some npm scripts to execute the build in the root folder of your project. 
While developing execute the following if your build is called `my_portal`: 

```
npm run dev_my_portal
```

This will create a development build and start a file watch. The development build contains some additional debugging 
possibilities (for example source maps). The file watch will listen to changes of the asset source files. Therefore, it's 
not needed to start this command again and again on each file change. Just let it up and running in the background.

### Step 7: Take a look at the portal frontend

Your changes should be visible there! Yeah!

### Step 8: Execute the production build

The development build is very helpful for development purposes, but you should never deploy the development build to a 
production environment. An additional production build script exists to create a minified build without the debugging 
stuff enabled.

```
npm run build_my_portal
```

All final build files will be located in the folder `{PROJECT_ROOT}/public/portal-engine/build` 
(for Pimcore 6: `{PROJECT_ROOT}/web/portal-engine/build`).


## Develop for the core

This would work quite the same way as mentioned above. For this purpose an additional option of the 
`portal-engine:update:frontend-build` exists (`--core-bundle-development`).

```
bin/console portal-engine:update:frontend-build --core-bundle-development
```

This will install additional npm scripts to update the frontend builds which are shipped within the bundle in your 
development environment.

```
npm run dev # development build
npm run build # production build
```