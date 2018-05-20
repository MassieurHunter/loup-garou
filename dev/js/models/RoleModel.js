import BaseModel from './BaseModel';
import ABuilder from '../tools/ABuilder'

export default class RoleModel extends BaseModel {

    getName() {
        return this.get('name');
    }

    getDescription() {
        return this.get('description');
    }

    hasFirstAction() {
        return this.getInt('firstAction') === 1;
    }

    getModel(){
        return this.get('model');
    }

    getFirstActionName() {
        return this.get('firstActionName');
    }

    getFirstActionNbTargets() {
        return this.getInt('firstActionNbTargets');
    }

    getFirstActionTargetType() {
        return this.get('firstActionTargetType');
    }

    isFirstActionPassive() {
        return this.getInt('firstActionPassive') === 1;
    }

    hasSecondAction() {
        return this.getInt('secondAction') === 1;
    }

    getSecondActionName() {
        return this.get('secondActionNAme');
    }

    isSecondActionPassive() {
        return this.getInt('secondActionPassive') === 1;
    }

    getSecondActionNbTargets() {
        return this.getInt('secondActionNbTargets');
    }

    getSecondActionTargetType() {
        return this.get('secondActionTargetType');
    }

    isVillageois() {
        return this.getInt('villageois') === 1;
    }

    isLoup() {
        return this.getInt('loup') === 1;
    }

    isTanneur() {
        return this.getInt('tanneur') === 1;
    }


    display(){


        let html
    }


}