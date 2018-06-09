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

    hasFirstAction() {
        return this.get('firstAction');
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
        return this.get('firstActionPassive');
    }

    hasSecondAction() {
        return this.get('secondAction');
    }

    isSecondActionNeedFailedFirst() {
        return this.get('secondActionNeedFailedFirst');
    }

    getSecondActionName() {
        return this.get('secondActionName');
    }

    setSecondActionName(secondActionName){
        this.set('secondActionName', secondActionName);
    }

    isSecondActionPassive() {
        return this.get('secondActionPassive');
    }

    setSecondActionPassive(isPassive) {
        return this.set('secondActionPassive', isPassive);
    }

    getSecondActionNbTargets() {
        return this.getInt('secondActionNbTargets');
    }

    setSecondActionNbTargets(nbTargets) {
        return this.set('secondActionNbTargets', nbTargets);
    }

    getSecondActionTargetType() {
        return this.get('secondActionTargetType');
    }

    setSecondActionTargetType(targetType) {
        return this.set('secondActionTargetType', targetType);
    }

    getType(){
        return this.get('type', '');
    }

    setType(type){
        this.set('type', type);
    }

    isVillageois() {
        return this.get('villageois');
    }

    isLoup() {
        return this.get('loup');
    }

    isTanneur() {
        return this.get('tanneur');
    }

    getBootstrapClass() {
        return this.get('bootstrapClass');
    }


    displayName() {
    	
    	let roleName = new ABuilder(
    		'h5',
			{},
			this.getName()
		);
    	
    	let roleDesc = new ABuilder(
    		'p',
			{},
			this.getDescription()
		);

		let alert = new ABuilder('div', {
				'class': 'alert alert-' + this.getBootstrapClass(),
				'role': 'alert'
			},
			[
				roleName,
				roleDesc
			]);

        $('.role-infos').append(alert);
    }


}