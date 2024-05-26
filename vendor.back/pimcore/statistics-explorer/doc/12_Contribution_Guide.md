# Contribution Guide

## Frontend Builds
All frontend assets are build with webpack encore. The most recent production build is
added to the repository in `src/Resources/public/build` folder. 

To build frontend assets 
- install webpack encore to your project
- add following content to your `package.json`
```json
{
    "name": "demo-project",
    "version": "1.0.0",
    "description": "This skeleton should be used by experienced Pimcore developers for starting a new project from the ground up.  If you are new to Pimcore, it's better to start with our demo package, listed below 😉",
    "main": "index.js",
    "scripts": {
        "encore": "encore",
        "dev": "npx encore dev --watch",
        "production": "npx encore production"
    },
    "dependencies": {
        "statitics-explorer": "file:<RELATIVE_PATH_TO_STATISTICS_EXPLORER_BUNDLE>" 
    },
    "author": "",
    "license": "ISC",
    "devDependencies": {
        "@babel/preset-react": "^7.10.4",
        "@symfony/webpack-encore": "^0.31.0",
        "webpack-notifier": "^1.8.0"
    }
}
``` 

- for development use following command: `npm run dev`
- for creating production build use following command: `npm run production`