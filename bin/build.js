const esbuild = require('esbuild');
const shouldWatch = process.argv.includes('--watch');

const formComponents = [
    'curator',
    'curation',
]

formComponents.forEach((component) => {
    esbuild
        .build({
            define: {
                'process.env.NODE_ENV': shouldWatch
                    ? `'production'`
                    : `'development'`,
            },
            entryPoints: [
                `resources/js/${component}.js`,
            ],
            outfile: `resources/dist/${component}.js`,
            bundle: true,
            platform: 'neutral',
            mainFields: ['module', 'main'],
            watch: shouldWatch,
            minifySyntax: true,
            minifyWhitespace: true,
        })
        .catch(() => process.exit(1))
})