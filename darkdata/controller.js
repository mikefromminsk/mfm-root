controller("darkdata", function ($scope) {
    $scope.path = "DarkCoin/js/controlller"
    $scope.path_text = function(){
        return $scope.path.split("/").join(" / ");
    }

    $scope.files = [
        {file_name: "New Folder", file_size: 0},
        {file_name: "New Folder", file_size: 0},
        {file_name: "New Folder", file_size: 0},
        {file_name: "New Folder", file_size: 0},
        {file_name: "New Folder", file_size: 0},
        {file_name: "New Folder", file_size: 0},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
        {file_name: "Test.txt", file_size: 200},
    ]
})