{
    let getIndex = location.href.indexOf('?');
    if (getIndex == -1) {
        if (location.href.endsWith('.php')) location.href = location.href.slice(0, -4);
    } else {
        let get = location.href.slice(getIndex);
        let hrefNGet = location.href.slice(0, getIndex);

        if (hrefNGet.endsWith('.php')) location.href = hrefNGet.slice(0, -4) + get;
    }
}