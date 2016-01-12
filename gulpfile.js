'use strict';

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify');

var config = {
    scriptsPaths: [
        'node_modules/jquery/dist/jquery.js',
        'node_modules/bootstrap-sass/assets/javascripts/bootstrap.js'
    ],
    sassPaths: [
        'resources/sass/**/*.scss'
    ],
    fontsPaths: [
        'node_modules/font-awesome/fonts/**/*'
    ],
    buildPath: 'public/build'
};

gulp.task('scripts', function() {
    gulp.src(config.scriptsPaths)
        .pipe(concat('app.js'))
        .pipe(uglify())
        .pipe(gulp.dest(config.buildPath));
});

gulp.task('styles', function() {
    gulp.src(config.sassPaths)
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest(config.buildPath));
});

gulp.task('fonts', function() {
    return gulp.src(config.fontsPaths)
        .pipe(gulp.dest(config.buildPath + '/fonts'));
});

gulp.task('watch', function() {
    gulp.watch(config.sassPaths, ['styles']);
    gulp.watch(config.fontsPaths, ['fonts']);
    gulp.watch(config.scriptsPaths, ['scripts']);
});

gulp.task('default', ['styles', 'fonts', 'scripts']);
