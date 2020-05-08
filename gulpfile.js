var gulp = require("gulp"),
    foreach = require("gulp-foreach"),
    directiveReplace = require("gulp-directive-replace"),
    jshint = require("gulp-jshint"),
    uglify = require("gulp-uglify"),
    concat = require("gulp-concat"),
    rename = require("gulp-rename"),
    sass = require("gulp-ruby-sass"),
    maps = require("gulp-sourcemaps"),
    app = "wpBlocks",
    directories = {
        root: "assets",
        js: "scripts",
        directives: "dist",
        sass: "sass",
        min: {
            css: "css",
            js: "js"
        }
    },
    src = {
        js: [directories.root + "/" + directories.js + "/*.app.js",
            directories.root + "/" + directories.js + "/*.config.js",
            directories.root + "/" + directories.js + "/*.factory.js",
            directories.root + "/" + directories.js + "/*.provider.js",
            directories.root + "/" + directories.js + "/*.service.js",
            directories.root + "/" + directories.js + "/*.filter.js",
            directories.root + "/" + directories.js + "/*.compressed.js",
        ],
        directives: {
            js: directories.root + "/" + directories.js + "/*.directive.js",
            html: directories.root + "/" + directories.js + "/*.template.html",
        },
        sass: directories.root + "/" + directories.sass + "/*.s*ss"
    };

gulp.task("replaceDirectives", function(){
    return gulp.src(src.directives.js)
        .pipe(foreach(function(stream, file){
            return stream
                .pipe(directiveReplace({root: "./" + directories.root + "/" + directories.js}))
                .pipe(rename(function (path) {
                    path.basename += ".compressed";
                }))
        }))
        .pipe(maps.write("./" + directories.root + "/" + directories.js))
        .pipe(gulp.dest("./" + directories.root + "/" + directories.js));
    });

gulp.task("compressJs", ["replaceDirectives"], function(){
    gulp.src(src.js)
        .pipe(jshint())
        .pipe(jshint.reporter("default"))
        .pipe(concat(app + ".min.js"))
        .pipe(uglify())
        .pipe(gulp.dest(directories.root + "/" + directories.min.js));
});

gulp.task("compressSass", function(){
    return sass(src.sass, {
        style: "compressed",
        sourcemap: true })
        .pipe(rename({
            basename : app,
            extname : ".min.css"
        }))
        .pipe(maps.write("./"))
        .pipe(gulp.dest(directories.root + "/" + directories.min.css));
    }
);

gulp.task("watch",function() {
    gulp.watch(src.sass, ["compressSass"]);
    gulp.watch([src.directives.js, src.directives.html], ["replaceDirectives"]);
    gulp.watch(src.js, ["compressJs"]);
});

gulp.task("default", ["compressSass", "replaceDirectives", "compressJs"]);