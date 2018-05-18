

/**
 * Helper for creation of DOM Elements
 *
 * @param aTagName
 * @param aAttributes
 * @returns {*|jQuery|HTMLElement}
 * @constructor
 */
function ABuilder(aTagName,aAttributes){

    let _element = jQuery('<'+aTagName+'/>');

    let content = arguments.length>2 ? arguments[2] : '' ;

    if($.isArray(content)){
        $.each(content,function(index,value){
            _element.append(value);
        });
    }else{
        _element.html(content);
    }

    $.each( aAttributes , function( key, value ) {
        _element.attr(key,value);
    });

    return _element;
}

export default ABuilder;