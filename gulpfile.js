const gulp = require("gulp");
const webpack = require("webpack-stream");
const babel = require("gulp-babel");
const concat = require("gulp-concat");
const uglify = require("gulp-uglify");
const sass = require("gulp-sass")(require("sass"));
const sourcemaps = require("gulp-sourcemaps");
const cleanCss = require("gulp-clean-css");
const rename = require("gulp-rename");


// Configuration file to keep your code DRY
const cfg = require("./gulpconfig.json");
const paths = cfg.paths;
const slug = cfg.slug;

// Aufgabe zum Kompilieren von SCSS zu CSS
function compileSCSS() {
  return gulp
    .src([paths.vendors + "/**/*.css", paths.sass + "/**/*.scss"])
    .pipe(sourcemaps.init())
    .pipe(sass().on("error", sass.logError))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(paths.css))
    .pipe(cleanCss())
    .pipe(rename({ suffix: ".min" }))
    .pipe(gulp.dest(paths.css));
}

// Aufgabe zum Beobachten von Änderungen
function watchFiles() {
  gulp.watch(paths.sass, compileSCSS);
}

// Run:
// gulp copy-assets.
// Copy all needed dependency assets files from bower_component assets to themes /js, /scss and /fonts folder. Run this task after bower install or bower update

gulp.task("copy-assets", function (done) {
  // Copy all JS files
  // var stream = gulp
  //   .src(`${paths.node}popper.js/dist/**/*`)
  //   .pipe(gulp.dest(`${paths.dev}/vendors/popper.js`));

  gulp
    .src(`${paths.node}tippy.js/dist/**/*`)
    .pipe(gulp.dest(`${paths.dev}/vendors/tippy.js`));

  gulp
    .src(`${paths.node}tippy.js/themes/**/*`)
    .pipe(gulp.dest(`${paths.dev}/vendors/tippy.js/themes`));

  gulp
    .src(`${paths.node}tippy.js/animations/**/*`)
    .pipe(gulp.dest(`${paths.dev}/vendors/tippy.js/animations`));

  done();
});

gulp.task("compileJS", function () {
  var scripts = [
    // `${paths.dev}/vendors/popper.js/umd/popper.js`,
    // `${paths.dev}/vendors/tippy.js/tippy-bundle.umd.js`,
    `${paths.dev}/js/*.js`,
  ];

  return gulp
    .src(scripts, { allowEmpty: true })
    .pipe(webpack(require("./webpack.config.js")))
    .pipe(gulp.dest(paths.js));
});

gulp.watch(`${paths.dev}/js/*.js`, gulp.series("compileJS"));

// Standardaufgabe
const build = gulp.series(compileSCSS, "compileJS", watchFiles);

exports.default = build;
