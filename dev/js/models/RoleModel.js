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

    getModel() {
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

    getBootstrapClass() {
        return this.get('bootstrapClass');
    }


    displayName() {
        let alert = new ABuilder('div', {
            'class': 'alert alert-' + this.getBootstrapClass(),
            'role': 'alert'
        }, this.getName());

        let col = new ABuilder('div', {
            'class': 'col-lg-8 offset-lg-2 col-md-8 offset-md-2 mt-1',
        }, alert);

        let row = new ABuilder('div', {
            'class': 'row started',
        }, col);

        $('.play-game').html(row);
    }


}