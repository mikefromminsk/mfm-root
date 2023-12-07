function openLaunchDialog(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/launch/index.html",
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.domain = domain
            $scope.amount = 1000000
            if (DEBUG) {
                $scope.domain = "super"
            }
            $scope.back = function () {
                $mdBottomSheet.hide()
            }
            $scope.launch = function () {
                hasBalance(wallet.gas_domain, function () {
                    postWithGas("/wallet/api/launch.php", {
                        domain: $scope.domain,
                        address: wallet.username,
                        next_hash: wallet.calcStartHash($scope.domain + "/wallet"),
                        amount: 1000000,
                    }, function () {
                        storage.pushToArray(storageKeys.domains, $scope.domain)
                        showSuccessDialog("Token " + $scope.formatTicker($scope.domain) + " launched", success)
                    })
                })
            }
            setFocus("launch_token_name")


            async function generateLogo() {
                function hexString(t) {
                    return [...new Uint8Array(t)].map(t => t.toString(16).padStart(2, "0")).join("")
                }

                function getHash(t) {
                    let e = (new TextEncoder).encode(t);
                    return window.crypto.subtle.digest("SHA-1", e)
                }

                function getColor(t) {
                    return "#" + t.slice(-6)
                }
                let canvas = document.getElementById("logo_canvas")
                let e = hexString(await getHash($scope.domain))
                let wh = canvas.height / 5
                let r = canvas.getContext("2d"), o = getColor(e);
                for (let t = 0; t < 5; t++)
                    for (let n = 0; n < 5; n++) {
                        r.fillStyle = "white"
                        r.moveTo(t + wh * n, wh * (n + 1))
                        parseInt(e.charAt(3 * t + (n > 2 ? 4 - n : n)), 16) % 2 && (r.fillStyle = o)
                        r.fillRect(wh * n, wh * t, wh, wh)
                        r.stroke()
                    }
            }

            $scope.$watch(function (newValue) {
                if (newValue == null) return
                generateLogo()
            })

        }
    }).then(function () {
        if (success)
            success()
    })
}
