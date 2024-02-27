function openMining(domain, success) {

    var difficulty = 1;

    function start(domain) {
        dataGet(domain + "/mining/last_hash", function (data) {
            mint(domain, data || "", difficulty)
        })
    }

    function mint(domain, last_hash, difficulty) {
        for (let i = 0; i < 10000000; i++) {
            if (md5(domain + last_hash + i).substring(0, difficulty) === "0".repeat(difficulty)) {
                postContractWithGas(domain, "api/mint.php", {
                        address: wallet.address(),
                        nonce: i,
                    },
                    function (data) {
                        alert(data.minted)
                        setTimeout(function () {
                            start(domain)
                        }, 100)
                    })
                break;
            }
        }
    }


    window.$mdDialog.show({
        templateUrl: 'mining/index.html',
        controller: function ($scope) {
            addFormats($scope)

            $scope.domain = domain

            $scope.startMining = function () {
                start($scope.domain)
            }

            $scope.init = function () {
                postContractWithGas($scope.domain, "api/init.php", {
                }, function (response) {
                    showSuccessDialog("Success " + response.success, success)
                })
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}

