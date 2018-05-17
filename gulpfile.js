'use strict';

//require('time-require');

var fs = require('fs');
var less = require('gulp-less');
var gutil = require('gulp-util');
var gulp = require('gulp');

var sourcemaps = require('gulp-sourcemaps');
var cssnano = require('gulp-cssnano');

var browserSync = require('browser-sync');
//var archiver = require('archiver');

var _publicDir = './public/';
var _sourceDir = './dev';

var _styleSource = _sourceDir + '/style';
var _stylePublic = _publicDir + '/css';

var _scriptSource = _sourceDir + '/javascript';
var _scriptPublic = _publicDir + '/js';

var lessConfig = {
    ie8compat: false
};

// webpack
var webpackConfig = require('./webpack.config.js');
var webpack = require("webpack");

// Helper

function fileExists(aPath){
    try {
        fs.accessSync(aPath, fs.F_OK);
        // Do something
    } catch (e) {
        return false;
    }
    return true;
}

function removeDir(path) {
    if( fs.existsSync(path) ) {
        fs.readdirSync(path).forEach(function(file,index){
            var curPath = path + "/" + file;
            if(fs.lstatSync(curPath).isDirectory()) { // recurse
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

gulp.task('styles:development', function() {

    lessConfig.globalVars = {
        ts: (new Date).getTime()
    };

	return gulp.src(['./node_modules/bootstrap/dist/css/bootstrap.css', _styleSource +'/style.less'])
        .pipe(less(lessConfig))
        .on('error', gutil.log.bind(gutil, 'Less Error'))
        .pipe(gulp.dest(_stylePublic))
        .pipe(browserSync.stream());

});

gulp.task("javascript:development", function () {

    // run webpack
    webpack(webpackConfig, function(err, stats) {
        if(err) throw new gutil.PluginError("webpack", err);
        gutil.log("[webpack]", stats.toString({
            // output options
        }));
    });
    
});


gulp.task('serve', ['styles:development'], function() {

    browserSync.init({
        open: true,
        ghostMode: false,
        proxy: "http://loup-garou.local/"
    });

    gulp.watch(['./node_modules/bootstrap/dist/css/bootstrap.css'], [_styleSource+"/**/*.less"], ['styles:development']);
    
    webpackConfig.watch = true;

    // run webpack
    webpack(webpackConfig, function(err, stats) {
        if(err) throw new gutil.PluginError("webpack", err);
        gutil.log("[webpack]", stats.toString({
            // output options
        }));
        browserSync.reload();
    });
    
});