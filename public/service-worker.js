/**
 * Copyright 2018 Google Inc. All Rights Reserved.
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// If the loader is already loaded, just stop.
if (!self.define) {
  let registry = {};

  // Used for `eval` and `importScripts` where we can't get script URL by other means.
  // In both cases, it's safe to use a global var because those functions are synchronous.
  let nextDefineUri;

  const singleRequire = (uri, parentUri) => {
    uri = new URL(uri + ".js", parentUri).href;
    return registry[uri] || (
      
        new Promise(resolve => {
          if ("document" in self) {
            const script = document.createElement("script");
            script.src = uri;
            script.onload = resolve;
            document.head.appendChild(script);
          } else {
            nextDefineUri = uri;
            importScripts(uri);
            resolve();
          }
        })
      
      .then(() => {
        let promise = registry[uri];
        if (!promise) {
          throw new Error(`Module ${uri} didn’t register its module`);
        }
        return promise;
      })
    );
  };

  self.define = (depsNames, factory) => {
    const uri = nextDefineUri || ("document" in self ? document.currentScript.src : "") || location.href;
    if (registry[uri]) {
      // Module is already loading or loaded.
      return;
    }
    let exports = {};
    const require = depUri => singleRequire(depUri, uri);
    const specialDeps = {
      module: { uri },
      exports,
      require
    };
    registry[uri] = Promise.all(depsNames.map(
      depName => specialDeps[depName] || require(depName)
    )).then(deps => {
      factory(...deps);
      return exports;
    });
  };
}
define(['./workbox-10cc9e8c'], (function (workbox) { 'use strict';

  self.skipWaiting();
  workbox.clientsClaim();

  /**
   * The precacheAndRoute() method efficiently caches and responds to
   * requests for URLs in the manifest.
   * See https://goo.gl/S9QRab
   */
  workbox.precacheAndRoute([{
    "url": "/css/app.css",
    "revision": "df8abfb0de36ac3c12019ed8939d42ad"
  }, {
    "url": "/fonts/vendor/bootstrap-sass/bootstrap/glyphicons-halflings-regular.eot?5be1347c682810f199c7f486f40c5974",
    "revision": "f4769f9bdb7466be65088239c12046d1"
  }, {
    "url": "/fonts/vendor/bootstrap-sass/bootstrap/glyphicons-halflings-regular.svg?060b2710bdbbe3dfe48b58d59bd5f1fb",
    "revision": "89889688147bd7575d6327160d64e760"
  }, {
    "url": "/fonts/vendor/bootstrap-sass/bootstrap/glyphicons-halflings-regular.ttf?4692b9ec53fd5972caa2f2372ae20d16",
    "revision": "e18bbf611f2a2e43afc071aa2f4e1512"
  }, {
    "url": "/fonts/vendor/bootstrap-sass/bootstrap/glyphicons-halflings-regular.woff2?be810be3a3e14c682a257d6eff341fe4",
    "revision": "448c34a56d699c29117adc64c43affeb"
  }, {
    "url": "/fonts/vendor/bootstrap-sass/bootstrap/glyphicons-halflings-regular.woff?82b1212e45a2bc35dd731913b27ad813",
    "revision": "fa2772327f55d8198301fdb8bcfc8158"
  }], {});
  workbox.cleanupOutdatedCaches();

}));
