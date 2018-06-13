'use strict';

//require('time-require');

var fs = require('fs');
var gutil = require('gulp-util');
var sass = require('gulp-sass');
var gulp = require('gulp');

var sourcemaps = require('gulp-sourcemaps');
var cssnano = require('gulp-cssnano');

var browserSync = require('browser-sync');
//var archiver = require('archiver');

var _publicDir = './public/';
var _sourceDir = './dev';

var _styleSource = _sourceDir + '/style';
var _stylePublic = _publicDir + '/css';

var _scriptSource = _sourceDir + '/js';
var _scriptPublic = _publicDir + '/js';


// webpack
var webpackConfig = require('./webpack.config.js');
var webpackConfigProd = require('./webpack.prod.config.js');
var webpack = require("webpack");

// Helper

function fileExists(aPath) {
    try {
        fs.accessSync(aPath, fs.F_OK);
        // Do something
    } catch (e) {
        return false;
    }
    return true;
}

function removeDir(path) {
    if (fs.existsSync(path)) {
        fs.readdirSync(path).forEach(function (file, index) {
            var curPath = path + "/" + file;
            if (fs.lstatSync(curPath).isDirectory()) { // recurse
                removeDir(curPath);
            } else { // delete file
                fs.unlinkSync(curPath);
            }
        });
        fs.rmdirSync(path);
    }
}

//
// Gulp Tasks

gulp.task('default', ['serve']);

gulp.task('styles:development', function () {


    return gulp.src([
        './node_modules/bootstrap/dist/css/bootstrap.min.css',
        './node_modules/bootswatch/dist/*/*.min.css',
        './node_modules/noty/lib/noty.css',
        './node_modules/noty/lib/themes/bootstrap-v4.css',
        _styleSource + '/medias.scss'])
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest(_stylePublic))
        .pipe(browserSync.stream());

});

gulp.task("javascript:development", function () {

    // run webpack
    webpack(webpackConfig, function (err, stats) {
        if (err) throw new gutil.PluginError("webpack", err);
        gutil.log("[webpack]", stats.toString({
            // output options
        }));
    });

});

gulp.task("prepare:development", ['javascript:development', 'styles:development']);
gulp.task("prepare:production", ['javascript:production', 'styles:production']);

gulp.task('serve', ['styles:development'], function () {

    browserSync.init({
        open: true,
        ghostMode: false,
        proxy: "http://loup-garou.local/"
    });

    gulp.watch([_styleSource + "/**/*.scss"], ['styles:development']);

    webpackConfig.watch = true;

    // run webpack
    webpack(webpackConfig, function (err, stats) {
        if (err) throw new gutil.PluginError("webpack", err);
        gutil.log("[webpack]", stats.toString({
            // output options
        }));
        browserSync.reload();
    });

});

gulp.task('styles:production', function () {


    return gulp.src([
        './node_modules/bootstrap/dist/css/bootstrap.min.css',
        './node_modules/bootswatch/dist/*/*.min.css',
        './node_modules/noty/lib/noty.css',
        './node_modules/noty/lib/themes/bootstrap-v4.css',
        _styleSource + '/medias.scss'])
		.pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.init())
        .pipe(cssnano())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(_stylePublic))
        .pipe(browserSync.stream());

});

gulp.task("javascript:production", function (done) {

    // run webpack
    webpack(webpackConfigProd, function (err, stats) {
        if (err) throw new gutil.PluginError("webpack", err);
        gutil.log("[webpack]", stats.toString({
            // output options
        }));
        done();
    });

});