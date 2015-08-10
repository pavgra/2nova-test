var gulp = require('gulp'),
    concat = require('gulp-concat'),
    less = require('gulp-less'),
    minifycss = require('gulp-minify-css'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify');

var paths = {
	less: {
		watch: './resources/assets/less/**/*.less',
		src: './resources/assets/less/styles.less'
	}
}

gulp.task('less', function() {
  return gulp.src(paths.less.src)
  	.pipe(less())
    .pipe(minifycss())
    .pipe(concat('styles.css'))
    .pipe(gulp.dest('./public/css'));
});

gulp.task('watch', function() {
  // Watch .less
  gulp.watch(paths.less.watch, ['less']);
});