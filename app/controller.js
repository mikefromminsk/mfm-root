controller("app", function ($scope, $timeout, $q, $http, $mdDialog, $routeParams) {

    $scope.mode = $routeParams.arg0 || "new";
    $scope.domain_name = $scope.mode === "new" ? null : $routeParams.arg1
    $scope.domain_name_focus = false
    $scope.path = []
    $scope.files = null

    let js_editor = ace.edit("js_editor");
    js_editor.setTheme("ace/theme/monokai");
    js_editor.getSession().setMode("ace/mode/javascript");

    let html_editor = ace.edit("html_editor");
    html_editor.setTheme("ace/theme/monokai");
    html_editor.getSession().setMode("ace/mode/html");
    html_editor.setValue("<html>\n<body>\n{{message}}\n</body>\n</html>")

    if ($scope.mode === "new") {
        $scope.$watch(function () {
            return $scope.domain_name;
        }, function (newVal) {
            if ($scope.mode === "new")
                js_editor.setValue("controller(\"" + newVal + "\", function($scope) {\n\t$scope.message = \"Hello world\"\n})")
        });
    } else {
        $dark.file_get($scope.domain_name, "index.html", function (data) {
            html_editor.setValue(data)
        })
        $dark.file_get($scope.domain_name, "controller.js", function (data) {
            js_editor.setValue(data)
        })
        get_files()
    }

    function get_files() {
        $dark.dir_get($scope.domain_name, $scope.path.join("/"), function (files) {
            $scope.files = files
            $scope.$apply()
        })
    }

    $scope.fileOpen = function (file) {
        if (file.size === 0) {
            $scope.path.push(file.name)
            get_files()
        } else {
            $scope.download(file.name)
        }
    }


    $scope.fileDelete = function (file) {
        let pathClone = [...$scope.path]
        pathClone.push(file.name)
        $dark.file_delete($scope.domain_name, pathClone.join("/"), function () {
            get_files()
        });
    }

    $scope.fileBack = function (file) {
        $scope.path.pop()
        get_files()
    }

    $scope.save_request_in_progress = false
    $scope.save = function () {
        $scope.save_request_in_progress = true
        if ($scope.mode === "new") {
            $dark.domain_create($scope.domain_name, function () {
                $scope.mode = "edit"
                $scope.apps = $dark.store.keys();
                saveIndexAndController()
            }, function () {
                $scope.save_request_in_progress = false
                $scope.$apply();
            });
        } else
            saveIndexAndController()
    }

    function saveIndexAndController() {
        $dark.file_set($scope.domain_name, "controller.js", js_editor.getValue(), function () {
            $dark.file_set($scope.domain_name, "index.html", html_editor.getValue(), function () {
                $scope.save_request_in_progress = false
                get_files()
            }, function () {
                $scope.save_request_in_progress = false
                $scope.$apply();
            });
        }, function () {
            $scope.save_request_in_progress = false
            $scope.$apply();
        });
    }

    $scope.uploadFile = function () {
        $dark.file_upload($scope.domain_name, "", function () {
            get_files()
        });
    }

    $scope.fileSizeFormat = function (size) {
        if (size < 1024)
            return size + " bytes"
        if (size >= 1024 && size < 1048576)
            return (size / 1024).toFixed(2) + " kb"
        if (size >= 1048576)
            return (size / 1048576).toFixed(2) + " Mb"
    }

    $scope.download = function (filename) {
        $dark.file_download($scope.domain_name, filename)
    }
})