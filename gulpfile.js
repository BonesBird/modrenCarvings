var gulp = require("gulp");
var sass = require("gulp-sass");

gulp.task("sass", function() {
  return gulp
    .src("./sass/**/*.scss")
    .pipe(sass().on("error", sass.logError))
    .pipe(gulp.dest("./css"));
});

gulp.task("sass:watch", function() {
  gulp.watch(".sass/**/*.scss", ["sass"]);
});

var uglifycss = require("gulp-uglifycss");

gulp.task("css", function() {
  gulp
    .src("./styles/**/*/css")
    .pipe(
      uglifycss({
        maxLineLen: 80,
        uglyComments: true
      })
    )
    .pipe(gulp.dest("./dist/"));
});
