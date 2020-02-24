controller("darkapp", function ($scope, $timeout, $q) {

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


    $scope.toggleExplorerFragment = false
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



})