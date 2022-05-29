let mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
	.js("resources/assets/js/patternfly.js", "public/js/patternfly.js")
	.styles(
		[
			"node_modules/patternfly/dist/css/patternfly.min.css",
			"node_modules/patternfly/dist/css/patternfly-additions.min.css",
			"node_modules/datatables.net-jqui/css/dataTables.jqueryui.min.css",
			"node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css",
			"node_modules/select2/dist/css/select2.min.css",
			"node_modules/sweetalert2/dist/sweetalert2.min.css",
			"node_modules/bootstrap-toggle/css/bootstrap2-toggle.min.css",
			"node_modules/summernote/dist/summernote.min.css",
			"resources/assets/css/application.css",
		],
		"public/css/all.css"
	)
	.copy("node_modules/patternfly/dist/fonts/*", "public/fonts/")
	.copy("node_modules/patternfly/dist/img/*", "public/img/")
	.copy("node_modules/summernote/dist/font", "public/css/font/")
	.copy("resources/assets/jstree/", "public/jstree/")
	.copy("resources/assets/js/datatable", "public/js/datatable")
	.copy("resources/assets/js/tabledit.js", "public/js/tabledit.js")
	.copy("resources/assets/js/html2canvas.js", "public/js/html2canvas.js")
	.copy("resources/assets/js/jspdf.js", "public/js/jspdf.js")
	.copy("resources/assets/js/moment.js", "public/js/moment.js")
	.copy(
		"resources/assets/js/daterangepicker.js",
		"public/js/daterangepicker.js"
	)
	.copy(
		"resources/assets/css/daterangepicker.css",
		"public/css/daterangepicker.css"
	).copy(
		"resources/assets/js/JSPrintManager-master",
		"public/js/JSPrintManager-master"
	).copy(
		"resources/assets/js/bluebird.js",
		"public/js/bluebird.js"
	);
