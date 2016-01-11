'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');

var config = {
    sassPath: 'resources/sass',
    buildPath: 'public/build'
};

gulp.task('styles', function() {
    gulp.src(config.sassPath + '/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest(config.buildPath));
});

gulp.task('fonts', function() {
    return gulp.src('node_modules/font-awesome/fonts/*')
        .pipe(gulp.dest(config.buildPath + '/fonts'));
});

gulp.task('watch', function() {
	gulp.watch(config.sassPath + '/**/*.scss', ['styles']);
});

gulp.task('default', ['styles', 'fonts']);
