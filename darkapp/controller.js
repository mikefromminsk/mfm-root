controller("darkapp", function ($scope, $timeout, $q, $http) {

    $scope.files = [
        {file_name: "New Folder", file_size: 0},
        {file_name: "New Folder", file_size: 0},
        {file_name: "New Folder", file_size: 0},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
    ]


     $scope.apps = [
        {domain_name: 'Super Application', author: "x29a100@mail.ru", descripition: "wfwefw efw ef wefw ef wefwefw ef wef we fwe fwe fwewfwefw efw ef wefw ef wefwefw ef wef we fwe fwe fwewfwefw efw ef wefw ef wefwefw ef wef we fwe fwe fwewfwefw efw ef wefw ef wefwefw ef wef we fwe fwe fwewfwefw efw ef wefw ef wefwefw ef wef we fwe fwe fwe"},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
        {domain_name: 'Super Application'},
    ];


    $scope.toggleCreateAppFragment = true
    $scope.toggleExplorerFragment = true
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
        input.onchange = function(e){
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


    $scope.delete = function (file) {

    }

    $scope.uploadLogo = function(){

    }

})