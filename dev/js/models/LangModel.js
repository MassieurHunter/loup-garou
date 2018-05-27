import BaseModel from './BaseModel';

export default class LangModel extends BaseModel {

    getLine(line){
        return this.get(line, '');
    }

}