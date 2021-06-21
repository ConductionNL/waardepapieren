module.exports = [{
      plugin: require('../node_modules/gatsby-remark-images/gatsby-browser.js'),
      options: {"plugins":[]},
    },{
      plugin: require('../node_modules/gatsby-plugin-plausible/gatsby-browser.js'),
      options: {"plugins":[],"domain":"demodam.org"},
    },{
      plugin: require('../node_modules/gatsby-plugin-manifest/gatsby-browser.js'),
      options: {"plugins":[],"name":"Demodam","short_name":"demodam","start_url":"/","display":"standalone","icon":"src/images/demodam_logo.svg","cache_busting_mode":"query","include_favicon":true,"legacy":true,"theme_color_in_head":true,"cacheDigest":"c05f35cac5c02ff22834b4b31787de4e"},
    },{
      plugin: require('../gatsby-browser.js'),
      options: {"plugins":[]},
    }]
