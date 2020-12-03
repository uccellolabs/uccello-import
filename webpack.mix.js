const mix = require('laravel-mix')

const autoload = {
   jquery: [ '$', 'jQuery', 'jquery']
}
mix.autoload(autoload)

mix.setPublicPath('public')

mix.js('./resources/js/script.js', 'public/js')
   .version()

// Copy all compiled files into main project (auto publishing)
mix.copyDirectory('public', '../../../public/vendor/uccello/import');
