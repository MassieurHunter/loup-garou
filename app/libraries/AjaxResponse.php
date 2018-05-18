<?php

class AjaxResponse
{

    /**
     * @var array
     */
    private $actions = [];

    /**
     * @var null|string
     */
    private $formTarget = null;

    /**
     * @var int
     */
    private $errors = 0;

    /**
     * @return null
     */
    public function getFormTarget() {
        return $this->formTarget;
    }

    /**
     * @param $aFormTarget
     */
    public function setFormTarget($aFormTarget) {
        $this->formTarget = $aFormTarget;
    }

    /**
     * @param array $aData
     * @param int $aCode
     *
     * @return array
     */
    public function t($aData = [], $aCode = 200) {
        return $this->result(["result" => true, "data" => $aData], $aCode);
    }

    /**
     * @param $aDataArray
     * @param int $aCode
     *
     * @return array
     */
    private function result($aDataArray, $aCode = 200) {

        if ($this->hasActions()) {
            $aDataArray['actions'] = $this->getActions();
        }

        if (!is_null($this->formTarget)) {
            $aDataArray['formTarget'] = $this->formTarget;
        }

        return ['body' => $aDataArray, 'code' => $aCode];
    }

    /**
     * @return bool
     */
    public function hasActions() {
        return $this->getActionsCount() > 0;
    }

    /**
     * @return int
     */
    public function getActionsCount() {
        return count($this->actions);
    }

    /**
     * Return list of all actions queued
     *
     * @return array
     */
    public function getActions() {
        return $this->actions;
    }

    /**
     * @param $aError
     * @param int $aCode
     *
     * @return array
     */
    public function f($aError, $aCode = 400) {
        return $this->result(["result" => false, "error" => $aError, "code" => $aCode,], $aCode);
    }

    /**
     * Reload page
     */
    public function reload() {
        $this->addAction(["method" => "reload"]);
    }

    /**
     * @param $aAction
     */
    public function addAction($aAction) {
        array_push($this->actions, $aAction);
    }

    /**
     * @param $aURL
     * @param $timeout
     */
    public function redirect($aURL, $timeout = 0) {
        $this->load($aURL, $timeout);
    }

    /**
     * Goto given url
     *
     * @param string $aURL
     * @param string $timeout
     */
    public function load($aURL, $timeout = 0) {
        $this->addAction(["method" => "load", "location" => (substr($aURL, 0, 1) === '/' ? '' : '/') . $aURL, 'timeout' => $timeout]);
		}

    /**
     * @param $aURL
     * @param array $aParams
     */
    public function postRedirect($aURL, $aParams = []) {
        $this->addAction(["method" => "postRedirect", "location" => (substr($aURL, 0, 1) === '/' ? '' : '/') . $aURL, 'params' => $aParams]);
    }

    /**
     * @param $aSelector
     * @param array $aParams
     */
    public function linkUpdateParams($aSelector, $aParams = []) {
        $this->addAction(["method" => "linkUpdateParams", "selector" => $aSelector, 'params' => $aParams]);
    }

    /**
     * Delete html element with id
     *
     * @param string $aSelector
     */
    public function delete($aSelector) {
        $this->addAction(["method" => "delete", "selector" => $aSelector]);
    }


    /**
     * Show DOM element
     *
     * @param $aSelector
     */
    public function show($aSelector) {
        $this->addAction(["method" => "show", "selector" => $aSelector]);
    }

    /**
     * Hide DOM element
     *
     * @param $aSelector
     */
    public function hide($aSelector) {
        $this->addAction(["method" => "hide", "selector" => $aSelector]);
    }


    /**
     * Show Collapse DOM element
     *
     * @param $aSelector
     */
    public function showCollapse($aSelector) {
        $this->addAction(["method" => "showCollapse", "selector" => $aSelector]);
    }

    /**
     * Hide Collapse DOM element
     *
     * @param $aSelector
     */
    public function hideCollaspe($aSelector) {
        $this->addAction(["method" => "hideCollapse", "selector" => $aSelector]);
    }

    public function enableButton($aSelector) {
        $this->addAction(["method" => "enableButton", "selector" => $aSelector]);
    }

    public function disableButton($aSelector) {
        $this->addAction(["method" => "disableButton", "selector" => $aSelector]);
    }

    public function attr($aSelector, $aAttr, $aValue) {
        $this->addAction(["method" => "attr", "selector" => $aSelector, "attr" => $aAttr, "value" => $aValue]);
    }

    public function removeAttr($aSelector, $aAttr) {
        $this->addAction(["method" => "removeAttr", "selector" => $aSelector, "attr" => $aAttr]);
    }

    public function addClass($aSelector, $aClass) {
        $this->addAction(["method" => "class", "type" => "add", "selector" => $aSelector, "class" => $aClass]);
    }

    public function removeClass($aSelector, $aClass) {
        $this->addAction(["method" => "class", "type" => "remove", "selector" => $aSelector, "class" => $aClass]);
    }

    /**
     * Insert HTML into element
     *
     * @param string $aSelector
     * @param string $aContent
     */
    public function insert($aSelector, $aContent) {
        $this->addAction(["method" => "insert", "selector" => $aSelector, "content" => $aContent]);
    }

    /**
     * Append HTML into element
     *
     * @param string $aSelector
     * @param string $aContent
     */
    public function append($aSelector, $aContent) {
        $this->addAction(["method" => "append", "selector" => $aSelector, "content" => $aContent]);
    }

    /**
     * Update input value
     *
     * @param $aSelector
     * @param $aValue
     */
    public function val($aSelector, $aValue) {
        $this->addAction(["method" => "val", "selector" => $aSelector, "value" => $aValue]);
    }

    public function trigger($aSelector, $aType) {
        $this->addAction(["method" => "trigger", "selector" => $aSelector, "type" => $aType]);
    }

    public function toggleClass($aSelector, $aClass) {
        $this->addAction(["method" => "class", "type" => "toggle", "selector" => $aSelector, "class" => $aClass]);
    }

    /**
     * @param $aInformation
     */
    public function error($aInformation) {
        $this->errors++;
        $this->addAction(["method" => "error", "information" => $aInformation]);
    }

    /**
     * @param $aInformation
     */
    public function success($aInformation) {
        $this->addAction(["method" => "success", "information" => $aInformation]);
    }

    public function hasErrors() {
        return $this->errors > 0;
    }

    /**
     * @param $aInformation
     */
    public function alert($aInformation) {
        $this->addAction(["method" => "alert", "information" => $aInformation]);
    }

    /**
     * @param $aFunction - Name of the javascript function to call
     */
    public function call($aFunction) {

        $action = ["method" => "call", "name" => $aFunction];

        if (func_num_args() > 1) {

            $params = func_get_args();

            array_shift($params);

            $action["params"] = $params;
        }

        $this->addAction($action);
    }


}