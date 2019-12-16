const { src, dest, parallel } = require("gulp");
const pug = require("gulp-pug");
const less = require("gulp-less");
const minifyCSS = require("gulp-csso");
const concat = require("gulp-concat");

var uglify = require("gulp-uglify"),
  concat = require("gulp-concat");

gulp.task("js", function() {
  gulp
    .src("scripts/*.js")
    .pipe(uglify())
    .pipe(concat("script.js"))
    .pipe(gulp.dest("assets"));
});

var connect = require("gulp-connect");
var browserSync = require("browser-sync").create();
gulp.task("watch", ["browserSync"], function() {
  gulp.watch("app/scss/**/*.scss", ["sass"]);
  // Other watchers
});

gulp.task("watch", ["browserSync", "sass"], function() {
  gulp.watch("app/scss/**/*.scss", ["sass"]);
  // Reloads the browser whenever HTML or JS files change
  gulp.watch("app/*.html", browserSync.reload);
  gulp.watch("app/js/**/*.js", browserSync.reload);
});

gulp.task("connect", function() {
  connect.server({
    root: ".",
    livereload: true
  });
});

gulp.task("sass", function() {
  return gulp
    .src("app/scss/**/*.scss") // Gets all files ending with .scss in app/scss
    .pipe(sass())
    .pipe(gulp.dest("app/css"))
    .pipe(
      browserSync.reload({
        stream: true
      })
    );
});

function html() {
  return src("client/templates/*.pug")
    .pipe(pug())
    .pipe(dest("build/html"));
}

function css() {
  return src("client/templates/*.less")
    .pipe(less())
    .pipe(minifyCSS())
    .pipe(dest("build/css"));
}

function js() {
  return src("client/javascript/*.js", { sourcemaps: true })
    .pipe(concat("app.min.js"))
    .pipe(dest("build/js", { sourcemaps: true }));
}

exports.js = js;
exports.css = css;
exports.html = html;
exports.default = parallel(html, css, js);

gulp.task("run", [("sass", "css")]);

gulp.task("default", [run, "watch"]);
