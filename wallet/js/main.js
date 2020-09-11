
httpRequestBasePath = "api/";

const urlParams = new URLSearchParams(window.location.search)
if (urlParams.get("token") != null)
    store.set("token", urlParams.get("token"))

if (store.get("token") != null)
    httpRequestHeaders["token"] = store.get("token")

