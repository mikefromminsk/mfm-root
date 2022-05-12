function track(params) {
    params.t = new Date().getTime()
    params.token = token
    var xhr = new XMLHttpRequest()
    xhr.open('POST', 'api/track.php', true)
    xhr.setRequestHeader('Content-Type', 'application/json')
    xhr.send(JSON.stringify(params))
}

function trackMarketViewed() {
    track({e: 'market_viewed'})
}

function trackMarketSelect(ticker) {
    track({e: 'market_select', p1: ticker})
}

function trackTradeViewed() {
    track({e: 'trade_viewed'})
}
