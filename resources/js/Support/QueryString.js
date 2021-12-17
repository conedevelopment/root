export default class QueryString
{
    /**
     * Create a new query string instance.
     *
     * @param  {string|null}  search
     * @param  {object}  defaults
     * @return {void}
     */
    constructor(search = null, defaults = {})
    {
        this.__defaults = JSON.parse(JSON.stringify(defaults));

        Object.entries(defaults).forEach((pair) => {
            this[pair[0]] = pair[1];
        });

        const params = new URLSearchParams(search);

        Array.from(params.entries()).forEach((pair) => {
            this.set(pair[0], pair[1]);
        });
    }

    /**
     * Set the value for the given key.
     *
     * @param  {string}  key
     * @param  {mixed}  value
     * @return {void}
     */
    set(key, value)
    {
        let target = this;
        const keys = key.replaceAll(']', '').replaceAll('[', '.').split('.').filter((key) => key !== '');

        while (keys.length - 1) {
            let n = keys.shift();

            if (! (n in target)) {
                target[n] = {};
            }

            target = target[n];
        }

        target[keys[0]] = value;
    }

    /**
     * Get the value of the given key.
     *
     * @param  {string}  key
     * @param  {mixed}  value
     * @return {mixed}
     */
    get(key, value = null)
    {
        return key.split('.').reduce((t, i) => (t && t[i]) || value, this);
    }

    /**
     * Reset the query string.
     *
     * @return {void}
     */
    reset()
    {
        Object.keys(this).filter((key) => {
            return ! ['__defaults'].includes(key);
        }).forEach((key) => {
            delete this[key];
        });

        Object.entries(this.__defaults).forEach((pair) => {
            this[pair[0]] = pair[1];
        });
    }

    /**
     * Clear the query string.
     *
     * @return {void}
     */
    clear()
    {
        this.__defaults = {};

        this.reset();
    }

    /**
     * Flatten the given target.
     *
     * @param  {object|null}  target
     * @param  {string|null}  prefix
     * @return {object}
     */
    flatten(target = null, prefix = null)
    {
        target = target || this;

        return Object.keys(target).filter((key) => {
            return ! ['__defaults'].includes(key)
                && ! [null, '', undefined].includes(target[key]);
        }).reduce((carry, key) => {
            const name = (prefix ? prefix + `[${key}]` : '') || key;

            return Object.assign(
                carry,
                typeof target[key] === 'object'
                    ? this.flatten(Object.assign({}, target[key]), name)
                    : { [name]: target[key] }
            );
        }, {});
    }

    /**
     * Get the string represenation of the query string.
     *
     * @return {string}
     */
    toString()
    {
        return Object.entries(this.flatten()).map((pair) => pair.join('=')).join('&');
    }

    /**
     * Get the string represenation of the query string.
     *
     * @return {URLSearchParams}
     */
    toURLSearchParams()
    {
        const params = new URLSearchParams();

        Object.entries(this.flatten()).forEach((pair) => {
            params.set(pair[0], pair[1]);
        });

        return params;
    }
}
