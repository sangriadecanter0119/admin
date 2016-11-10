$(function(){
    $("#formID").validationEngine();
 
 	$('input[type="text"],input[type="password"],textarea,select').focus(function() {
 			$(this).addClass("focusField");
 			/*
    		    if (this.value == this.defaultValue){ 
    		    	this.value = '';
				}
				if(this.value != this.defaultValue){
	    			this.select();
	    		}
	    	*/
    		});
  	$('input[type="text"],input[type="password"],textarea,select').blur(function() {
    			$(this).removeClass("focusField");
             /*
    			if ($.trim(this.value) == ''){
			    	this.value = (this.defaultValue ? this.defaultValue : '');
				}
			*/
    		});
});


 function checkHELLO(field, rules, i, options){
                if (field.val() != "HELLO") {
                    // this allows to use i18 for the error msgs
                    return options.allrules.validate2fields.alertText;
                }
 }