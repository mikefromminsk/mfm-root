controller("darkapp", function ($scope, $timeout, $q, $http) {

    $scope.token = store.get("user_session_token") || $routeParams.arg0;
    $scope.toggleCreateAppFragment = true
    $scope.toggleExplorerFragment = false

    $scope.files = [
        {file_name: "New Folder", file_size: 0},
        {file_name: "New Folder", file_size: 0},
        {file_name: "New Folder", file_size: 0},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
    ]

    $scope.apps = [
        {
            domain_name: 'Super Application',
            author: "x29a100@mail.ru",
            descripition: "wfwefw efw ef wefw ef wefwefw ef wef we fwe fwe fwewfwefw efw ef wefw ef wefwefw ef wef we fwe fwe fwewfwefw efw ef wefw ef wefwefw ef wef we fwe fwe fwewfwefw efw ef wefw ef wefwefw ef wef we fwe fwe fwewfwefw efw ef wefw ef wefwefw ef wef we fwe fwe fwe"
        },
        {domain_name: 'Super Application'},
    ];


    $scope.app = null;

    $scope.search_request_in_progress = false;
    $scope.openApp = function (app) {
        $scope.app = app;
    }

    $scope.downloadApp = function (app) {

    }

    function updateData() {
        $scope.app = $scope.apps[0];
    }

    updateData();

    $scope.path = function () {
        return "Path/dir/"
    }

    $scope.upload = function () {
        var input = document.createElement('input');
        input.type = 'file';
        input.onchange = function (e) {
            var file = e.target.files[0];
            var formData = new FormData();
            formData.append("token", 123);
            formData.append("userfile", file);
            var request = new XMLHttpRequest();
            request.open("POST", pathToRootDir + "/darknode/file_upload.php");
            request.send(formData);
        }
        input.click();
    }

    $scope.app_name = null
    $scope.create_app_message = null
    $scope.create_app_request_in_progress = null
    $scope.createApp = function () {
        $scope.create_app_message = null
        $scope.create_app_request_in_progress = true
        $http.post(pathToRootDir + "darknode/domain_get.php", {
            token: $scope.token,
            domain_name: $scope.app_name,
        }).then(function (response) {
            $scope.create_app_request_in_progress = false
            $scope.create_app_message = "domain is exist"
        }, function (response) {
            $http.post(pathToRootDir + "darknode/domain_set.php", {
                token: $scope.token,
                domain_name: $scope.app_name,
            }).then(function (response) {
                $scope.create_app_request_in_progress = false
                $scope.toggleCreateAppFragment = false
                $scope.app_name = null
            }, function (response) {
                $scope.create_app_request_in_progress = false
                $scope.create_app_message = response.data.message
            })
        })
    }
})