const mix = require('laravel-mix');

/*
|--------------------------------------------------------------------------
| Mix Asset Management pour E-commerce Laravel + React
|--------------------------------------------------------------------------
|
| Mix fournit une API propre et fluide pour définir les étapes de build
| Webpack pour votre application Laravel + React. Par défaut, nous
| compilons les fichiers Sass et JS dans des versions optimisées.
|
*/

// Configuration principale
mix.js('resources/js/app.js', 'public/js')
   .react() // Active le support React avec preset
   .sass('resources/sass/app.scss', 'public/css')
   .css('resources/css/animations.css', 'public/css')
   .sourceMaps();

// Configuration additionnelle pour l'environnement de production
if (mix.inProduction()) {
    mix.version();
} else {
    // Configuration pour le développement
    mix.options({
        hmrOptions: {
            host: 'localhost',
            port: 8080
        }
    });
}

// Configuration Webpack personnalisée
mix.webpackConfig({
    resolve: {
        extensions: ['.js', '.jsx', '.json'], // CORRECTION: Retirer le '*' initial
        alias: {
            '@': path.resolve('resources/js'),
            '@components': path.resolve('resources/js/components'),
            '@pages': path.resolve('resources/js/pages'),
            '@styles': path.resolve('resources/css')
        }
    },
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: [
                            '@babel/preset-env',
                            '@babel/preset-react'
                        ],
                        plugins: [
                            '@babel/plugin-syntax-dynamic-import',
                            '@babel/plugin-proposal-class-properties'
                        ]
                    }
                }
            }
        ]
    }
});

// Configuration pour les notifications (corrige l'erreur snoreToast)
mix.options({
    processCssUrls: false,
    notifications: {
        onSuccess: false,
        onFailure: true
    }
});

// Copier les assets statiques
mix.copyDirectory('resources/images', 'public/images');

// Configuration BrowserSync pour le développement
mix.browserSync({
    proxy: 'localhost:8000', // URL de votre serveur Laravel
    files: [
        'app/**/*.php',
        'resources/views/**/*.php',
        'resources/js/**/*.js',
        'resources/js/**/*.jsx',
        'resources/sass/**/*.scss',
        'resources/css/**/*.css'
    ],
    watchOptions: {
        usePolling: true, // Nécessaire pour Windows
        interval: 1000
    }
});

// Optimisations pour Windows
mix.options({
    fileLoaderDirs: {
        images: 'images',
        fonts: 'fonts'
    }
});

// Configuration spécifique pour React Fast Refresh (développement)
if (!mix.inProduction()) {
    mix.options({
        hmrOptions: {
            host: 'localhost',
            port: 8080
        }
    });
    
    mix.webpackConfig({
        devServer: {
            host: '0.0.0.0',
            port: 8080,
            hot: true,
            watchOptions: {
                poll: 1000, // Nécessaire pour Windows
                aggregateTimeout: 300
            }
        }
    });
}