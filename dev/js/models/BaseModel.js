import AData from '../tools/AData.js';

export default class BaseModel {

  constructor(aData = {}) {
    this._data = new AData(aData);
  }

  toString() {
    return JSON.stringify(this.toJSON());
  }

  toJSON() {
    return this._data.getData();
  }

  get(aKey, aReturnValueIfNull = null) {

    if (!this.has(aKey)) {
      return aReturnValueIfNull;
    }

    return this._data.get(aKey);
  }

  getInt(aKey) {
    return parseInt(this.get(aKey, 0), 10);
  }

  getFloat(aKey) {
    return parseFloat(this.get(aKey, 0));
  }

  getObject(aKey) {
    return this._data.getObject(aKey);
  }

  has(aKey) {
    return this._data.has(aKey);
  }

  hasValue(aKey) {
    return this._data.has(aKey) && this.get(aKey) !== '' && this.get(aKey) !== null;
  }

  set(aKey, aValue) {
    this._data.set(aKey, aValue);
  }

}