const debounce = (callback, delay = 300) => {
    let timeoutID = null;

    return function () {
        clearTimeout(timeoutID);

        const args = arguments;
        const context = this;

        timeoutID = setTimeout(function () {
            callback.apply(context, args);
        }, delay);
    };
};

const throttle = (callback, wait = 300) => {
    let timeoutID, lastTick;

    return function () {
        const args = arguments;
        const context = this;

        if (! lastTick) {
            callback.apply(context, args);
            lastTick = Date.now();
        } else {
            clearTimeout(timeoutID);
            timeoutID = setTimeout(function () {
                if ((Date.now() - lastTick) >= wait) {
                    callback.apply(context, args);
                    lastTick = Date.now();
                }
            }, wait - (Date.now() - lastTick));
        }
    };
};

export { debounce, throttle };
