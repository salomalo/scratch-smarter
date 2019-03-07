var project = 'affiliatewp-afnf';
var projectURL = 'https://affiliatewp.com';
var productURL = './';

var afnfJS = './assets/js/dev/*.js'; // Path to JS custom scripts folder.
var afnfJSDestination = './assets/js/'; // Path to place the compiled JS custom scripts file.
var afnfJSFile = 'affiliatewp-afnf';

// Watch files paths.
var mainJSWatchFiles = './assets/js/dev/*.js'; // Path to all custom JS files.

var gulp = require( 'gulp' );

var concat = require( 'gulp-concat' ); // Concatenates JS files
var uglify = require( 'gulp-uglify' ); // Minifies JS files

// Utilities.
var rename = require( 'gulp-rename' );
var filter = require( 'gulp-filter' );

var notify = require( 'gulp-notify' );

gulp.task( 'mainJS', function() {
	gulp.src( afnfJS )
		.pipe( concat( afnfJSFile + '.js' ) )
		.pipe( gulp.dest( afnfJSDestination ) )
		.pipe( rename( {
			basename: afnfJSFile,
			suffix: '.min'
		} ) )
		.pipe( uglify() )
		.pipe( gulp.dest( afnfJSDestination ) )
		.pipe( notify( {
			message: 'AFNF build completed.',
			onLast: true
		} ) );
} );

// Run `gulp build` to build js.
// Gulp will also watch for changes.
gulp.task( 'build', [ 'mainJS' ], function() {
	gulp.watch( mainJSWatchFiles, [ 'mainJS' ] );
} );
