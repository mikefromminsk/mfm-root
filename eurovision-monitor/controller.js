controller("eurovision-monitor", function ($scope, $http) {
    $scope.update = function () {
        $http.post('/eurovision-monitor/statistic.php', {
            lang: navigator.language
        }).then(function (response) {
            $scope.data = response.data;
            console.log(response);
        }, function () {

        });
    }

    $scope.update()

    $scope.views_round = function (views) {
        return views.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    }

    $scope.openYoutube = function (video_id) {
        window.location = "https://youtu.be/" + video_id
    }

    $scope.sortDataBy = function (field) {
        if ($scope.data != null) {
            let videos = [...$scope.data.videos];
            videos.sort(function (a, b) {
                let sumA = parseInt(a[field]);
                let sumB = parseInt(b[field]);
                if (sumA < sumB) return 1;
                if (sumA > sumB) return -1;
                return 0;
            });
            return videos;
        }
    }
})