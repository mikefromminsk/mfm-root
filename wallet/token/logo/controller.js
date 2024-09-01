function openTokenSettings(domain, success) {
    window.$mdDialog.show({
        templateUrl: '/wallet/token/logo/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.DEBUG = DEBUG

            $scope.getLogo = function () {
                if ($scope.logo == null)
                    return "wallet/token/logo/img/" + domain + ".svg"

                let canvas = document.createElement("canvas")
                canvas.width = 32
                canvas.height = 32
                drawLogo(canvas.getContext("2d"), $scope.logo)
                return canvas.toDataURL()
            }

            $scope.generate = async function () {
                function getHash(t) {
                    let e = (new TextEncoder).encode(t);
                    return window.crypto.subtle.digest("SHA-1", e)
                }

                function hexString(t) {
                    return [...new Uint8Array(t)].map(t => t.toString(16).padStart(2, "0")).join("")
                }

                $scope.logo = hexString(await getHash(randomString(4)))
                $scope.$apply()
            }

            function drawLogo(context, logo) {
                function getColor(t) {
                    return "#" + t.slice(-6)
                }

                let wh = 32
                let wh5 = wh / 5
                context.clearRect(0, 0, wh, wh);
                let o = getColor(logo);
                for (let t = 0; t < 5; t++)
                    for (let n = 0; n < 5; n++) {
                        context.fillStyle = "transparent"
                        context.moveTo(t + wh5 * n, wh5 * (n + 1))
                        parseInt(logo.charAt(3 * t + (n > 2 ? 4 - n : n)), 16) % 2 && (context.fillStyle = o)
                        context.fillRect(wh5 * n, wh5 * t, wh5, wh5)
                        context.stroke()
                    }
                return context
            }

            function genSvg(logo) {
                if (logo == null) return ""
                var ctx = new C2S(32, 32);
                drawLogo(ctx, logo)
                return ctx.getSerializedSvg(); //true here, if you need to convert named to numbered entities.
            }

            $scope.saveLogo = function () {
                postContract("wallet", "store/api/upload_file.php", {
                    filename: "/wallet/token/logo/img/" + domain + ".svg",
                    file: genSvg($scope.logo),
                }, function () {
                    showSuccess("Logo uploaded successfully", function () {
                        $scope.close("success")
                    })
                })
            }

            function init() {
                getProfile(domain, function (response) {
                    $scope.profile = response
                    $scope.$apply()
                })
            }

            init()

        }
    }).then(function (result) {
        if (success)
            success(result)
    })
}