/**
 * Created by Apart-Filipe on 05/10/16.
 */

/**
 * Wraper class for handling json objects
 *
 */
class AData{

    constructor(aData) {
        this._data = new Object(aData);
    }

    getData(){
        return this._data;
    }

    getValues() {
        return _.map(this._data, function (value, index) {
            return value;
        });
    }

    /**
     * Check if key exists
     *
     * @param aKey
     * @returns {boolean}
     */
    has(aKey){
        return this._data.hasOwnProperty(aKey);
    }

    /**
     * Return value with key
     *
     * @param aKey
     * @returns {*}
     */
    get(aKey){
        return this.has(aKey) ? this._data[aKey] : '';
    }

    /**
     * Save a new value with the given key
     *
     * @param aKey
     * @param aValue
     */
    set(aKey, aValue){
        this._data[aKey] = aValue;
    }

    getObject(aKey){
        return new AData( this.has(aKey)?this.get(aKey):{});
    }



}

export default AData;