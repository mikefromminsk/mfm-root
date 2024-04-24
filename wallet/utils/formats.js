function addFormats($scope) {
    $scope.round = function (num, precision) {
        return +(Math.round(num + "e+" + precision) + "e-" + precision)
    }

    function shortNumber(number) {
        number = $scope.round(number, 2)
        var numberFormat = new Intl.NumberFormat()
        var result
        if (number >= 1000000)
            result = numberFormat.format($scope.round(number / 1000000, 2)) + "M"
        else if (number >= 1000)
            result = numberFormat.format($scope.round(number / 1000, 2)) + "K"
        else
            result = numberFormat.format($scope.round(number, 4))
        return result
    }

    $scope.formatSec = function (sec) {
        var d = new Date(sec * 1000);

        function format_two_digits(n) {
            return n < 10 ? '0' + n : n;
        }

        var hours = format_two_digits(d.getHours());
        var minutes = format_two_digits(d.getMinutes());
        return hours + ":" + minutes;
    }

    $scope.formatPrice = function (number) {
        if (number == null)
            number = 0;
        return "$" + shortNumber(number)
    }
    $scope.formatAmount = function (number, domain) {
        if (number == null)
            number = 0;
        var result = shortNumber(number)
        if (domain != null) {
            if (domain.length > 5)
                domain = domain.substr(0, 3)
            return result + " " + domain.toUpperCase()
        }
        return result
    }
    $scope.formatHash = function (hash) {
        if (hash == null) return ""
        return hash.substr(0, 5) + "..." + hash.substr(-5)
    }
    $scope.formatDomain = function (domain) {
        if (domain == null) return ""
        if (domain.length > 5)
            domain = domain.substr(0, 3)
        return domain
    }
    $scope.formatTicker = function (domain) {
        return (domain || "").toUpperCase()
    }
    $scope.formatPercent = function (number) {
        if (number == null)
            number = 0;
        if (number == 0) return "0%";
        number = $scope.round(number, 0)
        if (number < 0)
            return "-" + number + "%";
        else if (number > 0)
            return "+" + number + "%";
    }

    $scope.percentColor = function (number) {
        if (number === undefined) return ""
        if (number == 0)
            return {'gray400-text': true}
        if (number > 0)
            return {'green-text': true}
        if (number < 0)
            return {'red-text': true}
    }

    $scope.formatTime = function (number) {
        return new Date(number * 1000).toLocaleString()
    }

    $scope.formatTimeDiff = function (seconds) {
        function round(num, precision) {
            return +(Math.round(num + "e+" + precision) + "e-" + precision);
        }

        var diff = new Date().getTime() / 1000 - seconds
        var string = ""
        if (diff < 60)
            string = round(diff, 0) + "s"
        else if (diff < 60 * 60)
            string = round(diff / 60, 0) + "m"
        else if (diff < 60 * 60 * 24)
            string = round(diff / 60 / 60, 0) + "h"
        else if (diff < 60 * 60 * 24 * 30)
            string = round(diff / 60 / 60 / 24, 0) + "d"
        else if (diff < 60 * 60 * 24 * 30 * 12)
            string = round(diff / 60 / 60 / 24 / 30, 0) + "m"
        else
            string = round(diff / 60 / 60 / 24 / 30 / 12, 0) + "y"
        return string
    }

    function padTo2Digits(num) {
        return num.toString().padStart(2, '0');
    }

    $scope.formatDate = function (number) {
        if (number == "") return ""
        let date = new Date(number * 1000)
        if (new Date().toDateString() === date.toDateString())
            return "today"
        return [
            padTo2Digits(date.getDate()),
            padTo2Digits(date.getMonth() + 1),
            date.getFullYear(),
        ].join('.');
    }

    $scope.percentFormat = function (number) {
        return $scope.round(number, 0) + "%";
    }

    $scope.formatTicker = function (ticker) {
        return ticker.toUpperCase()
    }

    $scope.drawLogo = function (context, logo) {
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

    $scope.genLogo = function (logo) {
        if (logo == null) return ""
        let canvas = document.createElement("canvas")
        canvas.width = 32
        canvas.height = 32
        $scope.drawLogo(canvas.getContext("2d"), logo)
        return canvas.toDataURL()
    }

    $scope.genSvg = function (logo) {
        if (logo == null) return ""
        var ctx = new C2S(32, 32);
        $scope.drawLogo(ctx, logo)
        var mySerializedSVG = ctx.getSerializedSvg(); //true here, if you need to convert named to numbered entities.
        var svg = ctx.getSvg();
        return mySerializedSVG
    }


    $scope.back = function (result) {
        window.$mdBottomSheet.hide(result)
    }

    $scope.close = function (result) {
        window.$mdBottomSheet.hide(result)
        window.$mdDialog.hide(result)
    }

    $scope.random = function (from, to) {
        return Math.floor(Math.random() * to) + from;
    }

    $scope.groupByTimePeriod = function (obj) {
        var objPeriod = {};
        var oneDay = 24 * 60 * 60;
        for (var i = 0; i < obj.length; i++) {
            var d = new Date(obj[i]['time']);
            d = Math.floor(d.getTime() / oneDay);
            objPeriod[d] = objPeriod[d] || [];
            objPeriod[d].push(obj[i]);
        }
        var result = []
        for (day of Object.keys(objPeriod).sort().reverse()) {
            result.push({
                day: $scope.formatDate(day * 24 * 60 * 60),
                items: objPeriod[day].sort((a, b) => b['time'] - a['time']),
            })
        }
        return result;
    }


    $scope.isHideBalance = function () {
        return storage.getString(storageKeys.hideBalances) != ""
    }

    $scope.hideBalances = function () {
        storage.setString(storageKeys.hideBalances, "true")
        $scope.hideBalance = true
    }

    $scope.showBalances = function () {
        storage.setString(storageKeys.hideBalances, "")
        $scope.hideBalance = false
    }

    $scope.copyText = function (text) {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        // Avoid scrolling to bottom
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            var successful = document.execCommand('copy');
            var msg = successful ? 'successful' : 'unsuccessful';
            console.log('Fallback: Copying text command was ' + msg);
        } catch (err) {
            console.error('Fallback: Oops, unable to copy', err);
        }

        document.body.removeChild(textArea);
    }

    $scope.getUriParam = function (paramName) {
        var uri = window.location.search.substring(1)
        var params = new URLSearchParams(uri)
        return params.get(paramName)
    }

    $scope.cardBack = function (coin) {
        function hexEncode(str) {
            var result = '';
            for (var i = 0; i < str.length; i++) {
                result += str.charCodeAt(i).toString(16);
            }
            return result;
        }

        function getColor(t) {
            return "#" + hexEncode(md5(t)).substr(0, 6)
        }
        if (coin == null)
            return {}
        return {
            'background': 'linear-gradient(75deg, var(--bottom) 0%, ' + getColor(coin.domain) + ' 100%)',
        }
    }

    $scope.cardFront = function (coin) {
        return {
        }
    }

    $scope.getLogo = function (domain, width) {
        if (width == null)
            width = 32
        var img = {
            'width': width + 'px',
            'height': width + 'px',
            'min-width': width + 'px',
            'min-height': width + 'px',
        }
        if (domain != null){
            img['background-image'] =  "url('/" + domain + "/logo.svg')"
            img['background-size'] = '100% 100%'
        }
        return img
    }

    $scope.max = function (a, b) {
        return Math.max(a, b)
    }
}