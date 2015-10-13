var gulp = require('gulp');						// Gulp!
//var sass = require('gulp-ruby-sass');			// Sass
var sass = require('gulp-sass');				// Sass
var prefix = require('gulp-autoprefixer');		// Autoprefixr
var minifycss = require('gulp-minify-css');		// Minify CSS
var concat = require('gulp-concat');			// Concat files
var uglify = require('gulp-uglify');			// Uglify javascript
var rename = require('gulp-rename');			// Rename files
var util = require('gulp-util');				// Writing stuff
var livereload = require('gulp-livereload');	// LiveReload
var jshint = require('gulp-jshint');			// jshint
var clean = require('gulp-clean');


gulp.task('move',['clean'], function(){

	gulp.src(['bower_components/fontawesome/css/font-awesome.min.css'])
	.pipe(gulp.dest('assets/fonts/fontawesome/css'));

	gulp.src(['bower_components/fontawesome/fonts/*.*'])
	.pipe(gulp.dest('assets/fonts/fontawesome/fonts'));

});

/**
 * Compile all CSS for the site
 */
gulp.task( 'sass', function() {
	gulp.src([
		'assets/scss/app.scss'])									// Gets the apps scss
		.pipe(sass())												// Compile sass
		.on('error', function (err) { console.log(err.message); })  // Handle sass errors
		.pipe(concat('main.css'))									// Concat all css
		.pipe(rename({suffix: '.min'}))								// Rename it
		.pipe(minifycss())											// Minify the CSS
		.pipe(gulp.dest('assets/css/'))								// Set the destination to assets/css
		util.log(util.colors.yellow('Sass compiled & minified'));	// Output to terminal
});

/**
 * Get all the JS, concat and uglify
 */
gulp.task('javascripts', function(){
	gulp.src([
		'bower_components/screenfull/dist/screenfull.js',   // Gets Fullscreen
		// 'bower_components/fastclick/lib/fastclick.js',	// Gets fastclick
		// 'bower_components/svgeezy/svgeezy.js',         	// Gets svgeezy
		// 'bower_components/modernizr/modernizr.js',		// Gets modernizr

		// Get Foundation JS - change to only include the scripts you'll need
		// 'bower_components/foundation/js/foundation/foundation.js',
		// 'bower_components/foundation/js/foundation/foundation.abide.js',
		// 'bower_components/foundation/js/foundation/foundation.accordion.js',
		// 'bower_components/foundation/js/foundation/foundation.alert.js',
		// 'bower_components/foundation/js/foundation/foundation.clearing.js',
		// 'bower_components/foundation/js/foundation/foundation.dropdown.js',
		// 'bower_components/foundation/js/foundation/foundation.equalizer.js',
		// 'bower_components/foundation/js/foundation/foundation.interchange.js',
		// 'bower_components/foundation/js/foundation/foundation.joyride.js',
		// 'bower_components/foundation/js/foundation/foundation.magellan.js',
		// 'bower_components/foundation/js/foundation/foundation.offcanvas.js',
		// 'bower_components/foundation/js/foundation/foundation.orbit.js',
		// 'bower_components/foundation/js/foundation/foundation.reveal.js',
		// 'bower_components/foundation/js/foundation/foundation.slider.js',
		// 'bower_components/foundation/js/foundation/foundation.tab.js',
		// 'bower_components/foundation/js/foundation/foundation.tooltip.js',
		// 'bower_components/foundation/js/foundation/foundation.topbar.js',

		// Get Isotope
		// 'bower_components/imagesloaded/imagesloaded.pkgd.js',
		// 'bower_components/isotope/dist/isotope.pkgd.js',

		// moving on...
		'assets/js/_*.js'])								// Gets all the user JS _*.js from assets/js
		.pipe(concat('scripts.js'))						// Concat all the scripts
		.pipe(rename({suffix: '.min'}))					// Rename it
		.pipe(uglify())									// Uglify & minify it
		.pipe(gulp.dest('assets/js/'))					// Set destination to assets/js
		util.log(util.colors.yellow('Javascripts compiled and minified'));
});

/**
 * JS hint
 */
gulp.task('jshint', function() {
	gulp.src('assets/js/_*.js')
		.pipe(jshint())
		.pipe(jshint.reporter('jshint-stylish'));
});

/**
 * Minify all SVGs and images
 */
gulp.task('svgmin', function() {
	gulp.src('assets/img/*.svg')							// Gets all SVGs
	.pipe(svgmin())											// Minifies SVG
	.pipe(gulp.dest('assets/img_min/'));					// Set destination to assets/img_min/
	util.log(util.colors.yellow('SVGs minified'));			// Output to terminal
});

/**
 * Clean up
 */
gulp.task('clean', function() {
  return gulp.src('**/.DS_Store', { read: false })
  .pipe(clean());
});

/**
 * Default gulp task.
 */
gulp.task('watch', function(){

	// You need to run gulp through vagrant for livereload to work
	livereload.listen();

	gulp.watch("assets/scss/**/*.scss", ['sass']);            // Watch and run sass on changes
	gulp.watch("assets/js/_*.js", ['jshint', 'javascripts']); // Watch and run javascripts on changes

	// Reload when php files, compiled css, compiled js and images change.
	gulp.watch(['**/*.php', 'assets/css/**', 'assets/js/**', 'assets/img/**']).on('change', function(file) {
		gulp.src(file.path).pipe(livereload()); // Trigger LiveReload
		util.log(util.colors.yellow('File changed' + ': ' + file.path ));
	});

});

gulp.task('default', ['sass', 'jshint', 'javascripts', 'clean', 'watch']);
