function openMining(domain, success) {
    var stopVar = false
    window.$mdDialog.show({
        templateUrl: '/mining/mining/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            $scope.log = []
            var currentLastHash = "start"
            var timer
            function start() {
                post("/" + domain + "/api/mining/info.php", {}, function (data) {
                    if (currentLastHash != data.last_hash) {
                        currentLastHash = data.last_hash
                        mint(domain, data.last_hash || "", data.difficulty)
                    }
                    if (timer) clearTimeout(timer)
                    if (stopVar) return;
                    timer = setTimeout(start, 10 * 1000);
                })
            }

            function time() {
                var d = new Date();
                return d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds() + "." + d.getMilliseconds();
            }

            function addLog(message) {
                var newLog = [time() + ": " + message]
                for (var i = 0; i < $scope.log.length && i < 10; i++) {
                    newLog.push($scope.log[i])
                }
                $scope.log = newLog
                $scope.$apply()
            }

            async function mint(domain, last_hash, difficulty) {
                for (let i = 0; i < 10000000000; i++) {
                    if (last_hash != currentLastHash || stopVar) return;
                    if (md5(last_hash + domain + i).substring(0, difficulty) === "0".repeat(difficulty)) {
                        postContractWithGas(domain, "api/mining/mint.php", {
                            address: wallet.address(),
                            nonce: i,
                        }, function (data) {
                            /*if (data.minted != 0)*/ {
                                addLog("Minted " + data.minted + " coins")
                                setTimeout(function () {
                                    start()
                                }, 1000)
                            }
                            start()
                        }, function () {
                            currentLastHash = "start"
                            addLog("None " + i)
                            setTimeout(function () {
                                start()
                            }, 1000)
                        })
                        break;
                    }
                }
            }

            $scope.startMining = function () {
                openPin($scope.domain, function (pin) {
                    window.tempPin = pin
                    start($scope.domain)
                    $scope.inProgress = true
                })
            }

            $scope.stopMining = function () {
                stopVar = true
                $scope.inProgress = false
            }
        }
    }).then(function () {
        stopVar = true
        if (success)
            success()
    })
}

