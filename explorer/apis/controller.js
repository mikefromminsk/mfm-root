function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    $scope.openAccount = function () {
        openAccount()
    }

    $scope.search_domain = ''
    $scope.$watch('search_domain', function (newValue) {
        $scope.files = []
        if (newValue == null) return
        post("/wallet/api/search.php", {
            search_text: (newValue || ""),
        }, function (response) {
            $scope.searchCoins = response.result
            if ($scope.getUriParam("domain"))
                $scope.selectDomain($scope.searchCoins[0])
            $scope.$apply()
        })
    })
    if ($scope.getUriParam("domain")) {
        $scope.search_domain = $scope.getUriParam("domain")
    }


    $scope.selectDomain = function (coin) {
        var request = new XMLHttpRequest();
        $scope.selectedCoin = coin
        $scope.searchCoins = [coin]
        $scope.mode = 'readme'

        request.open('GET', "/" + coin.domain + "/readme.md", true);
        request.onload = function (fileLoadevent) {
            var html = new Markdown.Converter().makeHtml(fileLoadevent.target.responseText);
            document.getElementById('output').innerHTML = html;
        };
        request.send();

        function createFileStructure(paths) {
            const root = {};
            paths.forEach(path => {
                const parts = path.split('/');
                let current = root;
                parts.forEach((part, index) => {
                    if (!current[part]) {
                        current[part] = {};
                    }
                    if (index === parts.length - 1) {
                        current[part].isFile = true;
                    } else {
                        current = current[part];
                    }
                });
            });
            return root;
        }

        function findCommonPrefix(arr) {
            if (arr.length === 0) return '';
            let prefix = arr[0];
            for (let i = 1; i < arr.length; i++) {
                while (arr[i].indexOf(prefix) !== 0) {
                    prefix = prefix.substring(0, prefix.length - 1);
                    if (prefix === '') return '';
                }
            }
            return prefix;
        }

        getContracts(coin.domain, function (contracts) {
            var list = Object.values(contracts)
            $scope.path = findCommonPrefix(list)
            list = list.map(item => item.replace($scope.path, ""));
            $scope.files = createFileStructure(list)
            $scope.$apply()
        })
    }

    $scope.selectFile = function (file) {
        $scope.selectedFile = file
        post("/" + $scope.path + file, {help: 1}, function (response) {
            $scope.info = response
            $scope.mode = 'info'
            $scope.$apply()
        })
    }

    $scope.makeRequest = function () {
        var domain = $scope.selectedCoin.domain
        var path = $scope.path.substring((domain + "/").length);
        var params = {}
        Object.values($scope.info.params).forEach(param => {
            params[param.name] = param.value
        })
        postContract(domain, path + $scope.selectedFile, params,
            function (response) {
                new JsonEditor('#json-display', response)
            })
    }
}