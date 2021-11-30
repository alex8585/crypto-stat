const mix = require("laravel-mix")
const path = require('path');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
  .ts("resources/js/app.js", "public/js")
  .react()
  .postCss("resources/css/app.css", "public/css", [
    require("postcss-import"),
    require("tailwindcss"),
    require("autoprefixer"),
  ])
  .webpackConfig(require("./webpack.config"))

if (mix.inProduction()) {
  mix.version()
}
mix.webpackConfig({
  resolve: {
      alias: {
          ziggy: path.resolve('vendor/tightenco/ziggy/dist'),
      },
  },
});
mix.disableNotifications()
mix.sourceMaps()
//mix.browserSync("https://local-devs-list.com/")
