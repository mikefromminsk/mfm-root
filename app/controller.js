controller("app", function ($scope, $timeout, $q, $http, $mdDialog, $routeParams) {

    $scope.mode = $routeParams.arg0 || "new";
    $scope.domain_name = $scope.mode === "new" ? null : $routeParams.arg1

    editor = ace.edit("js_editor");
    editor.setTheme("ace/theme/monokai");
    editor.getSession().setMode("ace/mode/javascript");

    editor = ace.edit("html_editor");
    editor.setTheme("ace/theme/monokai");
    editor.getSession().setMode("ace/mode/html");

    $scope.files = [
        {file_name: "New Folder", file_size: 0},
        {file_name: "New Folder", file_size: 0},
        {file_name: "New Folder", file_size: 0},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
    ]

    $scope.uploadImage = function () {
    }

    $scope.save_request_in_progress = false
    $scope.save = function () {
        $scope.save_request_in_progress = true
        if ($scope.mode === "new") {
            $dark.domain_create($scope.domain_name, function () {
                $scope.save_request_in_progress = false
                $scope.$apply();
                $scope.mode = "edit"
            }, function () {
                $scope.save_request_in_progress = false
                $scope.$apply();
            });
        }
        $dark.file_put();
    }

    $scope.path = "/";
    $scope.uploadFile = function () {
        $dark.upload(domain_name, path);
    }

})