function voteCreate($mdBottomSheet, path) {
    $mdBottomSheet.show({
        templateUrl: '/vote/create/index.html',
        locals: {
            path: path
        },
        controller: function ($scope, $mdBottomSheet, locals) {
            $scope.locals = locals
            $scope.path = locals.path
            $scope.answers = [{}]
            if (DEBUG) {
                $scope.path = "/upload/vote"
                $scope.question = "Do you want to upload this zip?"
                $scope.answers = []
                $scope.answers.push({
                    answer: "Yes",
                    value: "1412412412"
                })
                $scope.answers.push({
                    answer: "No",
                    value: "4134125122"
                })
            }
            $scope.voteCreate = function () {
                var params = {
                    path: $scope.path,
                    question: $scope.question,
                }
                for (var answer in $scope.answers) {
                    var index = $scope.answers.indexOf(answer)
                    params["answer" + index] = answer["answer"]
                    params["value" + index] = answer["value"]
                }
                postWithGas("/vote/api/create", params, function () {
                    $mdBottomSheet.hide()
                }, function (text) {
                    console.log(text)
                })
            }
            $scope.invalidate = function () {
                return false
            }
            $scope.addAnswer = function () {
                $scope.answers.push({})
            }
        }
    }).then(function () {
    })
}